/**
 * Esta clase contiene la funcionalidad de la GUI para el ingreso de
 * pedidos rápidos.
 */

class IngresoPedidosRapidoGUI extends ComponentManager {
    /**
     * Constructor de clase
     * @param {string} xidcontainer Establece el id. del contenedor.
     */
    constructor(xidcontainer = null) {
        super();
        this.__idContainer = xidcontainer;
        this.__idSelectorClientes = "";
        this.__idSelectorClientesDataList = "";
        this.__modalBusquedaAbierto = false;
        this.__objDataGrid = null;
        this.__tablaArticulos = null;
        this.__nroRenglon = 0;
        this.__nombreCacheItems = "ipr_pedido_actual";

        // Limpio el contenedor principal.
        if (xidcontainer !== null)
            this.clearContainer(xidcontainer);
    }

    /**
     * Permite dibujar el formulario de ingreso de pedidos rápido.
     */
    generateComponent() {
        let urlAPI = (new App()).getUrlApi("app-entidades-getClientesByVendedor");
        let urlTemplate = (new App()).getUrlTemplate("ingreso-pedido-rapido");
        let aSesion = (new CacheUtils("derweb", false).get("sesion"));
        let idVendedor = aSesion["id_vendedor"];
    
        this.getTemplate(urlTemplate, (htmlResponse) => {
            this.__inicializarGUI(htmlResponse, urlAPI, idVendedor);
        });
    }

    /**
     * 
     * @param {string} xhtmlResponse 
     * @param {string} xurlAPI 
     * @param {int} xidVendedor 
     */
    __inicializarGUI(xhtmlResponse, xurlAPI, xidVendedor) {
        this.__idSelectorClientes = "selector-clientes";
        xhtmlResponse = this.setTemplateParameters(xhtmlResponse, "id-selector", this.__idSelectorClientes);
        // Recupero los clientes para el vendedor actual.
        (new APIs()).call(xurlAPI, "id_vendedor=" + xidVendedor, "GET", response => {
            // Creo la GUI.
            this.__crearFormulario(xhtmlResponse, response);
        });
    }

