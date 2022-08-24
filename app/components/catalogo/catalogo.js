/**
 * Clase Catalogo para dar funcionalidad al módulo del catálogo.
 */

class Catalogo {

    constructor() {
        this._objApp = new App();
    }

    /**
     * Permite obtener las marcas de repuestos.
     * @param {string} xfilter Permite establecer la condición del where para filtrar registros
     * @returns {array}
     */
    getMarcas(xfilter = "") {
        var objApi = new APIs();
        var aMarcas = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-marcas-get"), xfilter);;
        return aMarcas;
    }

    /**
     * Permite levantar los datos de la tabla rubros.
     * @param {string} xfilter Establece la condición del where para filtrar registros.
     * @returns {array}
     */
    getRubros(xfilter = "") {
        var objApi = new APIs();
        var aRubros = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-rubros-get"), xfilter);;
        return aRubros;
    }

    /**
     * Permite obtener los subrubros.
     * @param {string} xfilter Permite establecer la condición del where para filtrar registros
     * @returns {array}
     */
    getSubrubros(xfilter = "") {
        var objApi = new APIs();
        var aSubrubros = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-subrubros-get"), xfilter);
        return aSubrubros;
    }

    /**
     * Obtiene los subrubros a partir de un rubro
     * @param {int} xid_rubro
     * @returns {array}
     */
    getSubrubrosByRubro(xid_rubro) {
        var objApi = new APIs();
        var filter = "id_rubro=" + xid_rubro;
        var aSubrubros = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-subrubros-getByRubro") + "?" + filter);
        return aSubrubros;
    }
}