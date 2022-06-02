/**
 * Clase: CatalogoGUIComponent
 * Descripción:
 *  Contiene el contenido la generación de la estructura de pantalla del catálogo.
 */

class CatalogoGUIComponent extends ComponentManager {
    /**
     * Constructor de clase
     * @param {string} xidAppContainer Id. del contenedor principal de la clase.
     */
    constructor(xidAppContainer) {
        super();
        this.__objListaContainer = document.createElement("div");

        // Id . del contendor de la aplicación donde se deberá desplegar la GUI.
        this.__idAppContainer = xidAppContainer;
        this.__objAppContainer = document.getElementById(this.__idAppContainer);

        // Id. del contenedor de los elementos del catálogo.
        this.__idCatalogoContainer = "catalogo-container";
        // Id. del contenedor que contiene todo el panel de opciones completo.
        this.__idPanelOpcionesContainter = "panel-opciones-container";

        // Elimino el contenido de app container.
        this.deleteContent(this.__idAppContainer, "carrusel-container");

        // Armo el contenedor de lista
        this.__objListaContainer.classList.add("catalogo-container")
        this.__objListaContainer.id = this.__idCatalogoContainer;
        this.__objAppContainer.appendChild(this.__objListaContainer);
    }

    /**
     * Permite generar la estructura de la página de catálogo.
     */
    generateComponent() {
        var objPanelOpcionesContainer = document.createElement("div");
        var objPanelOpciones = null;

        objPanelOpciones = this.__generarPanelOpciones();
        if (objPanelOpciones !== null) {
            objPanelOpcionesContainer.id = this.__idPanelOpcionesContainter;
            objPanelOpcionesContainer.classList.add("panel-opciones-container");
            objPanelOpcionesContainer.appendChild(objPanelOpciones);
            this.__objListaContainer.appendChild(objPanelOpcionesContainer);
        }
    }

    /**
     * Genero el panel de opciones.
     * @returns 
     */
    __generarPanelOpciones() {
        var objPanelOpciones = new PanelOpcionesComponent("panel-opciones");
        if (!this.existsComponent(this.__idPanelOpcionesContainter))
            return objPanelOpciones.generateComponent();
        return null;
    }

    /**
     * Permite limpiar la pantalla del catálogo.
     */
    clear() {
        this.deleteContent(this.__idAppContainer, "catalogo-container");
    }
}