/**
 * Clase: PanelOpcionesComponent
 * Descripción:
 *  Contiene el panel de opciones del catálogo.
 */

class PanelOpcionesComponent extends ComponentManager {
    /**
     * Constructor de clase
     * @param {string} xidcontainer Id. de contenedor.
     */
    constructor(xidcontainer) {
        super();
        this.idContainer = xidcontainer;
        this.__objContainer = document.createElement("div");
        this.__objContainer.id = this.idContainer;
        this.id_selector_modo = "sel-modo";
        this.nameOption = "opcion-precios";
        this.idOptionListaPrecio = "opcion-lista-precio";
        this.idOptionCosto = "opcion-costo";
        this.idOptionVenta = "opcion-venta";
        this.__objRow = this.addBootstrapRow();
    }

    /**
     * Genera le componente
     * @returns {DOMElement}
     */
    generateComponent() {
        var objPanel = document.createElement("div");
        objPanel.id = "panel-opciones";
        objPanel.classList.add("panel-opciones");

        this.__agregarSelectorModo();
        this.__agregarOpciones();
        objPanel.appendChild(this.__objRow);
        
        this.__objContainer.appendChild(objPanel);
        return this.__objContainer
    }

    /**
     * Arma la sección de selección de modo.
     * @returns {DOMElement}
     */
    __agregarSelectorModo() {
        var objLabel = document.createElement("label");
        var objSelect = document.createElement("select");
        var objColumn = this.addBoostralColumn(["col-md-3"]);

        // Armo el selector
        objLabel.classList.add("modo-label");
        objLabel.textContent = "MODO ";
        objLabel.setAttribute("for", this.id_selector_modo);
        objSelect.id = this.id_selector_modo;
        objSelect.classList.add("select-modo");

        objColumn.appendChild(objLabel);
        objColumn.appendChild(objSelect);
        this.__objRow.appendChild(objColumn);
    }

    /**
     * Agrega las opciones visualización de precios.
     * @returns {DOMElement}
     */
    __agregarOpciones() {
        var objContenedorOpciones = document.createElement("div");
        var objColumn = this.addBoostralColumn(["col-md-9"]);
        var objUl = document.createElement("ul");
        var objLiPrecioLista = document.createElement("li");
        var objLiPrecioCosto = document.createElement("li");
        var objLiPrecioVenta = document.createElement("li");

        objContenedorOpciones.classList.add("options-container");

        objLiPrecioLista.appendChild(this.__agregarOpcionPrecioLista());
        objUl.appendChild(objLiPrecioLista);
        objLiPrecioCosto.appendChild(this.__agregarOpcionPrecioCosto());
        objUl.appendChild(objLiPrecioCosto);
        objLiPrecioVenta.appendChild(this.__agregarOpcionPrecioVenta());
        objUl.appendChild(objLiPrecioVenta);

        objContenedorOpciones.appendChild(objUl);
        objColumn.appendChild(objContenedorOpciones);
        this.__objRow.appendChild(objColumn);
    }

    /**
     * Agrega opcion para precio de lista.
     * @returns {DOMElement}
     */
    __agregarOpcionPrecioLista() {
        var objLabel = document.createElement("label");
        var objSpan = document.createElement("span");
        var objOptionPrecioLista = document.createElement("input");

        objSpan.textContent = "PRECIO DE LISTA";
        objOptionPrecioLista.type = "checkbox"
        objOptionPrecioLista.id = this.idOptionListaPrecio;
        objOptionPrecioLista.name = this.nameOptionPrecio;
        objLabel.appendChild(objOptionPrecioLista);
        objLabel.appendChild(objSpan);

        return objLabel;
    }

    /**
     * Agrega las opciones para le precio de costo.
     * @returns {DOMElement}
     */
    __agregarOpcionPrecioCosto() {
        var objLabel = document.createElement("label");
        var objSpan = document.createElement("span");        
        var objOptionPrecioCosto = document.createElement("input");

        objSpan.textContent = "PRECIO DE COSTO";
        objOptionPrecioCosto.type = "checkbox";
        objOptionPrecioCosto.id = this.idOptionCosto;
        objOptionPrecioCosto.name = this.nameOption;
        objLabel.appendChild(objOptionPrecioCosto);
        objLabel.appendChild(objSpan);

        return objLabel;
    }

    /**
     * Agrega la opción para precio de venta.
     * @returns {DOMElement}
     */
    __agregarOpcionPrecioVenta() {
        var objLabel = document.createElement("label");
        var objSpan = document.createElement("span");        
        var objOptionPrecioVenta = document.createElement("input");
        
        objSpan.textContent = "PRECIO DE VENTA";
        objOptionPrecioVenta.type = "checkbox";
        objOptionPrecioVenta.id = this.idOptionVenta;
        objOptionPrecioVenta.name = this.nameOption;
        objLabel.appendChild(objOptionPrecioVenta);
        objLabel.appendChild(objSpan);

        return objLabel;
    }
}