    /**
     * Permite crear la interfaz de usuario para el ingreso de pedidos rápidos.
     * @param {string} xhtmlResponse 
     * @param {array} xresponse 
     */
    __crearFormulario(xhtmlResponse, xresponse) {
        let objDataList = new LFWDataListBS();

        objDataList.setIdSelector("sel-cliente");
        objDataList.setIdDataListOptions("sel-cliente-options");
        objDataList.setEtiqueta("Cliente:");
        objDataList.setPlaceholderText("Escribí parte del código o nombre del cliente...");
        objDataList.setData(xresponse);
        objDataList.setColumns(["codusu", "nombre"]);
        objDataList.setColumnsKey(["id", "codusu"]);

        objDataList.toHtml(html => {
            // Lleno el autocomplete y dibujo el HTML en el navegador.
            xhtmlResponse = this.setTemplateParameters(xhtmlResponse, "lfw-datalist-bs", html);
            document.getElementById(this.__idContainer).innerHTML = xhtmlResponse;

            this.__inicializarInputs();

            document.getElementById("txtCodArt").addEventListener("blur", () => {
                // Al salirse del foco realizo una búsqueda inicial.
                if (!this.__validarSeleccionCliente())
                    return;
                //this.__buscarArticulo();
            });
            
            this.__crearGridItems();

            // Agrego el evento change del selector de clientes.
            document.getElementById(objDataList.idSelector).addEventListener("change", (event) => {
                //cambiarCliente = true;
                sessionStorage.removeItem('ipr_grid_items_rows');
                objDataList.getSelectedValue(event.target.value);
                let element = document.querySelector('#sel-cliente');
                element = element.getAttribute('data-value');
                element = JSON.parse(element);
                let aSesion = new CacheUtils("derweb", false).get("sesion");
                aSesion.id_cliente = element.id;
                new CacheUtils("derweb", false).set("sesion", aSesion);
                const url = new App().getUrlApi("app-entidades-sucursales");
                
                (new APIs()).call(url, "filter=id_entidad=" + element.id, "GET", response => {
                    this.__recuperarDatosDeSucursales(response, aSesion);
                    this.__recuperarPedido();

                    // Agrego el evento blur de txtCodArt
                    document.getElementById("txtCodArt").addEventListener("blur", () => {
                        this.__ejecutarBuscadorDeArticulos();
                    });

                    document.getElementById("txtCodArt").focus();
                });                            
            });

            // Evento al recibir el foco.
            document.getElementById("txtCantidad").addEventListener("focus", () => {
                // Selecciono el contenido del input.
                document.getElementById("txtCantidad").select();
            });

            // Evento keydown del input de cantidad.
            document.getElementById("txtCantidad").addEventListener("keydown", async (event) => {
                if (event.key === "Enter") {
                    this.__agregarArticuloAlPedido();
                    //Tuve q hacer esta funcion asincronica porq el recuperar pedido se ejecutaba antes que agregararticulo.
                    const recuperarPedidoAsync = () => {
                        return new Promise((resolve, reject) => {
                            setTimeout(() => {
                            try {
                                resolve(this.__recuperarPedido());
                            } catch (error) {
                                reject(error);
                            }
                            }, 2000); // Tiempo en milisegundos que deseas esperar antes de ejecutar la función sincrónica
                        });
                    }
                    try {
                        const pedido = await recuperarPedidoAsync();
                    } catch (error) {
                    console.error(error);
                    }
                }
            });

            // Evento click del botón agregar ítem
            document.getElementById("btnAgregar").addEventListener("click", async () => {
                this.__agregarArticuloAlPedido();
                //Tuve q hacer esta funcion asincronica porq el recuperar pedido se ejecutaba antes que agregararticulo.
                const recuperarPedidoAsync = () => {
                    return new Promise((resolve, reject) => {
                        setTimeout(() => {
                          try {
                            resolve(this.__recuperarPedido());
                          } catch (error) {
                            reject(error);
                          }
                        }, 2000); // Tiempo en milisegundos que deseas esperar antes de ejecutar la función sincrónica
                    });
                }
                try {
                    const pedido = await recuperarPedidoAsync();
                } catch (error) {
                console.error(error);
                }
            })

            // Estos eventos los agrego acá porque sino cuando voy al confirmar pedido me abre
            // dos veces el modal.
            document.getElementById("btnConfirmarPedido").addEventListener("click", () => {
                this.__confirmarPedido(aSesion);
            });

            document.getElementById("btnVolver").addEventListener("click", () => {
                window.location.href = "main-vendedores.php";
            });        
        });
    }

    /**
     * Permite confirmar el pedido y enviarlo a SAP.
     * @returns 
     */
    __confirmarPedido() {
        let objConfirmarPedido = new ConfirmacionPedido("app-entidades-getSucursalesByEntidad", true);
        let objModal = new LFWModalBS("main", "modal_confirmar_pedido", "Confirmar pedido");
        objConfirmarPedido.setIdModal(objModal.getIdModal());
        objConfirmarPedido.setDivModal(objModal.getModalBody());

        // Agrego funcionalidad extra al finalizar el pedido
        objConfirmarPedido.setCallbackFinalizarPedido(() => {
            objModal.close();
            ingresar_pedidos_rapido();
        });

        objConfirmarPedido.generarFooterPedido();
        objModal.open();

        aSesion = sessionStorage.getItem("derweb_sesion");
        let url = new App().getUrlApi("catalogo-pedidos-getPedidoActual");
        (new APIs()).call(url, "sesion=" + aSesion, "GET", response => {
            new CacheUtils("derven", false).set("id_pedido_sel", response.id_pedido);
        });
    }

    /**
     * Obtiene la cantidad ingresada y agrega el artículo al pedido guardando el
     * ítem en la base de datos.
     */
    __agregarArticuloAlPedido() {
        let txtCantidad = document.querySelector('#txtCantidad').value;
        let xid_articulo = JSON.parse(txtCodArt.dataset.value);
        xid_articulo = xid_articulo.values[0].id;
        this.__guardarPedido(xid_articulo, txtCantidad);
        this.__blanquearInputsItems();
        /*if (this.__agregarItem()) {
            const txtCodArt = document.querySelector('#txtCodArt');
            let xid_articulo = JSON.parse(txtCodArt.dataset.value);
            xid_articulo = xid_articulo.values[0].id;
            this.__guardarPedido(xid_articulo, txtCantidad);
            this.__blanquearInputsItems();
        }*/
    }

