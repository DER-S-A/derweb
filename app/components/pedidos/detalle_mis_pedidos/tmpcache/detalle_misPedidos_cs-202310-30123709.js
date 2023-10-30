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
                const razonSocial = this.objCache.get("sesion").nombre;
                const nombreSuc = this.objCache.get("sesion").nombre_suc;
                html = this.setTemplateParameters(html, "razonSocial", razonSocial);
                html = this.setTemplateParameters(html, "nombreSuc", nombreSuc);
                html = this.setTemplateParameters(html, "total", this.totalPedido);
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "15%";
                const pedidoDet = await this.__getPedido();
                console.log(pedidoDet)
                this.__generarTabla(pedidoDet);
            });
        } catch (error) {
            console.error("Error en generateComponent:", error);
        }

    }

    __getPedido() {
        return new Promise((resolve, reject) => {
            const url = this.objApp.getUrlApi("pedidos_detalle");
            const argumento = { "id_pedido": this.id_pedido }
            new APIs().call(url, argumento, "POST", pedidos => {
                resolve(pedidos);
            }, true, error => reject(error))
        })
    }

    __generarTabla(pedidoDet) {
        let dataTabla;
        // Verifica si no existe una instancia de DataTable en la tabla
        if (!$.fn.DataTable.isDataTable('#contenedor-tabla-misPedidos_detalle')) {
            // Si no existe, la instancio
            dataTabla = $("#contenedor-tabla-misPedidos_detalle").DataTable({
                searching: true,
                paging: true,
                responsive: true,
                scrollY: 260
            });
        } else dataTabla = $("#contenedor-tabla-misPedidos_detalle").DataTable();
        dataTabla.clear();
        //const aParaTabla = this.__procesarDatosParaTabla(pedidos);
        pedidoDet.forEach(element => {
            const jsonPed = JSON.stringify(element);
            const detalleLink = `<a href="javascript:detallePedido('${encodeURIComponent(jsonPed)}')">VER DETALLES</a>`;
            const data = [element.id_pedido, element.fecha_alta, element.estado, detalleLink];
            dataTabla.row.add(data);
        });
        dataTabla.draw();
    }
}