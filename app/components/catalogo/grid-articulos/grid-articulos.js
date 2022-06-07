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

    _getData() {
        var pagina = 0;
        /*while (!this._getArticulos(pagina, "derweb_articulos_" + pagina)) {
            console.log("Procesando pagina " + pagina);
            pagina += 40;
        }*/
        this._getArticulos(pagina, "derweb_articulos_" + pagina)
    }

    /// Analizar bien la recursividad con promesas.
    _getArticulos(xpagina, xclaveSessionStorage, xEOF) {
        var url = this.aParametros["api_url"];
        var url_con_parametros = url + "?sesion=" + sessionStorage.getItem("derweb_sesion")
            + "&parametros=" + JSON.stringify(this.aParametros) + "&pagina=" + xpagina;

        if (!xEOF) {
            getAPI(url_con_parametros, (response) => {
                let aDatos = JSON.parse(response);
                console.log(aDatos["values"]);

                xEOF = false;
                if (aDatos["values"].length === 0)
                    xEOF = true
                else
                    sessionStorage.setItem(xclaveSessionStorage, JSON.stringify(aDatos));
                aDatos = null; 
            }, true);

            xpagina += 40;
            xEOF = this._getArticulos(xpagina, xclaveSessionStorage, xEOF)
        }

        return xEOF;
    }

    /**
     * Permite generar el componente datagrid
     */
    generateComponent (xidAppContainer) {
        var objAppContainer = document.getElementById(xidAppContainer);
        objAppContainer.appendChild(this._objGridContainer);
    }
}