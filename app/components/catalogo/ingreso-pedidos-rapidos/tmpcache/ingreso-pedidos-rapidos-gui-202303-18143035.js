/**
 * Esta clase contiene la funcionalidad de la GUI para el ingreso de
 * pedidos rápidos.
 */

class IngresoPedidosRapidoGUI extends ComponentManager {
    /**
     * Constructor de clase
     * @param {string} xidcontainer Establece el id. del contenedor.
     */
    constructor(xidcontainer) {
        super();
        this.__idContainer = xidcontainer;
        this.__idSelectorClientes = "";
        this.__idSelectorClientesDataList = "";
        this.__modalBusquedaAbierto = false;
        this.__objGridArticulos = null;
        this.__tablaArticulos = null;
        this.__nroRenglon = 0;

        // Limpio el contenedor principal.
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
            this.__idSelectorClientes = "selector-clientes";
            htmlResponse = this.setTemplateParameters(htmlResponse, "id-selector", this.__idSelectorClientes);
            // Recupero los clientes para el vendedor actual.
            (new APIs()).call(urlAPI, "id_vendedor=" + idVendedor, "GET", response => {
                // Creo la GUI.
                this.__crearFormulario(htmlResponse, response);
            });
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
            });
            
            let cambiarCliente = false;

            // Agrego el evento change del selector de clientes.
            document.getElementById(objDataList.idSelector).addEventListener("change", (event) => {
                cambiarCliente = true;
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
                    if(response.length >1) {
                        this.seleccionar_sucursal(response);
                    } else {
                        aSesion = new CacheUtils("derweb", false).get("sesion");
                        aSesion.id_sucursal = response[0].id;
                        new CacheUtils("derweb", false).set("sesion", aSesion);
                        this.pintarPedido();
                    }

                    // Agrego el evento blur de txtCodArt
                    document.getElementById("txtCodArt").addEventListener("blur", () => {
                        // Al salirse del foco realizo una búsqueda inicial.
                        if (!this.__validarSeleccionCliente())
                            return;
                        if(document.getElementById("txtCodArt").value == '') {
                            //swal('warning','Debes completar el campo articulo');
                            return;
                        }

                        this.__buscarArticulo();
                    });

                    // Evento al recibir el foco.
                    document.getElementById("txtCantidad").addEventListener("focus", () => {
                        // Selecciono el contenido del input.
                        document.getElementById("txtCantidad").select();
                    });

                    // Agrego el evento cantidad.
                    document.getElementById("txtCantidad").addEventListener("blur", () => {
                        let txtCantidad = document.querySelector('#txtCantidad').value;
                        this.__agregarItem();
                        const txtCodArt = document.querySelector('#txtCodArt');
                        let xid_articulo = JSON.parse(txtCodArt.dataset.value);
                        xid_articulo = xid_articulo.values[0].id;
                        //this.agregarArticulo();
                    
                        this.__agregarEnTabla(xid_articulo, txtCantidad);
                        new CacheUtils("derven", false).set("id_pedido_sel", 179);
                    });

                    // Eventos botones confirmar y volver
                    document.getElementById("btnConfirmarPedido").addEventListener("click", () => {
                        // Desarrollar llamado a API para enviar y confirmar el pedido.
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
                        })
                    });

                    // LLamo al método que crea la grilla.
                    this.__crearGridItems();

                    document.getElementById("sel-cliente").focus();
                            
                });
                        
            });

            document.getElementById("btnVolver").addEventListener("click", () => {
                window.location.href = "main-vendedores.php";
            });
            
            document.getElementById(objDataList.idSelector).addEventListener('click', () =>{
                if(cambiarCliente) {
                    ingresar_pedidos_rapido();
                    cambiarCliente = false;
                }
            })

        });
    }

    __inicializarInputs() {
        document.getElementById("txtCantidad").value = 0;
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
     * Busca un artículo por frase.
     */
    __buscarArticulo() {
        let txtCodArt = document.getElementById("txtCodArt").value;
        let url = (new App()).getUrlApi("catalogo-articulos-getByFranse");
        let aClienteSeleccionado;
        let aSesion = (new CacheUtils("derweb")).get("sesion");
        let sesion;
        let filter = "frase=" + txtCodArt;

        this.__modalBusquedaAbierto = false;
        
        aClienteSeleccionado = JSON.parse(document.getElementById("sel-cliente").dataset.value);
        //aSesion["id_cliente"] = aClienteSeleccionado["id"];
        //aSesion["id_sucursal"] = aClienteSeleccionado["id_sucursal"];
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
    __buscarArticuloEnGrilla(xurl, xsesion, xpagina, xfilter) {
        var tablaArticulos = null;
        if (!this.__modalBusquedaAbierto) {
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
    }

    /**
     * Crea la grilla de ítems del pedido.
     */
    __crearGridItems() {
        /*this.__objDataGrid = new LFWDataGrid("ipr_grid_items", "id")
        this.__objDataGrid.setAsociatedFormId("frm-ingreso-pedido-rapido");
        this.__objDataGrid.setPermitirEditarRegistro(true);
        this.__objDataGrid.setPermitirEliminarRegistro(true);
        this.__objDataGrid.setPermitirOrden(false);
        this.__objDataGrid.setPermitirFiltros(false);
        
        this.__objDataGrid.agregarColumna("Renglón", "id", "numeric", 0, false);
        this.__objDataGrid.agregarColumna("Código de artículo", "codart", "string");
        this.__objDataGrid.agregarColumna("Descripción", "descripcion", "string");
        this.__objDataGrid.agregarColumna("Cantidad", "cantidad", "numeric");
        this.__objDataGrid.agregarColumna("Precio Unit.", "precio_unitario", "numeric");
        this.__objDataGrid.agregarColumna("Subtotal", "subtotal", "numeric");*/

        this.__objDataGrid = $("#ipr_grid_items").DataTable({
            searching: false,
            paging: false,
            responsive: true,
            scrollY: 260,
            columns: [
                {data: "id", title: "Renglón N°", type: 'num'},
                {data: "codart", title: "Código", type: 'string'},
                {data: "descripcion", title: "Descripción", type: 'string'},
                {data: "cantidad", title: "Cantidad", type: 'num'},
                {data: "precio_unitario", title: "Precio Unit.", type: 'num', render: (data, type, row, meta) => {
                    if (type === "display")
                        return "<div style='text-align: right;'>" + data + '</div>';
                    else
                        return data;                    
                }},
                {data: "subtotal", title: "Subtotal", type: 'num', render: this.__formatearNumeros(data, type, row, meta)}
            ]
        });
        this.acomodarFooter()
    }

    __formatearNumeros(xdata, xtype, xrow, xmeta) {
        if (type === "display")
            return "<div style='text-align: right;'>" + data + '</div>';
        else
            return data;
    }

    /**
     * Permite acomodar el estilo de footer para que sea fijo o
     * relativo dependiendo de la situación.
     */
    acomodarFooter() {
        if (this.__objDataGrid.rows().count() !== 0) {
            let objFooter = document.getElementById("app-footer");
            objFooter.style.bottom = "none";
            objFooter.style.position = "relative";
        }
    }

    /**
     * Agrega un ítem al pedido.
     */
    __agregarItem() {        
        let articulo = JSON.parse(document.getElementById("txtCodArt").dataset.value);
        let item = {};

        if (parseInt(document.getElementById("txtCantidad").value) === 0) {
            swal("Debe ingresar la cantidad");
            return false;
        }

        this.__nroRenglon++;
        item = {
            "id": this.__nroRenglon,
            "codart": articulo.values[0].codigo,
            "descripcion": articulo.values[0].desc,
            "cantidad": parseFloat(document.getElementById("txtCantidad").value),
            "precio_unitario": parseFloat(articulo.values[0].cped).toFixed(2),
            "subtotal": (articulo.values[0].cped * parseInt(document.getElementById("txtCantidad").value)).toFixed(2)
        };

        this.__objDataGrid.row.add(item).draw();
        this.__calcularTotalPedido();
        this.__blanquearInputsItems();
        this.acomodarFooter();

        /*if(this.__objDataGrid.getDataGrid() != null) 
            item = this.validarItemRepetido(item);

        if(item.id > 0) 
            this.__objDataGrid.deleteRowByCampoClave(item.id);*/

        /*this.__objDataGrid.agregarFila(item);
        this.__objDataGrid.refresh();
        this.__calcularTotalPedido();
        this.__blanquearInputsItems();*/
    }

    /**
     * Calcula el total del pedido en base a los ítems agregados.
     */
    __calcularTotalPedido() {
        let aItems = this.__objDataGrid.getRows();
        let total = 0.00;
        aItems.forEach(element => {
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
        document.getElementById("txtCantidad").value = 0;
        document.getElementById("txtCodArt").focus();
    }

    seleccionar_sucursal(arraySuc) {
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
            this.pintarPedido();
        });
    }
    __agregarEnTabla(xidarticulo, cantidad) {
        let aSesion = JSON.parse(sessionStorage.getItem("derweb_sesion"));
        let objCatalogo = new Catalogo();
    
        if (cantidad == "" || cantidad < 1) {
            alert("Cantidad vacia o valor incorrecto");
            return;
        }
    
        // Recupero los datos de la sucursal predeterminada
        objCatalogo.getSucursalPredeterminadaByCliente(aSesion["id_cliente"], (xaSucursal) => {
            let acabecera = {
                "id_cliente": parseInt(aSesion["id_cliente"]),
                "id_tipoentidad": parseInt(aSesion["id_tipoentidad"]),
                "id_vendedor": parseInt(xaSucursal[0]["id_vendedor"]),
                "id_sucursal": parseInt(xaSucursal[0]["id"]),
                "codigo_sucursal": xaSucursal[0]["codigo_sucursal"],
                "id_transporte": xaSucursal[0]["id_transporte"],
                "codigo_transporte": xaSucursal[0]["codigo_transporte"],
                "id_formaenvio": xaSucursal[0]["id_formaenvio"],
                "codigo_forma_envio": xaSucursal[0]["codigo_forma_envio"]
            };
    
            if (xaSucursal === undefined) {
                alert("Usted no tiene sucursal asignada, por favor comuníquese con sistemas para resolver este problema");
                return;
            }
            
            objCatalogo.agregarArticuloEnCarrito(aSesion, xidarticulo, cantidad, acabecera);
        });
    }

    pintarPedido() {
        const urlPed = new App().getUrlApi("catalogo-pedidos-getPedidoActual");
        aSesion = sessionStorage.getItem("derweb_sesion");
        (new APIs()).call(urlPed, "sesion=" + aSesion, "GET", response => { 
            let arrItems = response.items;
            let total = 0.00;

            if (arrItems !== undefined)
                arrItems.forEach((item, index) => {
                    item = {
                        "id": 0,
                        "codart": item.codigo,
                        "descripcion": item.descripcion,
                        "cantidad": item.cantidad,
                        "precio_unitario": item.costo,
                        "subtotal": item.costo * item.cantidad
                    };
                    
                    /*this.__objDataGrid.agregarFila(item);
                    this.__objDataGrid.refresh();*/
                    this.__objDataGrid.row.add(item);
                    total += (item.costo * item.cantidad);
                });

            this.__objDataGrid.draw();
            this.acomodarFooter();
            /*this.__calcularTotalPedido();
            this.__blanquearInputsItems();*/

            document.getElementById("txtTotal").value = total.toFixed(2);


            if(response.id_pedido == 0) {
                //this.__objDataGrid.refresh();
            }
        })
    }

    validarItemRepetido(item) {
        let longitud = this.__objDataGrid.getRows().length;
        let element = this.__objDataGrid.getRows();
        for(let i=0;i<longitud;i++) {
            if(element[i].codart == item.codart) {
                item.cantidad += parseFloat(element[i].cantidad);
                item.id = element[i].id;
                return item;
            }
        }
        return item;
    }
}

// Funciones a medida de esta operación.

/**
 * Selecciona un artículo de la grilla.
 * @param {int} xid Id. de artículo seleccionado.
 */
function seleccionar_articulo(xid) {
    let url = (new App()).getUrlApi("catalogo-articulos-get");
    let sesion_tmp = (new CacheUtils("derweb")).get("sesion_temporal");
    let parametros = "sesion=" + JSON.stringify(sesion_tmp) + "&pagina=0&filter=\"art.id=" + xid + "\"";

    (new APIs()).call(url, parametros, "GET", response => {
        document.getElementById("txtCodArt").value = response.values[0]["codigo"];
        document.getElementById("txtDescripcion").value = response.values[0]["desc"];
        document.getElementById("txtCantidad").focus();

        // Pongo el JSON del artículo seleccionado en data-value en txtCodArt
        document.getElementById("txtCodArt").dataset.value = JSON.stringify(response);
        this.__modalBusquedaAbierto = false;
        (new CacheUtils("derweb")).remove("sesion_temporal");

        // Cierro el modal
        document.getElementById("main").removeChild(document.getElementById("modal_articulos"));
        document.querySelector("#page-container > div.modal-backdrop.fade.show").remove();
        document.getElementById("txtCantidad").focus();
    });
}

