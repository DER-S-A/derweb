/**
 * Clase: PedidosAPI
 * Descripción:
 *  Esta clase se encarga de gestionar el consumo de apis correspondiente al módulo de pedidos.
*/

class PedidosAPI {
    /**
     * Permite obtener los pedidos pendientes por vendedor.
     * @param {Callback function} xcallback 
     */
    getPendientesByVendedor(xcallback) {
        let url = (new App()).getUrlApi("catalogo-pedidos-getPendientesByVendedor");
        let parametros = "sesion=" + sessionStorage.getItem("derweb_sesion");
    
        (new APIs()).call(url, parametros, "GET", response => {
            xcallback(response);
        });
    }

    /**
     * Permite modificar un ítem.
     * @param {array} aDatos Array con los datos del ítem a modificar.
     * @param {Callback function} xcallback
     */
    modificarItem(aDatos, xcallback) {
        let urlAPI = (new App()).getUrlApi("catalogo-pedidos-modificar-items");
        (new APIs()).call(urlAPI, "data=" + JSON.stringify(aDatos[0]), "PUT", (response) => {
            xcallback(response);
        });
    }
}