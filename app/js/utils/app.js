/**
 * Clase principal de la aplicación
 */

// Referencias a javascript de bootstrap
const bootstrapJS = [
    "node_modules/@popperjs/core/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.min.js",
    "node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"
];

// Importación de javascripts de la aplicación
const jsreferences = [
    "components/header/header.js",
    "components/footer/footer.js"
];

// Configuraciones varias

// Configuro el logo del header.
const _LOGO = "assets/imagenes/logo.png";

// Configuro la barra de redes sociales.
const _FOOTER_BAR = [
    {
        image: "assets/imagenes/icons/data-fiscal-8.png",
        href: "http://qr.afip.gob.ar/?qr=5Ip-AeTRpniGhP9pY4y5pg,,"
    },
    {
        image: "assets/imagenes/icons/facebook-8.png",
        href: "https://www.facebook.com/derdistribuciones/"
    },
    {
        image: "assets/imagenes/icons/instagram-8.png",
        href: "https://www.instagram.com/der.distribuciones/"
    },
    {
        image: "assets/imagenes/icons/youtube-8.png",
        href: "https://www.youtube.com/user/derdistribuciones"
    }
];

class App {
    /**
     * Inicializa la aplicación
     */
    init() {
        bootstrapJS.forEach((xelement => {
            this.__addScriptToHead(xelement);
        }));

        jsreferences.forEach((xelement) => {
            this.__addScriptToHead(xelement);
        })
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
}
