/**
 * Clase: PedidosPendientes
 * Descripción:
 *  Esta clase contiene la operación de pedidos pendientes.
 */

class PedidosPendientes extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");

        this.getTemplate((new App()).getUrlTemplate("oper-pedidos-pendientes"), html => {
            document.getElementById("app-container").innerHTML = html;
        });
    }

    /**
     * Obtiene los pedidos pendientes de confirmar.
     * @param {callback} xcallback 
     */
    getPedidosPendientes(xcallback) {
        (new PedidosAPI()).getPendientesByVendedor((response) => {
            this.__guardarPedidosPendientesEnCache(response);
            xcallback(response);
        });
    }

    /**
     * Permite mostrar la grilla de pedidos pendientes.
     * @param {array} xdatos 
     */
     mostrarGrillaPedidosPendientes(xdatos) {
        document.getElementById("app_grid_container").innerHTML = "";
        this.eliminarFooter();

        let objGrid = new LFWDataGrid("app_grid_container", "id");
    
        objGrid.setAsociatedFormId("formulario");
        objGrid.setPermitirOrden(true);
        objGrid.setPermitirFiltros(true);
        objGrid.setPermitirEditarRegistro(true);
        objGrid.setEditJavascriptFunctionName("entrar_al_pedido");
        objGrid.setIconEditButton("fa-arrow-right-to-bracket");
        objGrid.setEditButtonTitle("Entrar al pedido");
    
        objGrid.agregarColumna("Pedido N°", "id", "numeric");
        objGrid.agregarColumna("Fecha", "fecha_alta", "string");
        objGrid.agregarColumna("Cliente", "cliente_cardcode", "numeric");
        objGrid.agregarColumna("Razón Social", "nombre");
        objGrid.agregarColumna("Total", "total", "numeric");

        if (xdatos.length !== 0) {
            xdatos.forEach(xelement => {
                objGrid.agregarFila(xelement);
            });
            objGrid.refresh();
        }
    }

    /**
     * Elimina el footer
     */
    eliminarFooter() {
        console.log(document.getElementById("pedsel-row-footer"));
        if (document.getElementById("pedsel-row-footer") !== null)
            document.getElementById("app-container").removeChild(document.getElementById("pedsel-row-footer"));
    }

    /**
     * Guarda el listado de pedidos pendientes actuales en cache.
     * @param {array} xdatos 
     */
     __guardarPedidosPendientesEnCache(xdatos) {
        let objCache = new CacheUtils(_APPNAME, false);
        objCache.set("pedido-actual", xdatos);
    }    
}