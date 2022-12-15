/**
 * Clase: LFWDataListBS
 * Descripción: Control para generar un autocomplete.
 * Referencia: https://getbootstrap.com/docs/5.2/forms/form-control/#datalists
 * Desarrollador: Leonardo D. Zulli
 * Fecha: 07/11/2022
 * Dependencias:
 *  - Clase App().
 *  - ComponentManager().
 */

class LFWDataListBS {
    constructor() {
        this.idSelector = "";
        this.etiqueta = "";
        this.idDataListOptions = "";
        this.placeholderText = "";
        this.html = "";
        this.dataSet = [];
        this.aColumns = [];
        this.aColumnsKey = [];
    }

    /**
     * Establece el id del selector
     * @param {string} xvalue 
     */
    setIdSelector(xvalue) {
        this.idSelector = xvalue;
    }

    /**
     * Establece la etiqueta que se le muestra al usuario.
     * @param {string} xvalue 
     */
    setEtiqueta(xvalue) {
        this.etiqueta = xvalue;
    }

    /**
     * Establece el id de las opciones del autocomplete.
     * @param {string} xvalue 
     */
    setIdDataListOptions(xvalue) {
        this.idDataListOptions = xvalue;
    }

    /**
     * Establece el texto a mostrar en el placeholder del input.
     * @param {string} xvalue 
     */
    setPlaceholderText(xvalue) {
        this.placeholderText = xvalue;
    }

    /**
     * Establece los datos que se mostrarán como opciones.
     * @param {array} xaValue 
     */
    setData(xaValue) {
        this.dataSet = xaValue;
    }

    /**
     * Establece la o las columnas a mostrar.
     * @param {array} xaValue 
     */
    setColumns(xaValue) {
        this.aColumns = xaValue;
    }

    /**
     * Establece las columnas que corresponden a los campos claves.
     * @param {array} xaValues 
     */
    setColumnsKey(xaValues) {
        this.aColumnsKey = xaValues
    }

    /**
     * Genera el código HTML a dibujar en pantalla.
     * @param {callback} xcb_function Función que permite procesar el html generado.
     */
    toHtml(xcb_function) {
        let objComponentManager = new ComponentManager();
        // Levanto el template HTML a procesar
        this.urlTemplate = (new App()).getUrlTemplate("terceros-autocomplete");
        objComponentManager.getTemplate(this.urlTemplate, htmlResponse => {
            let options;
            this.html = htmlResponse;
            this.html = objComponentManager.setTemplateParameters(this.html, "id-selector", this.idSelector);
            this.html = objComponentManager.setTemplateParameters(this.html, "etiqueta", this.etiqueta);
            this.html = objComponentManager.setTemplateParameters(this.html, "id-datalistOptions", this.idDataListOptions);
            this.html = objComponentManager.setTemplateParameters(this.html, "placeholder_text", this.placeholderText);
            options = this.__fillData();
            this.html = objComponentManager.setTemplateParameters(this.html, "data-list-option", options.outerHTML);
            xcb_function(this.html);
        });
    }

    /**
     * Genera la lista de opciones a mostrar en el autocomplete.
     * @returns {DOM Element}
     */
    __fillData() {
        let objDataListOption = document.createElement("datalist");
        let optionsValues = "";
        objDataListOption.id = this.idDataListOptions;
        this.__idSelectorClientesDataList = objDataListOption.id;
        this.dataSet.forEach(element => {
            let jsonValues = this.__getValuesKey(element);
            this.aColumns.forEach((column, index) => {
                if (index < this.aColumns.length - 1)
                    optionsValues += "<option data-value='" + jsonValues + "'>" + element[column] + " - ";
                else
                    optionsValues += element[column] + "</option>";
            });
        });

        objDataListOption.innerHTML = optionsValues;
        return objDataListOption;
    }

    /**
     * Devuelve un array JSON con los valores claves para poner en el data-value
     * @param {array} xelement 
     * @returns {string}
     */
    __getValuesKey(xelement) {
        let aValues = {};

        this.aColumnsKey.forEach(column => {
            aValues[column] = parseInt(xelement[column]);
        });

        return JSON.stringify(aValues);
    }

    /**
     * Obtiene el registro seleccionado actualmente y pone el valor en el atributo data-value.
     * @param {string} xSelectedValue Viene del event.target.value que obtiene el texto seleccionado.
     * @returns {Array}
     */
    getSelectedValue(xSelectedValue) {
        let options = document.getElementById(this.idDataListOptions).childNodes;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value === xSelectedValue)
                document.getElementById(this.idSelector).dataset.value = options[i].dataset.value;
        }
    }
}
