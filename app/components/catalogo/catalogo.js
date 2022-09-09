/**
 * Clase Catalogo para dar funcionalidad al módulo del catálogo.
 */

class Catalogo {

    constructor() {
        this._objApp = new App();
    }

    /**
     * Permite obtener las marcas de repuestos.
     * @param {string} xfilter Permite establecer la condición del where para filtrar registros
     * @returns {array}
     */
    getMarcas(xfilter = "") {
        var objApi = new APIs();
        var aMarcas = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-marcas-get"), xfilter);;
        return aMarcas;
    }

    /**
     * Permite levantar los datos de la tabla rubros.
     * @param {string} xfilter Establece la condición del where para filtrar registros.
     * @returns {array}
     */
    getRubros(xfilter = "") {
        var objApi = new APIs();
        var aRubros = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-rubros-get"), xfilter);;
        return aRubros;
    }

    /**
     * Permite obtener los subrubros.
     * @param {string} xfilter Permite establecer la condición del where para filtrar registros
     * @returns {array}
     */
    getSubrubros(xfilter = "") {
        var objApi = new APIs();
        var aSubrubros = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-subrubros-get"), xfilter);
        return aSubrubros;
    }

    /**
     * Obtiene los subrubros a partir de un rubro
     * @param {int} xid_rubro
     * @returns {array}
     */
    getSubrubrosByRubro(xid_rubro) {
        var objApi = new APIs();
        var filter = "id_rubro=" + xid_rubro;
        var aSubrubros = objApi.getFromAPI(this._objApp.getUrlApi("catalogo-subrubros-getByRubro") + "?" + filter);
        return aSubrubros;
    }

    /**
     * Recupera los datos de la sucursal predeterminada por id de cliente.
     * @param {int} xidCliente Id. cliente (Pasar id de la tabla entidades)
     * @param {callback} xcallback Función callback para recibir los datos.
     */
    getSucursalPredeterminadaByCliente(xidCliente, xcallback) {
        let objApp = new App();
        let objAPI = new APIs();
        let urlSuc = objApp.getUrlApi("app-entidades-sucursales");
        let filtros = "\"id_entidad = " + parseInt(xidCliente)
                + " AND predeterminado = 1\"";

        (new APIs()).call(urlSuc, "filter=" + filtros, "GET", (xdatos) => {
            xcallback(xdatos);
        });
    }

    /**
     * Obtiene los datos de un artículo por su Id.
     * @param {array} xaSesion Sesión iniciada
     * @param {int} xidarticulo Id. de artículo
     * @param {callback} xcallback Función para recibir los datos.
     */
    getArticuloById(xaSesion, xidarticulo, xcallback) {
        let objApp = new App();
        let url_articulo = objApp.getUrlApi("catalogo-articulos-get");
        let filtros = "sesion=" + JSON.stringify(xaSesion);
        filtros += "&pagina=0&filter=\"art.id = " + xidarticulo + "\"";
        
        (new APIs()).call(url_articulo, filtros, "GET", (xdatos) => {
            xcallback(xdatos);
        });
    }

    /**
     * Permite grabar un artículo en el carrito de compras.
     * @param {array} xaSesion Datos de sesión actual.
     * @param {array} xaCabecera Regitro de cabecera.
     * @param {array} xarticulo Registro de artículo
     */
    grabarArticuloEnCarrito(xaSesion, xaCabecera, xarticulo) {
        let objApp = new App();
        let url_carrito = objApp.getUrlApi("catalogo-pedidos-agregarAlCarrito");
        let parametros = new Array();
    
        // Armo la estructura de JSON para enviar al API.
        parametros = {
            "cabecera": {
                "_comment": "Los campos subtotal, importe_iva y total los dejo en cero porque se calculan en el API. Solo necesito la definición.",
                "id_entidad": xaCabecera["id_cliente"],
                "id_tipoentidad": xaCabecera["id_tipoentidad"],
                "id_estado" : 1,
                "id_vendedor": xaCabecera["id_vendedor"],
                "id_sucursal": xaCabecera["id_sucursal"],
                "codigo_sucursal": xaCabecera["codigo_sucursal"],
                "id_televenta": 0,
                "id_transporte": xaCabecera["id_transporte"],
                "codigo_transporte": xaCabecera["codigo_transporte"],
                "descuento_1": 0.00,
                "descuento_2": 0.00,
                "subtotal": 0.00,
                "importe_iva": 0.00,
                "total": 0.00
            },
            "item": {
                "id_articulo": xarticulo["id_articulo"],
                "cantidad": xarticulo["cantidad"],
                "porcentaje_oferta": xarticulo["porcentaje_oferta"],
                "precio_lista": xarticulo["precio_lista"],
                "costo_unitario": xarticulo["costo_unitario"],
                "alicuota_iva": xarticulo["alicuota_iva"]
            }
        };
    
        // Envío el pedido al API para grabarlo en la( base de datos.
        let argumentos = "sesion=" + JSON.stringify(xaSesion) + "&" + "datos=" + JSON.stringify(parametros);
        (new APIs()).call(url_carrito, argumentos, "PUT", (xdatos) => {
            alert(xdatos.mensaje);
        });
    }    
}