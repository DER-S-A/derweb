/**
 * Clase: EdicionPedidosPendientes
 * Descripción:
 *  Esta clase se encarga de la edición y confirmación de un pedido
 *  pendiente.
 */

class EdicionPedidosPendientes extends ComponentManager {
    constructor() {
        super();
        this.__rows_cache_name = "";
    }

    /**
     * Permite entrar al pedido para editarlo y/o confirmarlo.
     * @param {int} xidpedido 
     */
     entrarAlPedido(xidpedido) {
        this.getTemplate((new App()).getUrlTemplate("oper-pedidos-pendientes"), html => {
            document.getElementById("app-container").innerHTML = html;
            let pedidoSeleccionado =   this.__getPedidoSeleccionado(xidpedido);
            console.log(pedidoSeleccionado)
            let items = pedidoSeleccionado[0]["items"];
            document.getElementById("app_grid_container").innerHTML = "";
            document.getElementById("app_grid_container").appendChild(this.__mostrarCabeceraPedido(pedidoSeleccionado));
            this.__mostrarGridItemsPedidoSeleccionado(items);
            this.__mostrarPiePedidoSeleccionado(xidpedido);
    
            // Agrego el id de pedido seleccionado a session storage para mantener el valor.
            (new CacheUtils("derven", false)).set("id_pedido_sel", xidpedido);

            // Piso el id del cliente en la sesión.
            let aSesion = (new CacheUtils("derweb", false).get("sesion"));
            aSesion["id_cliente"] = parseInt(pedidoSeleccionado[0]["id_entidad"]);
            aSesion["id_sucursal"] = parseInt(pedidoSeleccionado[0]["id_sucursal"]);
            (new CacheUtils("derweb", false).set("sesion", aSesion));
        });
    }

    /**
     * Obtiene el pedido actualmente seleccionado por Id.
     * @param {int} xidpedido 
     * @returns 
     */
    __getPedidoSeleccionado(xidpedido) {
        let objCache = new CacheUtils(_APPNAME, false);
        let pedidosPendientesCached = objCache.get("pedido-actual");
        let pedidos = [];
        pedidos["rows"] = pedidosPendientesCached;
        let pedidoSeleccionado = pedidos.rows.filter(xelement => {
            if (parseInt(xelement["id"]) === parseInt(xidpedido))
                return true;
            else
                return false;
        });
        return pedidoSeleccionado;
    }

    /**
     * Muestra los datos de cabecera del pedido seleccionado.
     * @param {array} xaPedidoSeleccionado 
     * @returns 
     */
    __mostrarCabeceraPedido(xaPedidoSeleccionado) {
        let objDivCabecera = document.createElement("div");
        let objLayoutRow = document.createElement("div");
        let objLayoutCol = [];
        let objCampoNroPedido = document.createElement("div");
        let objCampoClienteNro = document.createElement("div");
        let objCampoClienteNombre = document.createElement("div");

        objDivCabecera.classList.add("contenedor-cabecera");

        objLayoutRow.classList.add("row");
        objLayoutCol[0] = document.createElement("div");
        objLayoutCol[1] = document.createElement("div");
        objLayoutCol[2] = document.createElement("div");
        objLayoutCol[0].classList.add("col-4");
        objLayoutCol[1].classList.add("col-4");
        objLayoutCol[2].classList.add("col-4");

        objCampoNroPedido.innerHTML = "<label>Pedido N°:</label> " + xaPedidoSeleccionado[0].id;
        objLayoutCol[0].appendChild(objCampoNroPedido);
        objLayoutRow.appendChild(objLayoutCol[0]);

        objCampoClienteNro.innerHTML = "<label>Código de Cliente: </label> " + xaPedidoSeleccionado[0].cliente_cardcode;
        objLayoutCol[1].appendChild(objCampoClienteNro);
        objLayoutRow.appendChild(objLayoutCol[1]);
        
        objCampoClienteNombre.innerHTML = "<label>Razón Social:</label> " + xaPedidoSeleccionado[0].nombre;
        objLayoutCol[2].appendChild(objCampoClienteNombre);
        objLayoutRow.appendChild(objLayoutCol[2]);

        objDivCabecera.appendChild(objLayoutRow);
        return objDivCabecera;
    }