    /**
     * Inicializa los inputs del ingreso de artículos.
     */
    __inicializarInputs() {
        document.getElementById("txtCantidad").value = 1;
    }

    /**
     * Valida que el cliente haya sido seleccionado.
     * @returns {bool}
     */
    __validarSeleccionCliente() {
        if (document.getElementById("sel-cliente").value === "") {
            swal("Atención!!!", "Tenés que seleccionar un cliente");
            document.getElementById("sel-cliente").focus();
            return false;
        }

        return true;
    }

    /**
     * Crea la grilla de ítems del pedido.
     */
    __crearGridItems() {
        this.__objDataGrid = $("#ipr_grid_items").DataTable({
            searching: false,
            paging: false,
            responsive: true,
            scrollY: 260,
            keys: true,
            order: false,
            columns: [
                {data: "id", title: "Renglón N°", type: 'num', width: '100px'},
                {data: "codart", title: "Código", type: 'string', width: '150px'},
                {data: "descripcion", title: "Descripción", type: 'string', width: '300px'},
                {data: "cantidad", title: "Cantidad", type: 'num', width: '100px',
                    render: (data, type, row, meta) => {
                        if (type === "display")
                            return "<div style='text-align: right;'>" + data + '</div>';
                        else
                            return data;
                    }
                },
                {data: "precio_unitario", title: "Precio Unit.", type: 'num', width: '100px', 
                    render: (data, type, row, meta) => {
                        if (type === "display")
                            return "<div style='text-align: right;'>" + Intl.NumberFormat("es-ES", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data) + '</div>';
                        else
                            return data;                    
                    }
                },
                {data: "subtotal", title: "Subtotal", type: 'num', width: '100px',
                    render: (data, type, row, meta) => {
                        if (type === "display")
                            return "<div style='text-align: right;'>" + Intl.NumberFormat("es-ES", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data) + '</div>';
                        else
                            return data;
                    }
                },
                {data: "opciones", title: "Opciones", width: '100px'}
            ]
        });
        this.__acomodarFooter()
    }

    /**
     * Permite acomodar el estilo de footer para que sea fijo o
     * relativo dependiendo de la situación.
     */
    __acomodarFooter() {
        if (this.__objDataGrid.rows().count() !== 0) {
            let objFooter = document.getElementById("app-footer");
            objFooter.style.bottom = "none";
            objFooter.style.position = "relative";
        }
    }

    /**
     * Recupera los datos de las sucursales de la entidad seleccionada.
     * @param {array} xresponse 
     * @param {array} xaSesion 
     */
    __recuperarDatosDeSucursales(xresponse, xaSesion) {
        if (xresponse.length > 1) {
            this.__seleccionar_sucursal(xresponse);
        } else {
            xaSesion.id_sucursal = xresponse[0].id;
            new CacheUtils("derweb", false).set("sesion", xaSesion);
        }
    }

    /**
     * Permite recuprar el pedido que se encuentra actualmente pendiente de
     * confirmar.
     */
    __recuperarPedido() {console.log(this.__objDataGrid);
        const urlPed = new App().getUrlApi("catalogo-pedidos-getPedidoActual");
        aSesion = sessionStorage.getItem("derweb_sesion");

        // Limpio el datatable
        this.__objDataGrid.clear().draw();

        (new APIs()).call(urlPed, "sesion=" + aSesion, "GET", response => {
            let arrItems = response.items;
            this.__guardarPedidoItemEnCache(response)
            
            if (arrItems !== undefined) {
                arrItems.forEach((item, index) => {
                    var opciones = "<a href='javascript:editarItem(\"" + item.id + "\");'><i class='fa-regular fa-pen-to-square fa-xl'></i></a>&nbsp;&nbsp;&nbsp;";
                    opciones += `<a href='javascript:eliminarItem(${response.id_pedido},${item.id})'><i class='fa-solid fa-trash-can fa-xl'></i></a>`

                    item = {
                        "id": index + 1,
                        "codart": item.codigo,
                        "descripcion": item.descripcion,
                        "cantidad": item.cantidad,
                        "precio_unitario": item.costo,
                        "subtotal": item.costo * item.cantidad,
                        "opciones": opciones
                    };
                    
                    this.__objDataGrid.row.add(item);
                    this.__nroRenglon = (index + 1);
                });
            } else {
                this.__nroRenglon = 0;
            }

            this.__refrescarGrillaItemsPedido();
            this.__acomodarFooter();
            this.__calcularTotalPedido();
        })
    }

