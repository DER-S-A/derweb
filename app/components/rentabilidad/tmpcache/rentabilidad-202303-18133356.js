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
                const arrayQuerySelec = [document.querySelector('#container-rentabilidad #marcas'), document.querySelector('#container-rentabilidad #rubros'), document.querySelector('#container-rentabilidad #subrubros')];
                this.llenarBoxes(marcas, rubros, subrubros, arrayQuerySelec);
                this.__llenarBoxFiltrado(arrayQuerySelec, marcas, rubros, subrubros);
                this.cargarTabla();
                //dataTableRenta.row.add(['Omer', 'Todas', 'Todas', '10.00', '0']);
                //dataTableRenta.draw();
                this.confirmar(id_cliente, arrayInputs, inputsArrayValue, miSession);
                this.cerrar();
                
            })

        })
    }

    /**
     * Permite llenar todos los select q hay en la pantalla.
     */
    llenarBoxes(marcas, rubros, subrubros, arrayQuerySelec) {console.log(arrayQuerySelec)
        //const arrayQuerySelec = ['#container-rentabilidad #marcas', '#container-rentabilidad #rubros', '#container-rentabilidad #subrubros'];
        //const arrayQuerySelec = [document.querySelector('#container-rentabilidad #marcas'), document.querySelector('#container-rentabilidad #rubros'), document.querySelector('#container-rentabilidad #subrubros')];
        this.__llenarBox(marcas, arrayQuerySelec[0]);
        this.__llenarBox(rubros, arrayQuerySelec[1]);
        this.__llenarBox(subrubros, arrayQuerySelec[2]);
        //this.__llenarBoxFiltrado(arrayQuerySelec, marcas, rubros, subrubros);
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
        const objMarcas = query_selector[0];
        const objRubros = query_selector[1];
        const objSubrubros = query_selector[2];
        
        objMarcas.addEventListener('change', () => {
            let consulta = this.__crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, 'marcas');
            this.__filtrarBoxesParaMarcas(consulta, objMarcas, objRubros, objSubrubros, rubros, subrubros, marcas);
            
        });
        objRubros.addEventListener('change', () => {
            let consulta = this.__crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, 'rubros');
            this.__filtrarBoxesParaRubros(consulta, objMarcas, objRubros, objSubrubros, marcas, subrubros, rubros);
            
        });
        objSubrubros.addEventListener('change', () => {
            let consulta = this.__crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, 'subrubros');
            this.__filtrarBoxesParaSubrubros(consulta, objMarcas, objRubros, objSubrubros, marcas, rubros, subrubros);
        })
    }

    __filtrarBoxesParaMarcas(consulta, objMarcas, objRubros, objSubrubros, rubros, subrubros, marcas) {
        const url = new App().getUrlApi('boxesFiltrados');

        if(objMarcas.value == 'TODAS' && objRubros.value == 'TODAS' && objSubrubros.value == 'TODAS') {

            this.__llenarBox(marcas, objMarcas, objMarcas.value);
            this.__llenarBox(rubros, objRubros, objRubros.value);
            this.__llenarBox(subrubros, objSubrubros, objSubrubros.value);
            return;
        }

        if(objMarcas.value == 'TODAS') {
            if(objRubros.value != 'TODAS') {
                let parametros = `where=id_rubro=${objRubros.value}`;
                let rubroSeleccionada = objRubros.value;
                objRubros.innerHTML = '<option value="TODAS">Todas</option>';
                this.__llenarBox(rubros, objRubros, rubroSeleccionada);
                
                new APIs().call(url, parametros, 'POST', xdatos => {
                    let listado = filtrarNumerosArray(xdatos,'id_subrubro');
                    let subrubroSeleccionado = objSubrubros.value;
                    objSubrubros.innerHTML = '';
                    this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);
                });
            } else {
                let parametros = `where=id_subrubro=${objSubrubros.value}`;
                let subrubroSeleccionada = objSubrubros.value;
                objSubrubros.innerHTML = '<option value="TODAS">Todas</option>';
                this.__llenarBox(subrubros, objSubrubros, subrubroSeleccionada);
                new APIs().call(url, parametros, 'POST', xdatos => {
                    let listado = filtrarNumerosArray(xdatos,'id_rubro');
                    let rubroSeleccionado = objRubros.value;
                    objRubros.innerHTML = '';
                    this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);
                });
            }
            return;
        }
        
        let parametros = `where=${consulta}`;
        console.log(parametros); 
        new APIs().call(url, parametros, 'POST', xdatos => {
            console.log(url + parametros);
            let listado = filtrarNumerosArray(xdatos,'id_rubro');
            let rubroSeleccionado = objRubros.value;
            objRubros.innerHTML = '';
            this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);

            listado = filtrarNumerosArray(xdatos,'id_subrubro');
            let subrubroSeleccionado = objSubrubros.value;
            objSubrubros.innerHTML = '';
            this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);
            
            parametros = `where=id_marca=${objMarcas.value}`;
            console.log(parametros);
            new APIs().call(url, parametros, 'POST', xdatos => {
                if(objSubrubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_rubro');
                    let rubroSeleccionado = objRubros.value;
                    objRubros.innerHTML = '<option value="TODAS">Todas</option>';
                    this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);
                }
                if(objRubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_subrubro');
                    let subrubroSeleccionado = objSubrubros.value;
                    objSubrubros.innerHTML = '<option value="TODAS">Todas</option>';
                    this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);
                }

                if(objSubrubros.value != 'TODAS' && objRubros.value != 'TODAS' && objMarcas.value != 'TODAS') {                
                    parametros = `where=id_marca=${objMarcas.value} AND id_rubro=${objRubros.value} AND id_subrubro=${objSubrubros.value}`;

                    new APIs().call(url, parametros, 'POST', xdatos => {

                        let listado = filtrarNumerosArray(xdatos, 'id_marca');
                        let marcaSeleccionado = objMarcas.value;
                        objMarcas.innerHTML = ''; 
                        this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_rubro');
                        let rubroSeleccionado = objRubros.value;
                        objRubros.innerHTML = '';
                        this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_subrubro');
                        let subrubroSeleccionado = objSubrubros.value;
                        objSubrubros.innerHTML = '';
                        this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);    
                    });
                }
            });
        });
        
    }
    


    __filtrarBoxesParaRubros(consulta, objMarcas, objRubros, objSubrubros, marcas, subrubros, rubros) {
        const url = new App().getUrlApi('boxesFiltrados');

        if(objMarcas.value == 'TODAS' && objRubros.value == 'TODAS' && objSubrubros.value == 'TODAS') {
            this.__llenarBox(marcas, objMarcas, objMarcas.value);
            this.__llenarBox(rubros, objRubros, objRubros.value);
            this.__llenarBox(subrubros, objSubrubros, objSubrubros.value);
            return;
        }

        if(objRubros.value == 'TODAS') {
            if(objMarcas.value != 'TODAS') {
                let parametros = `where=id_marca=${objMarcas.value}`;
                let marcaSeleccionada = objMarcas.value;
                objMarcas.innerHTML = '<option value="TODAS">Todas</option>';
                this.__llenarBox(marcas, objMarcas, marcaSeleccionada);
                new APIs().call(url, parametros, 'POST', xdatos => {
                    let listado = filtrarNumerosArray(xdatos,'id_subrubro');
                    let subrubroSeleccionado = objSubrubros.value;
                    objSubrubros.innerHTML = '';
                    this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);
                });
            } else {
                let parametros = `where=id_subrubro=${objSubrubros.value}`;
                let subrubroSeleccionada = objSubrubros.value;
                objSubrubros.innerHTML = '<option value="TODAS">Todas</option>';
                this.__llenarBox(subrubros, objSubrubros, subrubroSeleccionada);
                
                new APIs().call(url, parametros, 'POST', xdatos => {
                    let listado = filtrarNumerosArray(xdatos,'id_marca');
                    let marcaSeleccionado = objMarcas.value;
                    objMarcas.innerHTML = '';
                    this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionado);
                });
            }
            console.log('return');
            return;
        }

        let parametros = `where=${consulta}`;
        
        new APIs().call(url, parametros, 'POST', xdatos => {
            console.log(url + parametros);
            let listado = filtrarNumerosArray(xdatos,'id_marca');
            let marcaSeleccionada = objMarcas.value;
            objMarcas.innerHTML = '';
            this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
            listado = filtrarNumerosArray(xdatos,'id_subrubro');
            let subrubroSeleccionada = objSubrubros.value;
            objSubrubros.innerHtml = '<option value="TODAS">Todas</option>';
            this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionada);

            parametros = `where=id_rubro=${objRubros.value}`; 
        
            new APIs().call(url, parametros, 'POST', xdatos => {
                if(objSubrubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_marca');
                    let marcaSeleccionada = objMarcas.value;
                    objMarcas.innerHTML = '<option value="TODAS">Todas</option>';
                    this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
                }
                if(objMarcas.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_subrubro');
                    let subrubroSeleccionada = objSubrubros.value;
                    objSubrubros.innerHTML = '<option value="TODAS">Todas</option>';
                    this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionada);
                }

                if(objSubrubros.value != 'TODAS' && objMarcas.value != 'TODAS' && objMarcas.value != 'TODAS') {                
                    parametros = `where=id_marca=${objMarcas.value} AND id_rubro=${objRubros.value} AND id_subrubro=${objSubrubros.value}`;
                    new APIs().call(url, parametros, 'POST', xdatos => {

                        let listado = filtrarNumerosArray(xdatos, 'id_marca');
                        let marcaSeleccionado = objMarcas.value;
                        objMarcas.innerHTML = ''; 
                        this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_rubro');
                        let rubroSeleccionado = objRubros.value;
                        objRubros.innerHTML = '';
                        this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_subrubro');
                        let subrubroSeleccionado = objSubrubros.value;
                        objSubrubros.innerHTML = '';
                        this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);    
                    });
                }
                
            });
        });
        
    }

    __filtrarBoxesParaSubrubros(consulta, objMarcas, objRubros, objSubrubros, marcas, rubros, subrubros) {
        const url = new App().getUrlApi('boxesFiltrados');
        
        if(objMarcas.value == 'TODAS' && objRubros.value == 'TODAS' && objSubrubros.value == 'TODAS') {
            this.__llenarBox(marcas, objMarcas, objMarcas.value);
            this.__llenarBox(rubros, objRubros, objRubros.value);
            this.__llenarBox(subrubros, objSubrubros, objSubrubros.value);
            return;
        }
        
        if(objSubrubros.value == 'TODAS') {
            if(objRubros.value != 'TODAS') {
                let parametros = `where=id_rubro=${objRubros.value}`;
                let rubroSeleccionada = objRubros.value;
                objRubros.innerHTML = '<option value="TODAS">Todas</option>';
                this.__llenarBox(rubros, objRubros, rubroSeleccionada);
                
                new APIs().call(url, parametros, 'POST', xdatos => {
                    let listado = filtrarNumerosArray(xdatos,'id_marca');
                    let marcaSeleccionado = objMarcas.value;
                    objMarcas.innerHTML = '';
                    this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionado);
                });
            } else {
                let parametros = `where=id_marca=${objMarcas.value}`;
                let marcaSeleccionada = objMarcas.value;
                objMarcas.innerHTML = '<option value="TODAS">Todas</option>'
                this.__llenarBox(marcas, objMarcas, marcaSeleccionada);
                
                new APIs().call(url, parametros, 'POST', xdatos => {
                    let listado = filtrarNumerosArray(xdatos,'id_rubro');
                    let rubroSeleccionado = objRubros.value;
                    objRubros.innerHTML = '';
                    this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);
                });
            }
            console.log('return');
            return;
        }
        
        let parametros = `where=${consulta}`;

        new APIs().call(url, parametros, 'POST', xdatos => {

            let listado = filtrarNumerosArray(xdatos,'id_marca');
            let marcaSeleccionado = objMarcas.value;
            objMarcas.innerHtml = '';
            this.__llenarBox(filtrarArray(listado,marcas), objMarcas, marcaSeleccionado);

            parametros = `where=id_subrubro=${objSubrubros.value}`;
            listado = filtrarNumerosArray(xdatos,'id_rubro');
            let rubroSeleccionada = objRubros.value;
            objRubros.innerHTML = '';
            this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionada);

            parametros = `where=id_subrubro=${objSubrubros.value}`; 
        
            new APIs().call(url, parametros, 'POST', xdatos => {

                if(objRubros.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_marca');
                    let marcaSeleccionada = objMarcas.value;
                    objMarcas.innerHTML = '<option value="TODAS">Todas</option>';
                    console.log(listado);
                    this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionada);
                }
                if(objMarcas.value == 'TODAS') {
                    let listado = filtrarNumerosArray(xdatos,'id_rubro');
                    let rubroSeleccionado = objRubros.value;
                    objRubros.innerHTML = '<option value="TODAS">Todas</option>';
                    this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);
                }

                if(objSubrubros.value != 'TODAS' && objRubros.value != 'TODAS' && objMarcas.value != 'TODAS') {                
                    parametros = `where=id_marca=${objMarcas.value} AND id_rubro=${objRubros.value} AND id_subrubro=${objSubrubros.value}`;
                    new APIs().call(url, parametros, 'POST', xdatos => {

                        let listado = filtrarNumerosArray(xdatos, 'id_marca');
                        let marcaSeleccionado = objMarcas.value;
                        objMarcas.innerHTML = ''; 
                        this.__llenarBox(filtrarArray(listado, marcas), objMarcas, marcaSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_rubro');
                        let rubroSeleccionado = objRubros.value;
                        objRubros.innerHTML = '';
                        this.__llenarBox(filtrarArray(listado, rubros), objRubros, rubroSeleccionado);

                        listado = filtrarNumerosArray(xdatos,'id_subrubro');
                        let subrubroSeleccionado = objSubrubros.value;
                        objSubrubros.innerHTML = '';
                        this.__llenarBox(filtrarArray(listado, subrubros), objSubrubros, subrubroSeleccionado);    
                    });
                }
            });
        });
    }

    __crearConsultaParaFiltro(objMarcas, objRubros, objSubrubros, opcion) {
        let consulta = '';
        switch(opcion) {
            case 'marcas': {
                if(objMarcas.value != 'TODAS') {
                    consulta += `id_marca=${objMarcas.value}`;
                    if(objRubros.value != 'TODAS') consulta += ` AND id_rubro=${objRubros.value}`;
                    if(objSubrubros.value != 'TODAS') consulta += ` AND id_subrubro=${objSubrubros.value}`;
                } else {
                    if(objRubros.value != 'TODAS' && objSubrubros.value != 'TODAS') {
                        consulta += `id_rubro=${objRubros.value} AND id_subrubro=${objSubrubros.value}`;
                    } else {
                        if(objRubros.value != 'TODAS') {
                            consulta += `id_rubro=${objRubros.value}`;
                        } else consulta += `id_subrubro=${objSubrubros.value}`;
                    }
                }
                
                
                
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

    cargarTabla() {
        let dataTableRenta = $("#contenedor-tabla-renta").DataTable({
            searching: true,
            paging: true,
            responsive: true,
            scrollY: 260
        });
        let botonAgregar = document.querySelector('#container-rentabilidad #buttonR');
        let selectMarcas = document.querySelector('#container-rentabilidad #marcas');
        let selectRubros = document.querySelector('#container-rentabilidad #rubros');
        let selectSubrubros = document.querySelector('#container-rentabilidad #subrubros');
        let margen1 = document.querySelector('#container-rentabilidad .contenedor-mgEsp input[name="margen1"]');
        let margen2 = document.querySelector('#container-rentabilidad .contenedor-mgEsp input[name="margen2"]');
        let objTabla = [];        

        botonAgregar.addEventListener('click', () => {
            let objTemporal = {id:'', marca:selectMarcas.value, rubro:selectRubros.value, subrubro:selectSubrubros.value, margen1: margen1.value, margen2: margen2.value};
            this.__validarRepetido(objTabla, objTemporal);
            this.__pintarTabla(dataTableRenta, objTabla, selectMarcas, selectRubros, selectSubrubros, margen1, margen2);
            
        })
    }

    __pintarTabla(dataTableRenta, objTabla, selectMarcas, selectRubros, selectSubrubros, margen1, margen2) {
        objTabla.push({id:'', marca:selectMarcas.value, rubro:selectRubros.value, subrubro:selectSubrubros.value, margen1: margen1.value, margen2: margen2.value});
        console.log(objTabla);
        dataTableRenta.row.add([selectMarcas.options[selectMarcas.selectedIndex].text, selectRubros.options[selectRubros.selectedIndex].text, selectSubrubros.options[selectSubrubros.selectedIndex].text, margen1.value, margen2.value]);
        dataTableRenta.draw();
    }

    __validarRepetido(objTabla, objTemporal) {
        if(objTabla.length > 0){
            let resultado = objTabla.find(tabla => tabla.marca == objTemporal.marca && tabla.rubro == objTemporal.rubro && tabla.subrubro == objTemporal.subrubro);
            console.log(resultado);
            if(resultado !== undefined){
                swal('Error...!', 'Combinacion existente', 'error');
                return;
            }
        }
        
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
    for(let i=0;i<arrayClave.length;i++) {
        aux.push(arrayFiltrar.filter(arr => arr.id == arrayClave[i])[0]);
    }
    return aux;
}