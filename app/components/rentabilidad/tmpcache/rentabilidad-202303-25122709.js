class Rentabilidad extends ComponentManager {
    constructor () {
        super();
        this.objTabla = [];
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
                this.limpiarFiltro(marcas, rubros, subrubros, arrayQuerySelec);
                let objTabla = []; 
                this.cargarTabla(marcas, rubros, subrubros, objTabla);
                
                this.confirmar(id_cliente, arrayInputs, inputsArrayValue, miSession, objTabla);
                this.cerrar();
                
            })

        })
    }

    /**
     * Permite llenar todos los select q hay en la pantalla.
     */
    llenarBoxes(marcas, rubros, subrubros, arrayQuerySelec) {
        this.__llenarBox(marcas, arrayQuerySelec[0], 'TODAS');
        this.__llenarBox(rubros, arrayQuerySelec[1], 'TODAS');
        this.__llenarBox(subrubros, arrayQuerySelec[2], 'TODAS');
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
    confirmar(id, arrayInputs, inputsValueSession, session, objTabla) {
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
            //objTabla[0].id_sucursal = session.id_sucursal;
            const parametros = `id=${id}&renta=${JSON.stringify(arrayRenta)}`;
            const parametrosEsp = `datos=${JSON.stringify(objTabla)}&id_suc=${session.id_sucursal}`;
            
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
                url = new App().getUrlApi('margenesEspeciales-cargar');
                new APIs().call(url, parametrosEsp, 'POST', respuesta => {
                    console.log(respuesta);
                });
                
                swal(respuesta.result_titulo, respuesta.result_message, respuesta.result_code)
                .then(response => {
                    location.href = './main-clientes.php';
                });
            });
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

    /**
     * Permite llenar los box filtrados segun combinacion q se vaya eligiendo.
     */
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

    /**
     * Permite llenar los boxes de rubro y subrubros en base a la marca elegida.
     */
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

    cargarTabla(marcas, rubros, subrubros, objTabla) {
        let dataTableRenta = $("#contenedor-tabla-renta").DataTable({
            searching: true,
            paging: true,
            responsive: true,
            scrollY: 260
        });
        let quitarBoton = document.querySelector('#container-rentabilidad #buttonX');
        let botonAgregar = document.querySelector('#container-rentabilidad #buttonM');
        let selectMarcas = document.querySelector('#container-rentabilidad #marcas');
        let selectRubros = document.querySelector('#container-rentabilidad #rubros');
        let selectSubrubros = document.querySelector('#container-rentabilidad #subrubros');
        let margen1 = document.querySelector('#container-rentabilidad .contenedor-mgEsp input[name="margen1"]');
        let margen2 = document.querySelector('#container-rentabilidad .contenedor-mgEsp input[name="margen2"]');
        
        this.__recuperarTablaBD(objTabla, dataTableRenta);
        console.log(objTabla);
        
        let objTablaEliminar = [];       

        botonAgregar.addEventListener('click', () => {console.log(objTabla);
            let objTemporal = {id:'', marca:selectMarcas.value, rubro:selectRubros.value, subrubro:selectSubrubros.value, margen1: margen1.value, margen2: margen2.value};
            if(!this.__validarElegirUnaMinimo(objTemporal.marca, objTemporal.rubro, objTemporal.subrubro)) {
                return;
            }
            if(!this.__validarValoresMargen(objTemporal.margen1, objTemporal.margen2)) {
               return; 
            }
            let resultadoValidar = this.__validarRepetido(objTabla, objTemporal);
            this.__pintarTabla(resultadoValidar, dataTableRenta, objTabla, objTemporal, selectMarcas, selectRubros, selectSubrubros, margen1, margen2);
            
        });

        quitarBoton.addEventListener('click', () => {
            if(!objTabla.length > 0) {
                return swal('Error...!', 'Tabla vacia, nada para eliminar', 'error');
            }
            let objTemporal = {id:'', marca:selectMarcas.value, rubro:selectRubros.value, subrubro:selectSubrubros.value, margen1: margen1.value, margen2: margen2.value};
            //let resultado = this.__buscarCombinacion(objTabla, objTemporal);
            let index = objTabla.findIndex(tabla => tabla.marca == objTemporal.marca && tabla.rubro == objTemporal.rubro && tabla.subrubro == objTemporal.subrubro);
            console.log(index)
            if(index === -1) {
                return swal('Error...!', 'No existe combinacion', 'error');
            }
            //objTablaEliminar.push(resultado);
            objTablaEliminar.push(objTabla.splice(index, 1));
            this.__repintarTabla(objTabla, dataTableRenta, marcas, rubros, subrubros);
            console.log(objTablaEliminar);
        })
    }

    __pintarTabla(resultadoValidar, dataTableRenta, objTabla, objTemporal, selectMarcas, selectRubros, selectSubrubros, margen1, margen2) {
        if(resultadoValidar) {
            swal('Error...!', 'Combinacion existente', 'error');
            return;
        }
        //objTabla.push({id:'', marca:selectMarcas.value, rubro:selectRubros.value, subrubro:selectSubrubros.value, margen1: margen1.value, margen2: margen2.value});
        objTabla.push(objTemporal);
        console.log(objTabla);
        dataTableRenta.row.add([selectMarcas.options[selectMarcas.selectedIndex].text, selectRubros.options[selectRubros.selectedIndex].text, selectSubrubros.options[selectSubrubros.selectedIndex].text, margen1.value, margen2.value]);
        dataTableRenta.draw();
    }

    __validarRepetido(objTabla, objTemporal) {
        if(objTabla.length > 0){
            let resultado = this.__buscarCombinacion(objTabla, objTemporal);
            console.log(resultado);
            if(resultado !== undefined){
                return true;
            }
        }
        
    }

    limpiarFiltro(marcas, rubros, subrubros, arrayQuerySelec) {
        let botonlimpiar = document.querySelector('#container-rentabilidad #buttonR');
        botonlimpiar.addEventListener('click', () => {
            this.llenarBoxes(marcas, rubros, subrubros, arrayQuerySelec);
        })
    }

    __buscarCombinacion(objTabla, objTemporal) {
        return objTabla.find(tabla => tabla.marca == objTemporal.marca && tabla.rubro == objTemporal.rubro && tabla.subrubro == objTemporal.subrubro);
    }

    __repintarTabla(objTabla, dataTableRenta, marcas, rubros, subrubros) {
        dataTableRenta.clear().draw();
        //objTablaEliminar.push(objTabla.splice(index, 1));
        console.log(marcas)
        console.log(objTabla);
        objTabla.map(tabla => {
            let marcaDesc = marcas.find( marca => tabla.marca == marca.id);
            let rubroDesc = rubros.find( rubro => tabla.rubro == rubro.id);
            let subrubroDesc = subrubros.find( subrubro => tabla.subrubro == subrubro.id);
            console.log(subrubroDesc);
            marcaDesc ? marcaDesc = marcaDesc.descripcion : marcaDesc = 'Todas';
            rubroDesc ? rubroDesc = rubroDesc.descripcion : rubroDesc = 'Todas';
            subrubroDesc ? subrubroDesc = subrubroDesc.descripcion : subrubroDesc = 'Todas';
            dataTableRenta.row.add([marcaDesc, rubroDesc, subrubroDesc, tabla.margen1, tabla.margen2]);
            dataTableRenta.draw();
        });
    }

    __validarElegirUnaMinimo(valor1, valor2, valor3) {
        if(valor1 == 'TODAS' && valor2 == 'TODAS' && valor3 == 'TODAS') {
            return false;
        }
        return true;
    }

    __validarValoresMargen(valor1, valor2) {
        if(valor1 > 0 || valor2 > 0) {
            return true;
        }
        return false;
    }

    __recuperarTablaBD(objTabla, objGrid) {
        let url = new App().getUrlApi('margenesEspeciales-get');
        const miSession = new CacheUtils('derweb').get('sesion');
        const parametros = `filter=id_sucursal=${miSession.id_sucursal}`
        new APIs().call(url, parametros, 'GET', respuesta => {
            respuesta.forEach(respuesta => { 
                objTabla.push(respuesta)
                objGrid.row.add([respuesta.marca, respuesta.rubro, respuesta.subrubro, respuesta.margen1, respuesta.margen2]);
                objGrid.draw();
            });
            
        })
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