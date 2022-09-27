/**
 * Clase principal de la aplicación
 */

const _APPNAME = "derweb";

// Referencias a javascript de bootstrap
const bootstrapJS = [
    "node_modules/@popperjs/core/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.min.js",
    "node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"
];

// Referencias a URL de APIs (Comunicación con el backend)
const aAPIs = [
    {"login"                                    : "services/entidades.php/loginCliente" },
    {"registrar-cliente"                        : "services/cliente-potencial.php/registrarCliente"},
    {"app-operaciones-getByTipoEntidad"         : "services/lfw-operaciones.php/getByTipoEntidad"},
    {"app-banner-portada"                       : "services/banner_portada.php/get"},
    {"app-entidades-get"                        : "services/entidades.php/get"},
    {"app-entidades-getSucursalesByEntidad"     : "services/entidades.php/getSucursalesByEntidad"},
    {"app-entidades-getClientesByVendedor"      : "services/entidades.php/getClientesByVendedor"},
    {"app-entidades-cambiarClave"               : "services/entidades.php/cambiarPassword"},
    {"app-entidades-sucursales"                 : "services/sucursales.php/get"},
    {"app-forma-envio"                          : "services/formas-envios.php/getMiCarrito"},
    {"app-transportes"                          : "services/transportes.php/get"},
    {"catalogo-marcas-get"                      : "services/marcas.php/get"},
    {"catalogo-rubros-get"                      : "services/rubros.php/get"},
    {"catalogo-subrubros-get"                   : "services/subrubros.php/get"},
    {"catalogo-subrubros-getByRubro"            : "services/subrubros.php/getSubrubrosPorRubro"},
    {"catalogo-articulos-getByRubroAndSubrubro" : "services/articulos.php/getByRubroAndSubrubro"},
    {"catalogo-articulos-getByFranse"           : "services/articulos.php/getByFrase"},
    {"catalogo-articulos_destacados-get"        : "services/articulos-destacados.php/get"},
    {"catalogo-articulos-get"                   : "services/articulos.php/get"},
    {"catalogo-pedidos-getPedidoActual"         : "services/pedidos.php/getPedidoActual"},
    {"catalogo-pedidos-agregarAlCarrito"        : "services/pedidos.php/agregarAlCarrito"},
    {"catalogo-pedidos-vaciarCarrito"           : "services/pedidos.php/vaciarCarrito"},
    {"catalogo-pedidos-confirmarPedido"         : "services/pedidos.php/confirmarPedido"},
    {"catalogo-pedidos-getPendientesByVendedor" : "services/pedidos.php/getPedidosPendientesByVendedor"},
    {"catalogo-pedidos-modificar-items"         : "services/pedidos.php/modificarItem"}
]

class App {
    /**
     * Inicializa la aplicación
     */
    init() {
        bootstrapJS.forEach((xelement => {
            this.__addScriptToHead(xelement);
        }));
    }

    /**
     * Agrega referencias javascript al a cabecera
     */
    __addScriptToHead(xelement) {
        let domScript = document.createElement("script");
        domScript.src = xelement;
        domScript.type = "text/javascript";
        document.head.appendChild(domScript);            
    }

    /**
     * Permite recuperar la URL de una API desde el array de configuración
     * @param {string} xkey 
     * @returns 
     */
    getUrlApi(xkey) {
        for (let i = 0; i < aAPIs.length; ++i)
            if (aAPIs[i][xkey] !== undefined)
                return aAPIs[i][xkey];
    }
}