    /**
     * Inicia la ejecución del buscador de artículos. Este método se llama en el evento
     * blur de txtCodArt.
     * @returns {void}
     */
    async __ejecutarBuscadorDeArticulos() {
        // Al salirse del foco realizo una búsqueda inicial.
        if (!this.__validarSeleccionCliente())
            return;

        if(document.getElementById("txtCodArt").value == '') {
            //swal('warning','Debes completar el campo articulo');
            return;
        }
        const boxText = document.getElementById("txtCodArt");
        const url = (new App()).getUrlApi("catalogo-articulos-getByFranse");
        const sesion = JSON.stringify((new CacheUtils("derweb")).get("sesion"));
        const objbuscador = new Buscador(boxText, 1, url, undefined, sesion, 0);
        const response = await objbuscador.initComponent();
        console.log(response)
        //usar aca el response
        if (response.values.length === 1) {
            document.getElementById("txtCodArt").value = response.values[0]["codigo"];
            document.getElementById("txtDescripcion").value = response.values[0]["desc"];
            document.getElementById("txtCantidad").focus();
            
            // Pongo el JSON del artículo seleccionado en data-value en txtCodArt
            document.getElementById("txtCodArt").dataset.value = JSON.stringify(response);
            this.__modalBusquedaAbierto = false;
        } else {
            // En este caso tengo que abrir el modal.
            //this.__buscarArticuloEnGrilla(url, sesion, 0, filter);
            this.__buscarArticuloEnGrilla(boxText, url, sesion, 0);
            (new CacheUtils("derweb")).set("sesion_temporal", new CacheUtils("derweb").get("sesion"));
        }
        //this.__buscarArticulo();        
    }    

    /**
     * Busca un artículo por frase.
     */
    __buscarArticulo() {
        let txtCodArt = document.getElementById("txtCodArt").value;
        let url = (new App()).getUrlApi("catalogo-articulos-getByFranse");
        let aSesion = (new CacheUtils("derweb")).get("sesion");
        let sesion;
        let filter = "frase=" + txtCodArt;

        this.__modalBusquedaAbierto = false;
        sesion = "sesion=" + JSON.stringify(aSesion);
        
        (new APIs()).call(url, sesion + "&pagina=0&" + filter, "GET", response  => {
            if (response.values.length === 1) {
                document.getElementById("txtCodArt").value = response.values[0]["codigo"];
                document.getElementById("txtDescripcion").value = response.values[0]["desc"];
                document.getElementById("txtCantidad").focus();
                
                // Pongo el JSON del artículo seleccionado en data-value en txtCodArt
                document.getElementById("txtCodArt").dataset.value = JSON.stringify(response);
                this.__modalBusquedaAbierto = false;
            } else {
                // En este caso tengo que abrir el modal.
                this.__buscarArticuloEnGrilla(url, sesion, 0, filter);
                (new CacheUtils("derweb")).set("sesion_temporal", aSesion);
            }
        });
    }

