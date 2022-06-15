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

        this.clearContainer(xidAppContainer);

        // Id . del contendor de la aplicación donde se deberá desplegar la GUI.
        this.__idAppContainer = xidAppContainer;
        this.__objAppContainer = document.getElementById(this.__idAppContainer);

        // Id. del contenedor de los elementos del catálogo.
        this.__idCatalogoContainer = "catalogo-container";
        // Id. del contenedor que contiene todo el panel de opciones completo.
        this.__idPanelOpcionesContainter = "panel-opciones-container";

        // Elimino el contenido de app container.
        this.__deleteContent(this.__idAppContainer, "carrusel-container");

        // Armo el contenedor de lista
        this.__createListaContainer();
    }

    /**
     * Crea el contenedor general del catalogo.
     */
    __createListaContainer() {
        if (!this.__existsComponent(this.__idCatalogoContainer)) {
            this.__objListaContainer = document.createElement("div");
            this.__objListaContainer.classList.add("catalogo-container");
            this.__objListaContainer.id = this.__idCatalogoContainer;
            this.__objAppContainer.appendChild(this.__objListaContainer);
        }
    }

    /**
     * Permite generar la estructura de la página de catálogo.
     */
    generateComponent() {
        var objPanelOpcionesContainer = null;
        var objPanelOpciones = null;

        objPanelOpciones = this.__generarPanelOpciones();
        if (objPanelOpciones !== null) {
            objPanelOpcionesContainer = document.createElement("div");
            objPanelOpcionesContainer.id = this.__idPanelOpcionesContainter;
            objPanelOpcionesContainer.classList.add("panel-opciones-container");
            // Por algún motivo acá se está duplicado el div=id-opciones sin clase. (No causa efecto)
            objPanelOpcionesContainer.appendChild(objPanelOpciones);
            this.__objListaContainer.appendChild(objPanelOpcionesContainer);
        }
    }

    /**
     * Genero el panel de opciones.
     * @returns {DOM element}
     */
    __generarPanelOpciones() {
        var objPanelOpciones = new PanelOpcionesComponent("panel-opciones");
        var aOpcionesModo = new Array();
        if (!this.__existsComponent(this.__idPanelOpcionesContainter)) {
            // Armo las opciones del select de modo.
            aOpcionesModo.push({"valor": "PED", "nombre": "Pedido"});
            aOpcionesModo.push({"valor": "PRE", "nombre": "Presupuesto"});

            return objPanelOpciones.generateComponent(aOpcionesModo);
        }
        return null;
    }

    /**
     * Obtiene los resultados cuando se ingresa desde LISTA ARTIUCLOS.
     * @param {Array} xparametros Array con los parámetros de búsqueda
     *  Formato:    { 
     *                  api_url : "Establece la URL de la API a consultar", 
     *                  values: [
     *                      { id_rubro: "Id. de rubro"},
     *                      { id_subrubro: "Id. de de subrubro"},
     *                  ]
     *              }
     */
    getArticulosResultadoBusqueda(xaParametros) {
        var objGrilla = null;
        objGrilla  = new CatalogoGridComponent("grilla-articulos", xaParametros);
        objGrilla.generateComponent(this.__idAppContainer);
    }
}