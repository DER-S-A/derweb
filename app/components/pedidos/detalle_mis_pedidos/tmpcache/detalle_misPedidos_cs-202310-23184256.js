class Detalle_MisPedidos extends ComponentManager {
    constructor(idContainer, id_pedido) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
        this.id_pedido = id_pedido;
    }

    generateComponent() {
        this.nodoContainer.innerHTML = "";
        this.__getPedido();

    }

    __getPedido() {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("pedidos_detalle");
            const argumento = { "id_pedido": this.id_pedido }
            new APIs().call(url, argumento, "POST", pedidos => {
                console.log(pedidos)
                resolve(pedidos);
            }, true, error => reject(error))
        })
    }
}