    /**
     * Permite llenar la grilla de búsqueda de artículos con las coíncidencias.
     * @param {string} xurl
     * @param {string} xsesion 
     * @param {int} xpagina 
     * @param {string} xfilter 
     */
    async __buscarArticuloEnGrilla(xboxText, xurl, xsesion, xpagina/*, xfilter*/) {
        var tablaArticulos = null;
        if (!this.__modalBusquedaAbierto) {console.log('queondawey')
            this.getTemplate((new App()).getUrlTemplate("ipr-grid-articulos"), (htmlResponse) => {
                let objModal = new LFWModalBS(
                    "main", 
                    "modal_articulos", 
                    "Búsqueda de artículos",
                    htmlResponse,
                    "100%");
                
                objModal.open();
                this.__modalBusquedaAbierto = true;
    
                // Agrego evento al hacer clic en cerrar del modal. Al cerrar marco el estado del modal a 
                // cerrado.
                document.getElementById("modal_articulos_btnclose").addEventListener("click", () => {
                    this.__modalBusquedaAbierto = false;
                    objModal.close();
                });

                // Inicializo el datatable
                this.__tablaArticulos = $("#ipr_grid_articulos").DataTable({
                    searching: true,
                    paging: true,
                    responsive: true,
                    scrollY: 260
                });        
            });
        }

        // Cargo el datatable con los resultados obtenidos.
        this.__modalBusquedaAbierto = false;
        const objbuscador = new Buscador(xboxText, 1, xurl, undefined, xsesion, xpagina);
        const response = await objbuscador.initComponent();
        if (response.values.length !== 0) {
            xpagina += 40;
            this.__buscarArticuloEnGrilla(xboxText, xurl, xsesion, xpagina/*, xfilter*/);
            response.values.forEach((row) => {
                let linkSelect = "<a href='javascript:seleccionar_articulo(" + row.id + ");' title='Seleccionar'><i class='fa fa-arrow-right-to-bracket fa-lg'></i></a>";
                this.__tablaArticulos.row.add([row.id, row.codigo, row.desc, linkSelect]);
            });
            this.__tablaArticulos.draw();
        }

        /*
        (new APIs().call(xurl, xsesion + "&pagina=" + xpagina + "&" + xfilter, "GET", 
            response => {
                if (response.values.length !== 0) {
                    xpagina += 40;
                    this.__buscarArticuloEnGrilla(xurl, xsesion, xpagina, xfilter);
                    response.values.forEach((row) => {
                        let linkSelect = "<a href='javascript:seleccionar_articulo(" + row.id + ");' title='Seleccionar'><i class='fa fa-arrow-right-to-bracket fa-lg'></i></a>";
                        this.__tablaArticulos.row.add([row.id, row.codigo, row.desc, linkSelect]);
                    });
                    this.__tablaArticulos.draw();
                }
        }));
        */
    }

    /**
     * Permite agregar un artículo a la grilla. En caso de que el
     * artículo esté repetido, entonces, suma la cantidad.
     * @returns {bool} true: si el artículo se agregó, false: si no pasó las validaciones.
     */
    __agregarItem() {        
        let articulo = JSON.parse(document.getElementById("txtCodArt").dataset.value);
        let item = {};
        var opciones = "<a href='#'><i class='fa-regular fa-pen-to-square fa-xl'></i></a>&nbsp;&nbsp;&nbsp;";
        opciones += "<a href='#'><i class='fa-solid fa-trash-can fa-xl'></i></a>"


        if (parseInt(document.getElementById("txtCantidad").value) === 0) {
            alert("Debe ingresar la cantidad");
            return false;
        }

        item = {
            "id": this.__nroRenglon + 1,
            "codart": articulo.values[0].codigo,
            "descripcion": articulo.values[0].desc,
            "cantidad": parseFloat(document.getElementById("txtCantidad").value),
            "precio_unitario": parseFloat(articulo.values[0].cped).toFixed(2),
            "subtotal": (articulo.values[0].cped * parseInt(document.getElementById("txtCantidad").value)).toFixed(2),
            "opciones": opciones
        };

        if (!this.validarItemRepetido(item)) {
            // Agregar el ítem en la grilla.
            this.__objDataGrid.row.add(item);
            this.__refrescarGrillaItemsPedido();
            this.__nroRenglon++;
        }
            
        this.__calcularTotalPedido();
        this.__acomodarFooter();

        return true;
    }

    /**
     * Permite refrescar la grilla de los ítems del pedido actual.
     */
    __refrescarGrillaItemsPedido() {
        this.__objDataGrid.draw(false);
        this.__guardarItemsEnCache();
    }

    /**
     * Permite guardar los ítems de la grilla en cache para poder manipular la información
     * desde la interfaz de usuario.
     */
    __guardarItemsEnCache() {
        let items = this.__objDataGrid.rows().data().toArray();
        let objCache = new CacheUtils("derweb");
        objCache.set(this.__nombreCacheItems, items);
    }

