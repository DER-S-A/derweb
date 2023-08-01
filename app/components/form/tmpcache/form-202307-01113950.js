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
            if(element.tag == 'select') {
                let select = this.crearElementDom(element.tag, element.class, element.id, ['name', element.name]);
                select = this.__generarOptions(select, element.options);
                oDiv.append(select);
            }
            if(element.tag == 'input') {
                element.requerid = element.required != null ? element.required : "";
                oDiv.innerHTML = 
                `<label for="${element.id}" class="${element.classL}">${element.content}</label>
                 <${element.tag} type="${element.type}" class="${element.class}" name="${element.id}" id="${element.id}" aria-describedby="emailHelp"  ${element.requerid}>`
            }
            if(element.tag == 'textarea') {
                oDiv.classList.add('contenedor-textarea');
                oDiv.innerHTML = 
                `<label for="${element.id}" class="${element.classL}">${element.content}</label>
                 <${element.tag} class="${element.class}" name="${element.id}" id="${element.id}" row="${element.row}">`
            }
            this.form.appendChild(oDiv);
        });
        if(this.button != null) {
            const oButton = this.crearElementDom("button", "btn btn-primary", "btn-"+this.id, ["type", "submit"]);
            oButton.textContent = this.button
            this.form.append(oButton);
        }
        return this.form;
    }
    __generarOptions(select, options) {
        options.forEach(option => {
            const objOption = document.createElement("option");
            objOption.value = option.value;
            objOption.textContent = option.text;
            select.append(objOption);
        });
        return select;
    }
}