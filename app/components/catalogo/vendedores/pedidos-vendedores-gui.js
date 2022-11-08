/**
 * Clase: PedidosVendedoresGUI
 * Descripción:
 *  Permite gestionar los pedidos de los clientes tanto para comerciales como
 *  para televentas.
 */

class PedidosVendedoresGUI extends ComponentManager {
    constructor() {
        super();
    }

    /**
     * Obtiene los pedidos pendientes del vendedor actuamente logueado.
     */
    getPedidosPendientes(xrefreshCacheOnly = false, xcallback) {
        let objPedidosPendientes = new PedidosPendientes();
        objPedidosPendientes.getPedidosPendientes(response => {
            if (!xrefreshCacheOnly)
                objPedidosPendientes.mostrarGrillaPedidosPendientes(response);
            
            if (typeof xcallback === "function")
                xcallback(response);
        });
    }

    /**
     * Permite ingresar a la operación de pedidos rápidos.
     */
    ingresar_pedidos_rapidos() {
        let objIngresoPedidosRapido = new IngresoPedidosRapidoGUI("app-container");
        objIngresoPedidosRapido.generateComponent();    
    }
}