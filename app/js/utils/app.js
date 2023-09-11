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
    {"app-entidades-olvide_mi_contrasenia"      : "services/entidades.php/olvideMiContrasenia"},
    {"app-entidades-sucursales"                 : "services/sucursales.php/get"},
    {"app-entidades-getClientes"                : "services/entidades.php/getClientes"},
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
    {"catalogo-pedidos-eliminarItem"            : "services/pedidos.php/eliminarItem"},
    {"catalogo-pedidos-vaciarCarrito"           : "services/pedidos.php/vaciarCarrito"},
    {"catalogo-pedidos-confirmarPedido"         : "services/pedidos.php/confirmarPedido"},
    {"catalogo-pedidos-getPendientesByVendedor" : "services/pedidos.php/getPedidosPendientesByVendedor"},
    {"catalogo-pedidos-modificar-items"         : "services/pedidos.php/modificarItem"},
    {"ficha-articulos"                          : "services/articulos.php/getFicha"},
    {"rentabilidad"                             : "services/sucursales.php/editarRentabilidad"},
    {"boxesFiltrados"                           : "services/articulos.php/filtrarBoxes"},
    {"margenesGenerales-get"                    : "services/sucursales.php/getRentabilidadesSuc"},
    {"margenesEspeciales-get"                   : "services/margenes_especiales.php/get"},
    {"margenesEspeciales-cargar"                : "services/margenes_especiales.php/cargarMargenesEspeciales"},
    {"margenesEspeciales-borrar"                : "services/margenes_especiales.php/borrarMargenesEspeciales"},
    {"aviso-pago"                               : "services/avp-rendiciones.php/agregarAvisoPago"},
    {"rendiAbi"                                 : "services/avp-rendiciones.php/getRendicionAbiertaPorVendedor"},
    {"enviarRendi"                              : "services/avp-rendiciones.php/generarRendicion"},
    {"novedades"                                : "services/novedades.php/get"},
    {"pedidos"                                  : "services/pedidos.php/consultar"},
    {"pedido-sap"                               : "services/pedidos.php/enviarPedido_a_SAP"}
    

]

// El siguiente array contiene los templates html que se utilizan en el sistema para dibujar
// la interfaz de usuario.
const aTemplates = [
    {"terceros-autocomplete"                    : "terceros/lfw-datalist-bs/template.html"},
    {"oper-pedidos-pendientes"                  : "components/catalogo/vendedores/pedidos-pendientes/template.html"},
    {"ingreso-pedido-rapido"                    : "components/catalogo/ingreso-pedidos-rapidos/template.html"},
    {"rentabilidad"                             : "components/rentabilidad/template.html"},
    {"ficha-articulo"                           : "components/catalogo/ficha-articulo/template.html"},
    {"aviso-pago"                               : "components/aviso_pago/template.html"},
    {"ipr-grid-articulos"                       : "components/catalogo/ingreso-pedidos-rapidos/grid-articulos-template.html"},
    {"centroNoticias"                           : "components/centro_noticias/template.html"},
    {"misPedidos"                               : "components/pedidos/mis_pedidos/template.html"}
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

    /**
     * Obtiene la url del template a leer.
     * @param {string} xkey Nombre del template definido en el array.
     * @returns 
     */
    getUrlTemplate(xkey) {
        for (let i = 0; i < aTemplates.length; ++i)
            if (aTemplates[i][xkey] !== undefined)
                return aTemplates[i][xkey];
    }
}
