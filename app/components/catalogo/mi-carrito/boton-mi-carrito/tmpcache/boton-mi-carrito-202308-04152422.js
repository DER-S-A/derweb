/**
 * Esta clase permite crear el botón mi carrito.
 */

class BotonMiCarritoComponent extends ComponentManager {

    /**
     * Constructor de clase
     * @param {string} xidContainer Id. de container donde se tiene que generar el componente.
     */
    constructor (xidContainer) {
        super();
        this.__idContainer = xidContainer;
        this.__objContainer = document.getElementById(this.__idContainer);
        this.__jsFunction = "";
    }

    /**
     * Establece el nombre de función de javascriot que se ejecutará
     * al hacer click en el botón.
     * @param {string} xvalue Nombre de la función.
     */
    setJSFunction(xvalue) {
        this.__jsFunction = xvalue;
    }

    /**
     * Genera el componente
     */
    generateComponent() {
        var objhref = document.createElement("a");
        objhref.classList.add("btn");
        objhref.classList.add("btn-mi-carrito");
        objhref.href = "javascript:" + this.__jsFunction + "();";
        objhref.innerHTML = `<img src="assets/imagenes/icons/changuitoConCeleste.png">`
        //objhref.innerHTML = "<i class=\"fa-solid fa-cart-shopping fa-lg\"></i>";
        this.__objContainer.appendChild(objhref);
    }
}