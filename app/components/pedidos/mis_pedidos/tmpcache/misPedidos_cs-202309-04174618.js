class MisPedidos extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    async generateComponent() {
        try {
            const pedidos =  await this.__getPedidos();
            /*const oferta = await this.__getOfertas();
            this.getTemplate(new App().getUrlTemplate("centroNoticias"), html => {
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "20%";
                console.log(oferta);
                this.__controlEventos(novedades, oferta);
                this.__generarCarruselNov(novedades);
                //this.__generarCarruselOff(oferta);
            });*/
        }
        catch(error) {
            console.error("Error en generateComponent:", error);
        }
    }

    __getPedidos() {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("pedidos");
            const miSession = new CacheUtils('derweb').get('sesion');
            const fechaDesde = '2023-08-13';
            const fechaHasta = '2023-09-4';
            const argumento = {"id_sucursal":miSession.id_sucursal, "fecha_desde":fechaDesde, "fecha_hasta":fechaHasta}
            new APIs().call(url, argumento, "POST", pedidos => {
                console.log(pedidos);
                resolve(pedidos);
            }, true, error => reject(error))
        })
    }
}