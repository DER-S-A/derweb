class MisPedidos extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    async generateComponent() {
        try {
            this.getTemplate(new App().getUrlTemplate("misPedidos"), html => {console.log(html)
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "15%";
                const aFecha = this.__recuperarFechasFiltro()
                const pedidos =  await this.__getPedidos(aFecha[0], aFecha[1]);
                console.log(pedidos);
                //this.__controlEventos(novedades, oferta);
                //this.__generarCarruselNov(novedades);
                //this.__generarCarruselOff(oferta);
            });
        }
        catch(error) {
            console.error("Error en generateComponent:", error);
        }
    }

    __getPedidos(desde, hasta) {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("pedidos");
            const miSession = new CacheUtils('derweb').get('sesion');
            const fechaDesde = desde;
            const fechaHasta = hasta;
            const argumento = {"id_sucursal":miSession.id_sucursal, "fecha_desde":fechaDesde, "fecha_hasta":fechaHasta}
            new APIs().call(url, argumento, "POST", pedidos => {
                resolve(pedidos);
            }, true, error => reject(error))
        })
    }

    __recuperarFechasFiltro() {
        const buscar = document.getElementById('buscar_misPedidos');
        let aFechas = [];
        buscar.addEventListener('click', () => {
            const fechaD = document.getElementById('desde').value;
            const fechaH = document.getElementById('hasta').value;
            aFechas.push(fechaD, fechaH);
        });
        console.log(aFechas);
        return aFechas; 
    }
}