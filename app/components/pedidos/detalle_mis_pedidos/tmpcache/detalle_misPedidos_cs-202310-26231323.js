class Detalle_MisPedidos extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
        this.objCache = new CacheUtils("derweb");
        this.id_pedido = this.objCache.get('detalleMiPed').id_pedido;
        this.totalPedido = this.objCache.get('detalleMiPed').total;
        this.objApp = new App();
    }

    generateComponent() {
        this.nodoContainer.innerHTML = "";
        try {
            this.getTemplate(this.objApp.getUrlTemplate("misPedidos_detalle"), async html => {
                this.objCache = new CacheUtils("derweb");
                const razonSocial = objCache.get("sesion").nombre;
                const nombreSuc = objCache.get("sesion").nombre_suc;
                html = this.setTemplateParameters(html, "razonSocial", razonSocial);
                html = this.setTemplateParameters(html, "nombreSuc", nombreSuc);
                html = this.setTemplateParameters(html, "total", this.totalPedido);
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "15%";
            });
        } catch (error) {
            
        }

    }

    __getPedido() {
        return new Promise((resolve, reject) => {
            const url = this.objApp.getUrlApi("pedidos_detalle");
            const argumento = { "id_pedido": this.id_pedido }
            new APIs().call(url, argumento, "POST", pedidos => {
                console.log(pedidos)
                resolve(pedidos);
            }, true, error => reject(error))
        })
    }
}