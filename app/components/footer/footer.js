/**
 * Este componente se encarga de armar el footer de la aplicación.
 * 
 * Dependencia: funciones.js y app.js
 */

class HTMLFooter extends HTMLElement {
    constructor() {
        super();
        this.template = this.getAttribute("template");
        this.includeSocialBar = this.hasAttribute("include-social-bar");
        fetch(this.template).then(xresponse => {
            return xresponse.text();
        }).then(xHtml => {
            this.__procesarTemplate(xHtml);
        });
    }

    __procesarTemplate(xHtml) {
        let configuracion = JSON.parse(sessionStorage.getItem("derweb_config"));

        // Si está marcado como footer portada ar
        if (this.includeSocialBar)
            _FOOTER_BAR.forEach((xelement, xindex) => {
                xHtml = xHtml.replace("{" + xindex + "}", xelement.href);
                xHtml = xHtml.replace("{" + xindex + "}", xelement.image);
            });
        this.innerHTML = xHtml;        
    }
}

// Creo el componente.
customElements.define("app-footer", HTMLFooter);