    /**
     * Muestra la grilla con los ítems del pedido seleccionado
     * @param {datos} xdatos 
     */
     __mostrarGridItemsPedidoSeleccionado(xdatos) {    
        let objGrid = new LFWDataGrid("app_grid_container", "id");

        objGrid.setAsociatedFormId("formulario");
        objGrid.setPermitirOrden(true);
        objGrid.setPermitirFiltros(true);
        objGrid.setPermitirEditarRegistro(true);
        objGrid.setPermitirEliminarRegistro(true);
        objGrid.setEditJavascriptFunctionName("editar_pedido");
    
        objGrid.agregarColumna("Cantidad", "cantidad", "numeric");
        objGrid.agregarColumna("Código", "codigo", "string");
        objGrid.agregarColumna("Descripción", "descripcion", "string");
        objGrid.agregarColumna("Pr. Unit.", "costo_unitario", "numeric");
        objGrid.agregarColumna("Subtotal", "subtotal", "numeric");
        objGrid.agregarColumna("Total", "total", "numeric");
    
        xdatos.forEach(xelement => {
            objGrid.agregarFila(xelement);
        });
        
        objGrid.refresh();
        (new CacheUtils("derweb", false)).set("grid-items-cache-name", objGrid.getCacheName());
        this.__rows_cache_name = objGrid.getCacheName();
    }

    /**
     * Permite mostrar el pié del pedido seleccionado.
     * @param {int} xidpedido Id. del pedido seleccionado
     */
    __mostrarPiePedidoSeleccionado(xidpedido) {
        let objContainer = document.getElementById("main");
        let objDivTotal = document.createElement("div");
        let txtTotal = new HTMLInput("txtTotal", "Total:");
        let pedidoSeleccionado = this.__getPedidoSeleccionado(xidpedido);
        let objRow = this.__addBootstrapRow();
        let objCol = this.__addBoostralColumn(["col-m-6"]);

        objRow.id = "pedsel-row-footer";
        //objContainer.innerHTML += "<hr>";

        objDivTotal.id = "contenedor-totales";
        objDivTotal.classList.add("div-total-pedido-seleccionado");

        objRow.appendChild(this.__crearBotoneraFooter());

        txtTotal.setReadOnly();
        txtTotal.setDataType("float");
        txtTotal.setWidth(300);
        txtTotal.setValue(pedidoSeleccionado[0]["total"].toFixed(2));
        objDivTotal.appendChild(txtTotal.toHtml());
        objCol.appendChild(objDivTotal);
        objRow.appendChild(objCol);

        objContainer.appendChild(objRow);

        // Agrego los eventos de los botones confirmar y volver:
        document.getElementById("btnConfirmarPedido").addEventListener("click", () => {
            let objConfirmarPedido = new ConfirmacionPedido("app-entidades-getSucursalesByEntidad", true);
            let objModal = new LFWModalBS("main", "modal_confirmar_pedido", "Confirmar pedido");
            objConfirmarPedido.setIdModal(objModal.getIdModal());
            objConfirmarPedido.setDivModal(objModal.getModalBody());

            // Agrego funcionalidad extra al finalizar el pedido
            objConfirmarPedido.setCallbackFinalizarPedido(() => {
                objModal.close();

                // Volver a la pantalla de pedidos pendientes.
                //this.getPedidosPendientes();
                (new PedidosPendientes).getPedidosPendientes();
            });
            
            objConfirmarPedido.generarFooterPedido();
            objModal.open();
        });

        // Evento click del botón volver.
        document.getElementById("btnVolver").addEventListener("click", () => {
            let objPedidosPendientes = new PedidosPendientes();

            this.getTemplate((new App()).getUrlTemplate("oper-pedidos-pendientes"), html => {
                document.getElementById("app_grid_container").innerHTML = html;
                objPedidosPendientes.getPedidosPendientes(response => {
                    objPedidosPendientes.mostrarGrillaPedidosPendientes(response);
                });    
            });
        })
    }

