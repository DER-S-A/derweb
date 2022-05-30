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
        this.__idAppContainer = xidAppContainer;
        this.__objAppContainer = document.getElementById(this.__idAppContainer);

        // Elimino el contenido de app container.
        this.deleteContent(this.__idAppContainer, "carrusel-container");

        // Armo el contenedor de lista
        this.__objListaContainer.classList.add("catalogo-container")
        this.__objAppContainer.appendChild(this.__objListaContainer);
    }

    /**
     * Permite generar la estructura de la página de catálogo.
     */
    generateComponent() {
        var objPanelOpcionesContainer = document.createElement("div");
        objPanelOpcionesContainer.id = "panel-opciones-container";
        objPanelOpcionesContainer.classList.add("panel-opciones-container");
        objPanelOpcionesContainer.appendChild(this.__generarPanelOpciones());
        this.__objListaContainer.appendChild(objPanelOpcionesContainer);
    }

    /**
     * Genero el panel de opciones.
     * @returns 
     */
    __generarPanelOpciones() {
        var objPanelOpciones = new PanelOpcionesComponent("panel-opciones");
        return objPanelOpciones.generateComponent();
    }
}