/**
 * Este componente se encarga de armar el encabezado de la aplicación
 * que se va a mostrar en todas las pantallas.
 * 
 * Dependencia: funciones.js y app.js
 */

class HtmlHeader extends HTMLElement {
    /**
     * Al instanciar la clase armo el contenido HTML de app-header.
     */
    constructor() {
        super();
        this.logo = "";
        this.buttonHome = this.hasAttribute("back-button");
        this.backButtonURL = "#";
        this.controlSearch = this.hasAttribute("control-search");
        this.template = this.getAttribute("template");

        getTemplate(this.template, (xhtml) => {
            this.__procesarTemplate(xhtml);
        });
            
    }

    __procesarTemplate(xhtml) {
        this.logo = _LOGO;
        this.innerHTML = xhtml.replace("{logo}", this.logo);

        // Si el atributo back-button está establecido en la etiqueta, entonces,
        // muestro el link para volver.
        if (this.buttonHome) {
            this.backButtonURL = this.getAttribute("back-button-url");
            this.__setBackButton();
        }        
    }

    /**
     * Este método permite agregar el link para volver a la página principal.
     */
    __setBackButton() {
        var headerContainer = document.getElementById("app-header-content");
        var objDiv = document.createElement("div");
        var objButton = document.createElement("a");

        objDiv.id = "volver";
        objDiv.classList.add("back-button");

        objButton.innerHTML = "<i class=\"fa-solid fa-angle-left\"></i> Volver a la web";
        objButton.href = this.backButtonURL;
        objDiv.appendChild(objButton);

        headerContainer.appendChild(objDiv);
    }
}

// Defino la etiqueta app-header.
customElements.define("app-header", HtmlHeader);