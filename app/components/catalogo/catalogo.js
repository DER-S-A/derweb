/**
 * Clase Catalogo para dar funcionalidad al módulo del catálogo.
 */

class Catalogo {
    /**
     * Permite obtener las marcas de repuestos.
     * @param {string} xfilter Permite establecer la condición del where para filtrar registros
     * @returns {array}
     */
    getMarcas(xfilter = "") {
        var aMarcas = (new APIs()).getFromAPI((new App()).getUrlApi("catalogo-marcas-get"), xfilter);;
        return aMarcas;
    }

    /**
     * Permite levantar los datos de la tabla rubros.
     * @param {string} xfilter Establece la condición del where para filtrar registros.
     * @returns {array}
     */
    getRubros(xfilter = "") {
        var aRubros = (new APIs()).getFromAPI((new App()).getUrlApi("catalogo-rubros-get"), xfilter);;
        return aRubros;
    }

    /**
     * Permite obtener los subrubros.
     * @param {string} xfilter Permite establecer la condición del where para filtrar registros
     * @returns {array}
     */
    getSubrubros(xfilter = "") {
        var aSubrubros = (new APIs()).getFromAPI((new App()).getUrlApi("catalogo-subrubros-get"), xfilter);
        console.log(aSubrubros);
        return aSubrubros;
    }

    /**
     * Obtiene los subrubros a partir de un rubro
     * @param {int} xid_rubro
     * @returns {array}
     */
    getSubrubrosByRubro(xid_rubro) {
        var filter = "id_rubro=" + xid_rubro;
        var aSubrubros = (new APIs()).getFromAPI((new App()).getUrlApi("catalogo-subrubros-getByRubro") + "?" + filter);
        return aSubrubros;
    }

    /**
     * Recupera los datos de la sucursal predeterminada por id de cliente.
     * @param {int} xidCliente Id. cliente (Pasar id de la tabla entidades)
     * @param {callback} xcallback Función callback para recibir los datos.
     */
    getSucursalPredeterminadaByCliente(xidCliente, xcallback) {
        let urlSuc = (new App()).getUrlApi("app-entidades-sucursales");
        let filtros = "\"id_entidad = " + parseInt(xidCliente)
                + " AND predeterminado = 1\"";

        (new APIs()).call(urlSuc, "filter=" + filtros, "GET", (xdatos) => {
            xcallback(xdatos);
        });
    }

    /**
     * Agrega el artículo al pedido recuperando los datos que se requieren del mismo.
     * @param {array} xaSesion Array con los datos de la sesión actual.
     * @param {int} xidarticulo Id. del artículo seleccionado.
     * @param {float} xcantidad Cantidad pedida del artículo seleccionado.
     * @param {array} xaCabecera Array con la cabecera.
     */
    agregarArticuloEnCarrito(xaSesion, xidarticulo, xcantidad, xaCabecera) {
        // Recupero los datos del artículo que necesito.
        this.getArticuloById(xaSesion, xidarticulo, (xarticulo) => {
            let aArticulo = {
                "id_articulo": parseInt(xidarticulo),
                "cantidad": parseFloat(xcantidad),
                "procentaje_oferta": 0.00,
                "precio_lista": xarticulo["values"][0]["prlista"],
                "costo_unitario": 0,
                "alicuota_iva": xarticulo["values"][0]["iva"]
            };

            this.__grabarArticuloEnCarrito(xaSesion, xaCabecera, aArticulo);
        });
    }

    /**
     * Obtiene los datos de un artículo por su Id.
     * @param {array} xaSesion Sesión iniciada
     * @param {int} xidarticulo Id. de artículo
     * @param {callback} xcallback Función para recibir los datos.
     */
     getArticuloById(xaSesion, xidarticulo, xcallback) {
        let url_articulo = (new App()).getUrlApi("catalogo-articulos-get");
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
    __grabarArticuloEnCarrito(xaSesion, xaCabecera, xarticulo) {
        let url_carrito = (new App()).getUrlApi("catalogo-pedidos-agregarAlCarrito");
        let parametros = new Array();
    
        // Armo la estructura de JSON para enviar al API.
        parametros = {
            "cabecera": {
                "_comment": "Los campos subtotal, importe_iva y total los dejo en cero porque se calculan en el API. Solo necesito la definición.",
                "id_entidad": xaCabecera["id_cliente"],
                "id_tipoentidad": xaCabecera["id_tipoentidad"],
                "id_estado" : 1,
                "id_vendedor": xaCabecera["id_vendedor"],
                "id_sucursal": this.__getIdSucursal(xaSesion, xaCabecera),
                "codigo_sucursal": xaCabecera["codigo_sucursal"],
                "id_televenta": 0,
                "id_transporte": xaCabecera["id_transporte"],
                "codigo_transporte": xaCabecera["codigo_transporte"],
                "id_formaenvio": xaCabecera["id_formaenvio"],
                "codigo_forma_envio": xaCabecera["codigo_forma_envio"],
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

    /**
     * Obtiene el Id. de Sucursal en base a quién esté logueado.
     * @param {array} $xaSesion Array de inicio de sesión.
     * @param {array} xaCabecera Array de cabecera.
     * @returns {int} Devuelve el Id. de sucursal.
     */
    __getIdSucursal($xaSesion, xaCabecera) {
        if ($xaSesion["tipo_login"] === 'C')
            return parseInt($xaSesion["id_sucursal"]);
        else
            return parseInt(xaCabecera["id_sucursal"]);
    }
}