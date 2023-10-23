class Detalle_MisPedidos extends ComponentManager {
    constructor(idContainer, id_pedido) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    generateComponent() {
        this.nodoContainer.innerHTML = id_pedido;

    }

    __getPedido(desde, hasta) {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("pedidos");
            const miSession = new CacheUtils('derweb').get('sesion');
            const fechaDesde = desde;
            const fechaHasta = hasta;
            const argumento = { "id_sucursal": miSession.id_sucursal, "fecha_desde": fechaDesde, "fecha_hasta": fechaHasta }
            new APIs().call(url, argumento, "POST", pedidos => {
                resolve(pedidos);
            }, true, error => reject(error))
        })
    }
}