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
        objDataList.setColumns(["codsuc", "nombre"]);
        objDataList.setColumnsKey(["id", "id_sucursal"]);
        objDataList.toHtml(html => {
            // Lleno el autocomplete y dibujo el HTML en el navegador.
            xhtmlResponse = this.setTemplateParameters(xhtmlResponse, "lfw-datalist-bs", html);
            document.getElementById(this.__idContainer).innerHTML = xhtmlResponse;

            this.__inicializarInputs();

            // Agrego el evento blur del selector de clientes.
            document.getElementById(objDataList.idSelector).addEventListener("blur", (event) => {
                objDataList.getSelectedValue(event.target.value);
            });
            
            // Agrego el evento blur de txtCodArt
            document.getElementById("txtCodArt").addEventListener("blur", () => {
                // Al salirse del foco realizo una búsqueda inicial.
                if (!this.__validarSeleccionCliente())
                    return;

                this.__buscarArticulo();
            });

            // Evento al recibir el foco.
            document.getElementById("txtCantidad").addEventListener("focus", () => {
                // Selecciono el contenido del input.
                document.getElementById("txtCantidad").select();
            });

            // Agrego el evento cantidad.
            document.getElementById("txtCantidad").addEventListener("blur", () => {
                this.__agregarItem();
            });

            // LLamo al método que crea la grilla.
            this.__crearGridItems();

            document.getElementById("sel-cliente").focus();
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
        
        aClienteSeleccionado = JSON.parse(document.getElementById("sel-cliente").dataset.value);
        aSesion["id_cliente"] = aClienteSeleccionado["id"];
        aSesion["id_sucursal"] = aClienteSeleccionado["id_sucursal"];
        sesion = "sesion=" + JSON.stringify(aSesion);

        (new APIs()).call(url, sesion + "&pagina=0&" + filter, "GET", response => {
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
        if (!this.__modalBusquedaAbierto) {
            let objModal = new LFWModalBS(
                "main", 
                "modal_articulos", 
                "Búsqueda de artículos",
                "<div id='ipr_grid_articulos'></div>",
                "700px");
            
            objModal.open();
            this.__modalBusquedaAbierto = true;
            this.__crearGrillaArticulos();

            // Agrego evento al hacer clic en cerrar del modal. Al cerrar marco el estado del modal a 
            // cerrado.
            document.getElementById("modal_articulos_btnclose").addEventListener("click", () => {
                this.__modalBusquedaAbierto = false;
                objModal.close();
            });
        }

        (new APIs().call(xurl, xsesion + "&pagina=" + xpagina + "&" + xfilter, "GET", 
            response => {
                if (response.values.length !== 0) {
                    xpagina += 40;
                    this.__buscarArticuloEnGrilla(xurl, xsesion, xpagina, xfilter);
                }   this.__llenarGrillaArticulos(response.values);
            }));        
    }

    /**
     * Permite crear la grilla de artículos.
     */
    __crearGrillaArticulos() {
        this.__objGridArticulos = new LFWDataGrid("ipr_grid_articulos", "id");
        this.__objGridArticulos.setAsociatedFormId("frm-ingreso-pedido-rapido");
        this.__objGridArticulos.setPermitirFiltros(true);
        this.__objGridArticulos.setPermitirOrden(true);
        this.__objGridArticulos.setPermitirEditarRegistro(true);
        this.__objGridArticulos.setEditButtonTitle("Seleccionar")
        this.__objGridArticulos.setEditJavascriptFunctionName("seleccionar_articulo")
        this.__objGridArticulos.setIconEditButton("fa-arrow-right-to-bracket");

        this.__objGridArticulos.agregarColumna("ID", "id", "numeric", 0, false);
        this.__objGridArticulos.agregarColumna("Código", "codigo", "string", 100);
        this.__objGridArticulos.agregarColumna("Descripción", "desc", "string");
    }

    /**
     * Permite llenar la grilla de artículos.
     * @param {array} Array Artículos a llenar en la grilla.
     */
    __llenarGrillaArticulos(xrows) {
        xrows.forEach(element => {
            this.__objGridArticulos.agregarFila(element);
        });

        this.__objGridArticulos.refresh();
    }

    /**
     * Crea la grilla de ítems del pedido.
     */
    __crearGridItems() {
        this.__objDataGrid = new LFWDataGrid("ipr_grid_items", "id")
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
        this.__objDataGrid.agregarColumna("Subtotal", "subtotal", "numeric");
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

        item = {
            "id": 0,
            "codart": articulo.values[0].codigo,
            "descripcion": articulo.values[0].desc,
            "cantidad": parseFloat(document.getElementById("txtCantidad").value),
            "precio_unitario": parseFloat(articulo.values[0].cped).toFixed(2),
            "subtotal": (articulo.values[0].cped * parseInt(document.getElementById("txtCantidad").value)).toFixed(2)
        };

        this.__objDataGrid.agregarFila(item);
        this.__objDataGrid.refresh();
        this.__calcularTotalPedido();
        this.__blanquearInputsItems();
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