    /**
     * Permite guardar los ítems de la grilla en cache para poder manipular la información
     * desde la interfaz de usuario.
     * @param {Object} arrItems
     */
    __guardarPedidoItemEnCache(arrItems) {
        let objCache = new CacheUtils("derweb");
        objCache.set('pedido-item', arrItems);
    }

    /**
     * Calcula el total del pedido en base a los ítems agregados.
     */
    __calcularTotalPedido() {
        let total = 0.00;
        let items = this.__objDataGrid.rows().data().toArray();
        items.forEach(element => {
            total += parseFloat(element.subtotal);
        });

        document.getElementById("txtTotal").value = total.toFixed(2);
    }

    /**
     * Blanquea los controles de los inputs del ítem y se posiciona
     * en el ingreso del código de artículo.
     */
    __blanquearInputsItems() {
        document.getElementById("txtCodArt").value = "";
        document.getElementById("txtDescripcion").value = "";
        document.getElementById("txtCantidad").value = 1;
        document.getElementById("txtCodArt").focus();
    }

    /**
     * Arma el contenido de la pantalla modal para finalizar pedidos permitiendo
     * la selección de sucursales.
     * @param {array} arraySuc 
     */
    __seleccionar_sucursal(arraySuc) {
        let html = `
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Selecciona sucursal</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Por favor selecciona una sucursal:</p>
                <select class="form-control" name="sucursal" id= "selectorSuc">
                </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="btnSelect">Seleccionar</button>
            </div>
          </div>
        </div>`;
        let modal = this.crearElementDom('div', 'modal', 'modalSuc', ['tabindex','-1']);
        modal.style.display = 'block';
        modal.innerHTML = html;
        let container = document.querySelector('#app-container');
        container.append(modal);
        let selector = document.querySelector('#selectorSuc');

        arraySuc.forEach(sucursal => {
            let option = document.createElement('option');
            option.innerText = sucursal.nombre;
            option.value = sucursal.id;
            selector.append(option);
        })

        modal.addEventListener('click', (e) => {
           if(e.target.id == 'modalSuc' || e.target.classList == 'btn-close') {
                ingresar_pedidos_rapido();
           }
        });
        let btnSelec = document.querySelector('#btnSelect');
        
        btnSelec.addEventListener('click', ()=> {
            let aSesion = new CacheUtils("derweb", false).get("sesion");
            aSesion.id_sucursal = parseInt(selector.value);
            new CacheUtils("derweb", false).set("sesion", aSesion);
            modal.style.display = 'none';
            this.__recuperarPedido();
        });
    }

    /**
     * Permite guardar el ítem de un pedido en la base de datos.
     * @param {int} xidarticulo Id. de artículo.
     * @param {double} cantidad Cantidad
     * @returns 
     */
    __guardarPedido(xidarticulo, cantidad) {
        let aSesion = JSON.parse(sessionStorage.getItem("derweb_sesion"));
        let objCatalogo = new Catalogo();
    
        if (cantidad == "" || cantidad < 1) {
            swal("Cantidad vacia o valor incorrecto");
            document.getElementById("txtCantidad").focus();
            return;
        }
    
        // Recupero los datos de la sucursal predeterminada
        objCatalogo.getSucursalPredeterminadaByCliente(aSesion["id_cliente"], (xaSucursal) => {
            if (xaSucursal === undefined) {
                alert("Usted no tiene sucursal asignada, por favor comuníquese con sistemas para resolver este problema");
                return;
            }

            this.__guardarItemEnBD(aSesion, xaSucursal, objCatalogo, xidarticulo, cantidad);
        });
    }

