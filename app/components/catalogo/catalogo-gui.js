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
        this.__objGrilla = null;

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
            this.__objAppContainer.className = 'container';
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

            // Agrego los eventos de las opciones de vista de precios.
            
            document.getElementById("opcion-lista-precio").addEventListener("click", () => {
                let objOpcionLista = document.getElementById("opcion-lista-precio");
                let objOpcionCosto = document.getElementById("opcion-costo");
                let objOpcionVenta = document.getElementById("opcion-venta");
                console.log(this.__validarCheck(objOpcionLista.checked, objOpcionCosto.checked, objOpcionVenta.checked))
                if(this.__validarCheck(objOpcionLista.checked, objOpcionCosto.checked, objOpcionVenta.checked)){
                    this.__objGrilla.setVerPrecioLista(objOpcionLista.checked);
                } else {
                    swal("Oops!", "Debe seleccionar otro campo antes de quitar este", "error");
                    objOpcionLista.checked = "true";
                }
                
            });

            document.getElementById("opcion-costo").addEventListener("click", () => {
                let objOpcionLista = document.getElementById("opcion-lista-precio");
                let objOpcionCosto = document.getElementById("opcion-costo");
                let objOpcionVenta = document.getElementById("opcion-venta");
                if(this.__validarCheck(objOpcionLista.checked, objOpcionCosto.checked, objOpcionVenta.checked)){
                    this.__objGrilla.setVerPrecioCosto(objOpcionCosto.checked);
                } else {
                    swal("Oops!", "Debe seleccionar otro campo antes de quitar este", "error");
                    objOpcionCosto.checked = "true";
                }
                
            });

            document.getElementById("opcion-venta").addEventListener("click", () => {
                let objOpcionLista = document.getElementById("opcion-lista-precio");
                let objOpcionCosto = document.getElementById("opcion-costo");
                let objOpcionVenta = document.getElementById("opcion-venta");
                if(this.__validarCheck(objOpcionLista.checked, objOpcionCosto.checked, objOpcionVenta.checked)) {
                    this.__objGrilla.setVerPrecioVenta(objOpcionVenta.checked);
                } else {
                    swal("Oops!", "Debe seleccionar otro campo antes de quitar este", "error");
                    objOpcionVenta.checked = "true";
                }
                
            });
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

    __validarCheck(xlista, xcosto, xventa) {
        if (xlista == false && xcosto == false && xventa == false) {
            return false;
        } else return true;
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
    getArticulosResultadoBusqueda(xaParametros, xbuscarPorFrase = false) {
        this.__objGrilla = new CatalogoGridComponent("grilla-articulos", xaParametros, xbuscarPorFrase);
        this.__objGrilla.generateComponent(this.__idAppContainer);
    }

    /*
     * Permite obtener una lista de artículos buscando por una frase.
     * @param {array} $xaParametros Array con los parámetors a pasar
     */
    getByFrase($xaParametros) {console.log(this.__idAppContainer)
        this.__objGrilla = new CatalogoGridComponent("grilla-articulos", $xaParametros);
        this.__objGrilla.generateComponent(this.__idAppContainer);
    }
}
