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
    
            // Recupero los clientes para el vendedor actual y armo el selector.
            (new APIs()).call(urlAPI, "id_vendedor=" + idVendedor, "GET", response => {
                let txtCodArt = new HTMLInput("txtCodArt", "Artículo");
                let txtDescripcion = new HTMLInput("txtDescripcion", "Descripción");
                let txtCantidad = new HTMLInput("txtCantidad", "Cantidad");
                let objDataList = new LFWDataListBS();

                objDataList.setIdSelector("sel-cliente");
                objDataList.setIdDataListOptions("sel-cliente-options");
                objDataList.setEtiqueta("Cliente:");
                objDataList.setPlaceholderText("Tipee el nombre del cliente...");
                objDataList.setData(response);
                objDataList.setColumns(["codsuc", "nombre"]);
                objDataList.setColumnsKey(["id", "id_sucursal"]);
                objDataList.toHtml(html => {
                    htmlResponse = this.setTemplateParameters(htmlResponse, "lfw-datalist-bs", html);
                    document.getElementById(this.__idContainer).innerHTML = htmlResponse;

                    // Agrego el evento blur del selector.
                    document.getElementById(objDataList.idSelector).addEventListener("blur", (event) => {
                        objDataList.getSelectedValue(event.target.value);
                    });
                    
                    // Agrego el evento blur de txtCodArt
                    document.getElementById("txtCodArt").addEventListener("blur", () => {
                        // Al salirse del foco realizo una búsqueda inicial.
                        if (!this.__validarSeleccionCliente())
                            return;
    
                        this.__buscarArticuloPorCodigo();
                    });
                });

                // Agrego los controles input para cargar artículos.
                txtCodArt.setWidth(300);
                htmlResponse = this.setTemplateParameters(htmlResponse, "input-codigo-articulo", txtCodArt.toHtml().outerHTML);
                txtDescripcion.setWidth(500);
                txtDescripcion.setReadOnly();
                htmlResponse = this.setTemplateParameters(htmlResponse, "input-descripcion-articulo", txtDescripcion.toHtml().outerHTML);
                txtCantidad.setWidth(100);
                txtCantidad.setDataType("int");
                htmlResponse = this.setTemplateParameters(htmlResponse, "input-cantidad", txtCantidad.toHtml().outerHTML);
            });
        });
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
    __buscarArticuloPorCodigo() {
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
            }
        });
    }

    __crearGridItems() {

    }

}