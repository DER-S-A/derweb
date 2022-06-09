/**
 * Clase CatalogoGridComponent
 * Descripción:
 *  Esta clase contiene el componente grilla para el catálogo teniendo en cuenta
 *  sus funcionalidades.
 */

class CatalogoGridComponent extends ComponentManager {
    /**
     * Creo el contenedor general de la grilla.
     * @param {string} xidgrid Id de contenedor de grilla. 
     *  Formato:    { 
     *                  api_url : "Establece la URL de la API a consultar", 
     *                  tipo_busqueda: "tipo_de_busqueda", 
     *                  values: [
     *                      { id_rubro: "Id. de rubro"},
     *                      { id_subrubro: "Id. de de subrubro"},
     *                  ]
     *              }
     * @param {array} xparametros Array JSON con los parámetros de búsqueda.
     */
    constructor (xidgrid, xparametros) {
        super();
        this.aParametros = xparametros;
        this.finLista = false;

        this._objGridContainer = document.createElement("div");
        this._objGridContainer.id = xidgrid.id;
        this._objGridContainer.classList.add("grid-container");
        this._aDatos = [];

        this._getData();
    }

    /**
     * Permite recupera los datos a mostrar en la grilla.
     */
    _getData() {
        var pagina = 0;
        this._getArticulosByRubroAndSubrubro(pagina, "derweb_articulos")
    }

    /**
     * Permite recuperar página por página el resultado de artículos en forma
     * asincrónica.
     * @param {int} xpagina Número de página a recuperar
     * @param {string} xclaveSessionStorage Clave de almacenamiento para sessionStorage.
     */
    _getArticulosByRubroAndSubrubro(xpagina, xclaveSessionStorage) {
        var url = this.aParametros["api_url"];
        var url_con_parametros = url + "?sesion=" + sessionStorage.getItem("derweb_sesion")
            + "&parametros=" + JSON.stringify(this.aParametros) + "&pagina=" + xpagina;

        fetch (url_con_parametros)
            .then(xresponse => xresponse.json())
            .then(xdata  => {
                if (xdata["values"].length !== 0) {
                    sessionStorage.setItem(xclaveSessionStorage + "_" + xpagina, JSON.stringify(xdata));
                    xpagina += 40;
                    this._getArticulosByRubroAndSubrubro(xpagina, xclaveSessionStorage);         
                }
            });
    }

    /**
     * Permite generar el componente datagrid
     */
    generateComponent (xidAppContainer) {
        var objAppContainer = document.getElementById(xidAppContainer);
        objAppContainer.appendChild(this._objGridContainer);
    }
}