    /**
     * Permite grabar el artículo en el pedido.
     * @param {array} xaSesion Datos de sesión iniciada actualmente.
     * @param {array} xaSucursal Sucursal predeterminada.
     * @param {Catalogo} xobjCatalogo Objeto catálogo
     * @param {int} xidarticulo Id. de artículo
     * @param {double} xcantidad Cantidad
     */
    __guardarItemEnBD(xaSesion, xaSucursal, xobjCatalogo, xidarticulo, xcantidad) {console.log('pregrabo')
        let aCabecera = {
            "id_cliente": parseInt(xaSesion["id_cliente"]),
            "id_tipoentidad": parseInt(xaSesion["id_tipoentidad"]),
            "id_vendedor": parseInt(xaSucursal[0]["id_vendedor"]),
            "id_sucursal": parseInt(xaSucursal[0]["id"]),
            "codigo_sucursal": xaSucursal[0]["codigo_sucursal"],
            "id_transporte": xaSucursal[0]["id_transporte"],
            "codigo_transporte": xaSucursal[0]["codigo_transporte"],
            "id_formaenvio": xaSucursal[0]["id_formaenvio"],
            "codigo_forma_envio": xaSucursal[0]["codigo_forma_envio"]
        };

        xobjCatalogo.agregarArticuloEnCarrito(xaSesion, xidarticulo, xcantidad, aCabecera);
    }

    /**
     * Permite verificar si un ítem se encuentra repetido en el pedido. Si está repetido
     * entonces le suma la cantidad.
     * @param {array} xitem 
     * @returns {bool} true: si el ítem está repetido | false: en caso contrario.
     */
    validarItemRepetido(xitem) {
        let items = this.__objDataGrid.rows().data().toArray();
        let resultado = false;
        items.forEach((element, index) => {
            if (element.codart === xitem.codart) {
                this.__sumarCantidadAlItemRepetido(xitem, element, index);
                resultado = true;
                return;
            }
        });

        return resultado;
    }

    /**
     * Permite sumar la cantidad al ítem repetido.
     * @param {array} xitem Item original que está cargado actualmente.
     * @param {array} element Item que se está cargando en el sistema.
     * @param {int} index Índice de fila de la grilla.
     */
    __sumarCantidadAlItemRepetido(xitem, element, index) {
        xitem.cantidad += parseFloat(element.cantidad);
        xitem.subtotal = parseFloat(xitem.cantidad) * parseFloat(element.precio_unitario);
        this.__objDataGrid.row(index).data(xitem);
        this.__refrescarGrillaItemsPedido();
    }

    editarItem(id) {
        this.__objDataGrid = $('#ipr_grid_items').DataTable();
        const aPedidoItem = (new CacheUtils("derweb")).get("pedido-item");
        swal("Cantidad:", {
            content: "input",
        })
        .then((value) => {
            if(value < 1 ) {
                return;
            }
            let dat = aPedidoItem["items"].filter(datos => datos.id == id);
            let Ojson = dat;
            Ojson[0].id_pedido = aPedidoItem.id_pedido;
            Ojson[0].cantidad = value;
            Ojson[0].costo_unitario = dat[0]["costo"];
            Ojson[0].total = dat[0].subtotal_final;
            delete Ojson[0].costo;
            delete Ojson[0].subtotal_final;
            // Preparo el api para enviar al php.
            let objApp = new App();
            let urlAPI = objApp.getUrlApi("catalogo-pedidos-modificar-items");
            let objAPI = new APIs();
            objAPI.call(urlAPI, "data=" + JSON.stringify(Ojson[0]), "PUT", (response) => {
                swal(response.codigo, response.mensaje, 'success')
                .then( () => {
                    this.__recuperarPedido();
                })
            });
        });
    }

    /**
     * Permite eliminar un item al hacer clic en "tash"
     */
    borrarItem(xidpedido, xId) {
        this.__objDataGrid = $('#ipr_grid_items').DataTable();
        const url =  app.getUrlApi("catalogo-pedidos-eliminarItem");
        const pedidoStorage = JSON.parse(sessionStorage.getItem("derweb_ipr_pedido_actual"));
        if(pedidoStorage.length>1) {
            let objCarrito = new MiCarritoModalComponent;
            objCarrito.eliminar_item_carrito(url, xidpedido, xId);
        } else {
            this.__vaciar_carrito(xidpedido);
        }
        this.__recuperarPedido();
    }

    /**
     * Permite vaciar mi carrito al hacer clic en "Vaciar mi carrito"
     */
    __vaciar_carrito(xidpedido) {
        const url =  app.getUrlApi("catalogo-pedidos-vaciarCarrito");
        let objCarrito = new MiCarritoModalComponent;
        objCarrito.vaciarMiCarrito(url, xidpedido);
    }
}