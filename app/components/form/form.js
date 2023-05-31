class Form extends ComponentManager {
    /**
     * @param {object} formData Tiene los labels e inputs.
     * @param {string} idName Establece el id del form.
     * @param {string} oButton Texto contenedor de la etiqueta button.
     */
    constructor(formData, idName, oButton = null) {
        super();
        this.formData = formData;
        this.button = oButton;
        this.id = idName;
        this.class = idName;
        this.form = this.crearElementDom("form", this.class, this.id); 
    }

    /**
     * Genero el componente.
     * @return {objDomElement}
     */
    generateComponent() {
        this.formData.forEach(element => {
            const oDiv = this.crearElementDom("div", "mb-3");
            oDiv.innerHTML = 
                `<label for="${element.id}" class="form-label">${element.content}</label>
                 <input type="${element.type}" class="form-control" id="${element.id}" aria-describedby="emailHelp">`
            this.form.appendChild(oDiv);
        });
        if(this.button != null) {
            const oButton = this.crearElementDom("button", "btn btn-primary", "btn-"+this.id, ["type", "submit"]);
            oButton.textContent = this.button
            this.form.append(oButton);
        }
        return this.form;
    }
}