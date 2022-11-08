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
                })
            });
        });
    }

    /**
     * Permite crear la lista de datos a seleccionar para el autocomplete.
     * @param {array} xdatos Clientes del vendedor.
     * @returns {DOM Element} Retorna el objeto datalistOptions.
     */
    __crearDataListOptionClientes(xdatos) {
        let objDataListOption = document.createElement("datalist");
        let optionsValues = "";

        objDataListOption.id = this.__idSelectorClientes + "-datalistOptions";
        this.__idSelectorClientesDataList = objDataListOption.id;
        xdatos.forEach(element => {
            optionsValues += "<option value='" + element["codsuc"] + " - " + element["nombre"] + "'>";
        });

        objDataListOption.innerHTML = optionsValues;
        return objDataListOption;
    }
}