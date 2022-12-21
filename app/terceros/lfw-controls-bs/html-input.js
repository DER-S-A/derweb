/**
 * Clase: HTMLInput
 * Descripción:
 *  Crea el campo de texto para solicitar ingreso de información.
 */

class HTMLInput {
    /**
     * Control Input
     * @param {string} xidcontrol Id. de control.
     * @param {string} xetiqueta Establece la etiqueta con el nombre de campo a mostrar en pantalla.
     */
    constructor(xidcontrol, xetiqueta) {
        this.__idControl = xidcontrol;
        this.__etiqueta = xetiqueta;
        this.__inputPlaceHolder = "";
        this.__readOnly = false;
        this.__dataType = "text";
        this.__value = null;
        this.__width = 100;

        this.__objFieldContainer = null;
        this.__objLabel = null;
        this.__objInput = null;
    }

    /**
     * Establece el placeholder del input.
     * @param {string} xvalue 
     */
    setPlaceHolder(xvalue) {
        this.__inputPlaceHolder = xvalue;
    }

    /**
     * Establece el control como de solo lectura.
     * @param {bool} xvalue 
     */
    setReadOnly(xvalue = true) {
        this.__readOnly = xvalue;
    }

    /**
     * Establece el tipo de datos del control.
     * @param {string} xvalue int | float | text | email
     */
    setDataType(xvalue) {
        this.__dataType = xvalue;
    }

    /**
     * Establece el valor del campo.
     * @param {mixed} xvalue 
     */
    setValue(xvalue) {
        this.__value = xvalue;
    }

    /**
     * Establece el ancho del control en pixeles.
     * @param {int} xvalue 
     */
    setWidth(xvalue) {
        this.__width = xvalue;
    }

    /**
     * Genera el HTML.
     */
    toHtml() {
        this.__createFieldContainer();
        this.__createLabel();
        this.__createInput();
        return this.__objFieldContainer;
    }

    /**
     * Crea el contenedor del campo.
     */
    __createFieldContainer() {
        this.__objFieldContainer = document.createElement("div");
        this.__objFieldContainer.classList.add("mb-3");
    }

    /**
     * Crea el label (etiqueta).
     */
    __createLabel() {
        this.__objLabel = document.createElement("label");
        this.__objLabel.setAttribute("for", this.__idControl);
        this.__objLabel.classList.add("form-label");
        this.__objLabel.innerHTML = this.__etiqueta;
        this.__objFieldContainer.appendChild(this.__objLabel);
    }

    /**
     * Crea el control input.
     */
    __createInput() {
        this.__objInput = document.createElement("input");       
        this.__objInput.id = this.__idControl;
        this.__objInput.name = this.__idControl;
        this.__objInput.classList.add("form-control");
        this.__objInput.setAttribute("style", "font-size: 12px");

        // Establezco el tipo de datos.
        if (this.__esNumerico()) {
            this.__objInput.type = "numeric";
            this.__objInput.style.textAlign = "right";
        } else {
            this.__objInput.type = this.__dataType;
        }

        if (this.__readOnly)
            this.__objInput.setAttribute("readonly", this.__readOnly);

        if (this.__value !== null)
            this.__objInput.value = this.__value;

        this.__objInput.style.width = this.__width + "px";
        this.__objFieldContainer.appendChild(this.__objInput);
    }

    /**
     * Verifica si el control es numérico o no.
     * @returns {bool} 
     */
    __esNumerico() {
        if (this.__dataType === "int" || this.__dataType === "float")
            return true;
        else
            return false;
    }

    /**
     * Devuelve el valor del input.
     * @returns {mixed}
     */
    getValue() {
        this.__value = document.getElementById(this.__idControl).value;
        if (this.__dataType === "int")
            return parseInt(this.__value);
        if (this.__dataType === "float")
            return parseFloat(this.__value);
        return this.__value;
    }
}