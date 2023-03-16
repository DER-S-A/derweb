class Rentabilidad extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");
    }

    /**
     * Permite generar la estructura de la pantalla de rentabilidad.
     */
    generateComponent(section) {
        this.getTemplate(new App().getUrlTemplate("rentabilidad"), html => {
            section.innerHTML = html;

            const arrayInputs = document.querySelectorAll('#container-rentabilidad .contenedor-inputs input');
            const miSession = new CacheUtils('derweb').get('sesion');
            const id_cliente = miSession.id_cliente;
            const inputsArrayValue = [miSession.rentabilidad_1, miSession.rentabilidad_2];
            this.llenarInputs(inputsArrayValue, arrayInputs);
            let url = new App().getUrlApi('catalogo-marcas-get');
            const marcaPromise = fetch(url, {method:'GET'}).then(response => response.json());
            url = new App().getUrlApi('catalogo-rubros-get');
            const rubroPromise = fetch(url, {method:'GET'}).then(response => response.json());
            url = new App().getUrlApi('catalogo-subrubros-get');
            const subrubroPromise = fetch(url, {method:'GET'}).then(response => response.json());

            
            // Esperar a que todas las promesas se resuelvan
            Promise.all([marcaPromise, rubroPromise, subrubroPromise])
            .then(([marcas, rubros, subrubros]) => {
                this.llenarBoxes(marcas, rubros, subrubros);
                this.confirmar(id_cliente, arrayInputs, inputsArrayValue, miSession);
                this.cerrar();
                
            })

        })
    }

    /**
     * Permite llenar todos los select q hay en la pantalla.
     */
    llenarBoxes(marcas, rubros, subrubros) {
        //const arrayQuerySelec = ['#container-rentabilidad #marcas', '#container-rentabilidad #rubros', '#container-rentabilidad #subrubros'];
        const arrayQuerySelec = [document.querySelector('#container-rentabilidad #marcas'), document.querySelector('#container-rentabilidad #rubros'), document.querySelector('#container-rentabilidad #subrubros')];
        this.__llenarBox(marcas, arrayQuerySelec[0]);
        this.__llenarBox(rubros, arrayQuerySelec[1]);
        this.__llenarBox(subrubros, arrayQuerySelec[2]);
        this.__llenarBoxFiltrado(arrayQuerySelec, marcas, rubros, subrubros);
    }

    /**
     * Permite llenar todos los options q hay en la pantalla.
     */
    __llenarBox(listado, query_selector, eleccion = false) {
        //let objSelector = document.querySelector(query_selector);
        let objSelector = query_selector;
        if(eleccion == 'TODAS') objSelector.innerHTML = '<option value="TODAS">Todas</option>';
        listado.forEach(lista => {
            let objOption = document.createElement('option');
            objOption.value = lista.id;
            objOption.innerText = lista.descripcion;
            objSelector.append(objOption);
            /// Pongo la seleccion guardada previamente como 1ra opcion
            if(eleccion) {
                for(let i=0;i<objSelector.options.length;i++) {
                    if(objSelector.options[i].value == eleccion) {
                        objSelector.selectedIndex = i;
                    break;
                    }
                }
            }
        })
    }

    /**
     * Permite confirmar las rentabilidades editadas en la pantalla.
     */
    confirmar(id, arrayInputs, inputsValueSession, session) {
        const botonConfirmar = document.querySelector('#container-rentabilidad #Aceptar');
        botonConfirmar.addEventListener('click', () => {
            
            let arrayRenta = [];
            arrayInputs.forEach((inp, index) => {
                if(inp.value != '') {
                    arrayRenta.push(inp.value);
                } else {
                    arrayRenta.push(inputsValueSession[index]);
                }
            })
            console.log(arrayRenta);
            const parametros = `id=${id}&renta=${JSON.stringify(arrayRenta)}`;
            
            let url = new App().getUrlApi('rentabilidad');
            console.log(url + '?'+ parametros);
            new APIs().call(url, parametros, 'PUT', respuesta => {
                if(respuesta.result_code == 'success') {
                    for(let i=0;i<arrayRenta.length;i++){
                        let clave = 'rentabilidad_' + (i+1);
                        session[clave] = arrayRenta[i];
                    }
                    new CacheUtils('derweb').set('sesion', session);
                }
                swal(respuesta.result_titulo, respuesta.result_message, respuesta.result_code)
                .then(response => {
                    location.href = './main-clientes.php';
                })
            })
        });

    }

    /**
     * Permite cerrar la pantalla.
     */
    cerrar() {
        const botonCerrar = document.querySelector('#container-rentabilidad #Cerrar');
        botonCerrar.addEventListener('click', () => {
            location.href = './main-clientes.php';
        })
    }

    /**
     * Permite llenar los inputs con las rentabilidades que tiene el cliente.
     */
    llenarInputs(inputsArrayValue, arrayInputs) {
        arrayInputs.forEach((inp, index) => {
            inp.value = inputsArrayValue[index];
        })
    }

    __llenarBoxFiltrado(query_selector, marcas, rubros, subrubros) {
        //const objMarcas = document.querySelector(query_selector[0]);
        //const objRubros = document.querySelector(query_selector[1]);
        //const objSubrubros = document.querySelector(query_selector[2]);
        const objMarcas = query_selector[0];
        const objRubros = query_selector[1];
        const objSubrubros = query_selector[2];
        
        objMarcas.addEventListener('change', () => {
            let consulta = this.__crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, 'marcas');
            this.__filtrarBoxesParaMarcas(consulta, objMarcas, objRubros, objSubrubros, rubros, subrubros, marcas);
            
        });
        objRubros.addEventListener('change', () => {
            let consulta = this.__crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, 'rubros');
            console.log(consulta)
            this.__filtrarBoxesParaRubros(consulta, objMarcas, objRubros, objSubrubros, marcas, subrubros);
            
        });
        objSubrubros.addEventListener('change', () => {
            let consulta = this.__crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, 'subrubros');
            this.__filtrarBoxesParaSubrubros(consulta, objMarcas, objRubros, objSubrubros, marcas, rubros);
        })
    }

    __filtrarBoxesParaMarcas(consulta, objMarcas, objRubros, objSubrubros, rubros, subrubros, marcas) {
        const url = new App().getUrlApi('boxesFiltrados');
        let parametros = `where=${consulta}`;
        console.log(parametros); 
        new APIs().call(url, parametros, 'POST', xdatos => {
            console.log(url + parametros);
            console.log(xdatos);
            let listado = filtrarNumerosArray(xdatos,'id_rubro');
            let rubroSeleccionado = objRubros.value;
            objRubros.innerHTML = '';
            this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);

            listado = filtrarNumerosArray(xdatos,'id_subrubro');
            let subrubroSeleccionado = objSubrubros.value;
            objSubrubros.innerHTML = '';
            console.log('1')
            this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);
            
            parametros = `where=id_marca=${objMarcas.value}`;
            new APIs().call(url, parametros, 'POST', xdatos => {
                console.log(xdatos);
                if(objSubrubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_rubro');
                    let rubroSeleccionado = objRubros.value;
                    objRubros.innerHTML = '';
                    console.log(listado);
                    console.log('2')
                    this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);
                }
                if(objRubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_subrubro');
                    let subrubroSeleccionado = objSubrubros.value;
                    objSubrubros.innerHTML = '';
                    console.log(listado);
                    console.log('2');
                    this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);
                }

                //parametros = `where=${consulta}`;
                if(objSubrubros.value != 'TODAS' && objRubros.value != 'TODAS' && objMarcas.value != 'TODAS') {                
                    parametros = `where=id_marca=${objMarcas.value} AND id_rubro=${objRubros.value} AND id_subrubro=${objSubrubros.value}`;
                    console.log(parametros)
                    new APIs().call(url, parametros, 'POST', xdatos => {
                        console.log(xdatos);

                        let listado = filtrarNumerosArray(xdatos, 'id_marca');
                        let marcaSeleccionado = objMarcas.value;
                        objMarcas.innerHTML = ''; 
                        console.log(listado);
                        console.log('3');
                        console.log(marcas);
                        this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_rubro');
                        let rubroSeleccionado = objRubros.value;
                        objRubros.innerHTML = '';
                        console.log(listado);
                        console.log('3');
                        this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_subrubro');
                        let subrubroSeleccionado = objSubrubros.value;
                        objSubrubros.innerHTML = '';
                        console.log(listado);
                        console.log('3');
                        this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);    
                    });
                }
            });
        });
        
    }
    


    __filtrarBoxesParaRubros(consulta, objMarcas, objRubros, objSubrubros, marcas, subrubros) {
        const url = new App().getUrlApi('boxesFiltrados');
        let parametros = `where=${consulta}`;
        
        new APIs().call(url, parametros, 'POST', xdatos => {
            console.log(url + parametros);
            console.log(xdatos);
            let listado = filtrarNumerosArray(xdatos,'id_marca');
            let marcaSeleccionada = objMarcas.value;
            objMarcas.innerHTML = '';
            console.log(listado);
            this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
            listado = filtrarNumerosArray(xdatos,'id_subrubro');
            let subrubroSeleccionada = objSubrubros.value;
            objSubrubros.innerHtml = '';
            console.log(listado);
            this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionada);

            parametros = `where=id_rubro=${objRubros.value}`; 
        
            new APIs().call(url, parametros, 'POST', xdatos => {
                console.log(url + parametros);
                console.log(xdatos);
                if(objSubrubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_marca');
                    let marcaSeleccionada = objMarcas.value;
                    objMarcas.innerHTML = '';
                    console.log(listado);
                    this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
                }
                if(objMarcas.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_subrubro');
                    let subrubroSeleccionada = objSubrubros.value;
                    objSubrubros.innerHTML = '';
                    console.log(listado);
                    this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionada);
                }
                
            });
        });
        
    }

    __filtrarBoxesParaSubrubros(consulta, objMarcas, objRubros, objSubrubros, marcas, rubros) {
        const url = new App().getUrlApi('boxesFiltrados');
        let parametros = `where=${consulta}`;

        new APIs().call(url, parametros, 'POST', xdatos => {
            console.log(url + parametros);
            console.log(xdatos);
            let listado = filtrarNumerosArray(xdatos,'id_marca');
            let marcaSeleccionado = objMarcas.value;
            objMarcas.innerHtml = '';
            this.__llenarBox(filtrarArray(listado,marcas), objMarcas, marcaSeleccionado);

            parametros = `where=id_subrubro=${objSubrubros.value}`;
            console.log(xdatos);
            listado = filtrarNumerosArray(xdatos,'id_rubro');
            console.log(listado);
            let rubroSeleccionada = objRubros.value;
            objRubros.innerHTML = '';
            this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionada);

            parametros = `where=id_subrubro=${objSubrubros.value}`; 
        
            new APIs().call(url, parametros, 'POST', xdatos => {
                console.log(url + parametros);
                console.log(xdatos);
                let listado = filtrarNumerosArray(xdatos,'id_marca');
                let marcaSeleccionada = objMarcas.value;
                objMarcas.innerHTML = '';
                console.log(listado);
                this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
            });
        });
        
        // new APIs().call(url, parametros, 'POST', xdatos => {
        //     console.log(xdatos);
        //     /*let listado = filtrarNumerosArray(xdatos,'id_marca');
        //     let marcaSeleccionada = objMarcas.value;
        //     objMarcas.innerHTML = '';
        //     this.__llenarBox(filtrarArray(listado,marcas), objMarcas, marcaSeleccionada);*/
        //     let listado = filtrarNumerosArray(xdatos,'id_rubro');
        //     let rubroSeleccionada = objRubros.value;
        //     objRubros.innerHTML = '';
        //     console.log(listado);
        //     this.__llenarBox(filtrarArray(listado,rubros), objRubros, rubroSeleccionada);
        // });
        // parametros = `where=id_subrubro=${objSubrubros.value}`;
        // new APIs().call(url, parametros, 'POST', xdatos => {
        //     console.log(xdatos);
        //     let listado = filtrarNumerosArray(xdatos,'id_marca');
        //     let marcaSeleccionada = objMarcas.value;
        //     objMarcas.innerHTML = '<option value="TODAS">Todas</option>';
        //     console.log(listado);
        //     this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
        // });

        
    }

    __crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, opcion) {
        let consulta = '';
        switch(opcion) {
            case 'marcas': {
                consulta += `id_marca=${objMarcas.value}`;
                if(objRubros.value != 'TODAS') consulta += ` AND id_rubro=${objRubros.value}`;
                if(objSubrubros.value != 'TODAS') consulta += ` AND id_subrubro=${objSubrubros.value}`;
            }
            break;
            case 'rubros': {
                consulta += `id_rubro=${objRubros.value}`;
                if(objMarcas.value != 'TODAS') consulta += ` AND id_marca=${objMarcas.value}`;
                if(objSubrubros.value != 'TODAS') consulta += ` AND id_subrubro=${objSubrubros.value}`;
            }
            break;
            case 'subrubros': {
                consulta += `id_subrubro=${objSubrubros.value}`;
                if(objMarcas.value != 'TODAS') consulta += ` AND id_marca=${objMarcas.value}`;
                if(objRubros.value != 'TODAS') consulta += ` AND id_rubro=${objRubros.value}`;
            }
            break;
        }
        
        return consulta;
    }

}

function generarRentabilidad() {
    const section = document.querySelector('#app-container');
    new Rentabilidad().generateComponent(section);
}

function filtrarNumerosArray(listado, clave) {
    let lista = listado.map(lista => lista[clave]);
    lista = lista.filter((item, index) => lista.indexOf(item) === index);
    let resultado = [];
    //let objeto = {};
    
    for(let i=0;i<lista.length;i++) {
        //let objeto = {};
        //objeto['id'] = lista[i];
        resultado.push(lista[i]);
    }
    return resultado;
}

function filtrarArray(arrayClave, arrayFiltrar) {
    let aux = [];
    console.log(arrayFiltrar)
    console.log(arrayClave)
    for(let i=0;i<arrayClave.length;i++) {
        aux.push(arrayFiltrar.filter(arr => arr.id == arrayClave[i])[0]);
    }
    return aux;
}