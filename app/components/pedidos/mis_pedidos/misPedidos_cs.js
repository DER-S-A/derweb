class MisPedidos extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    generateComponent() {
        try {
            this.getTemplate(new App().getUrlTemplate("misPedidos"), async html => {
                const objCache = new CacheUtils("derweb");
                const razonSocial = objCache.get("sesion").nombre;
                const nombreSuc = objCache.get("sesion").nombre_suc;
                html = this.setTemplateParameters(html, "razonSocial", razonSocial);
                html = this.setTemplateParameters(html, "nombreSuc", nombreSuc);
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "15%";
                this.__controlEventos();
            });
        }
        catch (error) {
            console.error("Error en generateComponent:", error);
        }
    }

    __getPedidos(desde, hasta) {
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

    __controlEventos() {
        const buscar = document.getElementById('buscar_misPedidos');
        buscar.addEventListener('click', async () => {

            const fechaD = document.getElementById('desde').value;
            const fechaH = document.getElementById('hasta').value;
            let aFecha = [fechaD, fechaH];
            try {
                const pedidos = await this.__getPedidos(aFecha[0], aFecha[1]);
                this.__generarTabla(pedidos);
            } catch (error) {
                console.error('Error al obtener pedidos:', error);
            }
        });
    }

    __generarTabla(pedidos) {
        let dataTabla;
        // Verifica si no existe una instancia de DataTable en la tabla
        if (!$.fn.DataTable.isDataTable('#contenedor-tabla-misPedidos')) {
            // Si no existe, la instancio
            dataTabla = $("#contenedor-tabla-misPedidos").DataTable({
                searching: true,
                paging: true,
                responsive: true,
                scrollY: 260
            });
        } else dataTabla = $("#contenedor-tabla-misPedidos").DataTable();
        dataTabla.clear();
        const aParaTabla = this.__procesarDatosParaTabla(pedidos);
        aParaTabla.forEach(element => {
            const detalleLink = `<a href="javascript:detallePedido(${element.id_pedido})">VER DETALLES</a>`;
            const data = [element.id_pedido, element.fechaAlta, element.estado, detalleLink];
            dataTabla.row.add(data);
        });
        dataTabla.draw();
    }

    __procesarDatosParaTabla(pedidos) {
        return pedidos = pedidos.map(pedido => {
            return {
                id_pedido: pedido.id_pedido,
                fechaAlta: pedido.fecha_alta,
                estado: pedido.estado
            }
        });

    }
}