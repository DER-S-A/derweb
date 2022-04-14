/**
 * Clase principal de la aplicación
 */

// Referencias a javascript de bootstrap
const bootstrapJS = [
    "node_modules/@popperjs/core/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.min.js",
    "node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"
];

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
}
