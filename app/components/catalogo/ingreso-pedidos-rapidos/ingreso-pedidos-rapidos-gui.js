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
                objDataList.toHtml(html => {
                    htmlResponse = this.setTemplateParameters(htmlResponse, "lfw-datalist-bs", html);
                    document.getElementById(this.__idContainer).innerHTML = htmlResponse;

                    document.getElementById(objDataList.idSelector).addEventListener("change", () => {
                        console.log(objDataList.getSelectedValue());
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
}