    /**
     * Crea la botonoera del footer.
     * @returns {DOM Element}
     */
    __crearBotoneraFooter() {
        let objDivBotoneraFooter = document.createElement("div");
        let btnConfirmarPedido = document.createElement("button");
        let btnVolver = document.createElement("button");
        let objCol = this.__addBoostralColumn(["col-m-6"]);
        
        objDivBotoneraFooter.id = "pedsel-botonera-footer";
        
        btnConfirmarPedido.id = "btnConfirmarPedido";
        btnConfirmarPedido.name = "btnConfirmarPedido";
        btnConfirmarPedido.type = "button";
        btnConfirmarPedido.classList.add("btn");
        btnConfirmarPedido.classList.add("btn-primary");
        btnConfirmarPedido.classList.add("mt-1");
        btnConfirmarPedido.innerHTML = "<i class=\"fa-regular fa-circle-check\"></i> Confirmar";
        objDivBotoneraFooter.appendChild(btnConfirmarPedido);

        btnVolver.id = "btnVolver";
        btnVolver.name = "btnVolver";
        btnVolver.type = "button";
        btnVolver.classList.add("btn");
        btnVolver.classList.add("btn-secondary");
        btnVolver.classList.add("ms-1");
        btnVolver.classList.add("mt-1");
        btnVolver.innerHTML = "<i class=\"fa-solid fa-arrow-left\"></i> Volver";
        objDivBotoneraFooter.appendChild(btnVolver);

        objCol.appendChild(objDivBotoneraFooter);

        return objCol;
    }

    /**
     * Abre le modal para editar un ítem de la grilla
     * @param {int} xidpedido_item 
     */
    editarItem(xidpedido_item) {
        let objModal = new LFWModalBS(
            "main",
            "modal-edit-item", 
            "Editar ítem", 
            this.__crearFormEdit(xidpedido_item),
            "500px", 
            "Grabar", 
            () => {
                // El siguiente código permite realizar la modificación del ítem en la base de datos
                // al hacer click en el botón grabar del modal.

                let aArticulo = this.__getItemByIdFromCache(xidpedido_item);
                aArticulo[0]["cantidad"] = parseFloat(document.getElementById("txtCantidad").value);

                (new PedidosAPI).modificarItem(aArticulo, (response) => {
                        // Cuando refresco la cache de los pedidos pendientes, para refrescar la grilla
                        // de los ítems modificados lo hago en callback por un tema de asincornicidad de
                        // javascript.
                        let objPedidosPendientes = new PedidosPendientes();
                        objPedidosPendientes.getPedidosPendientes((result) => {
                        this.entrarAlPedido(parseInt(aArticulo[0]["id_pedido"]));
                    });                
                });
            });

        objModal.open();
    }

    /**
     * Crea el formulario de edición de ítems.
     * @param {int} xidpedido_item Id. del ítem seleccionado
     * @returns 
     */
    __crearFormEdit(xidpedido_item) {
        let objForm = document.createElement("div");
        let objCampoCodigo = new HTMLInput("txtCodigo", "Código");
        let objCampoDescripcion = new HTMLInput("txtDescripcion", "Descripción");
        let objCampoCantidad = new HTMLInput("txtCantidad", "Cantidad");
        let articulo = this.__getItemByIdFromCache(xidpedido_item);
        
        objCampoCodigo.setReadOnly();
        objCampoCodigo.setWidth(300);
        objCampoDescripcion.setReadOnly();
        objCampoDescripcion.setWidth(450);
        objCampoCantidad.setDataType("float");
        objCampoCantidad.setWidth(100);

        objCampoCodigo.setValue(articulo[0]["codigo"]);
        objCampoDescripcion.setValue(articulo[0]["descripcion"]);
        objCampoCantidad.setValue(articulo[0]["cantidad"]);
        
        objForm.appendChild(objCampoCodigo.toHtml());
        objForm.appendChild(objCampoDescripcion.toHtml());
        objForm.appendChild(objCampoCantidad.toHtml());
        return objForm;
    }

    /**
     * Obtiene el ítem seleccionado desde la cache de la grilla.
     * @param {int} xidpedido_item 
     * @returns {array}
     */
     __getItemByIdFromCache(xidpedido_item) {
        let items = JSON.parse(sessionStorage.getItem((new CacheUtils("derweb", false)).get("grid-items-cache-name")));
        let articulos = items.rows;
        return articulos.filter(xelement => {
            if (parseInt(xelement.id) === parseInt(xidpedido_item))
                return true;
        });
    }

}