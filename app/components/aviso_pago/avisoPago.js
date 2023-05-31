class AvisoPago extends ComponentManager {
    constructor(){
        super();
    }
    generateComponent() {
        const nodoContainer = document.querySelector("#app-container");
        this.getTemplate(new App().getUrlTemplate("aviso-pago"), html => {
            let containerModal = this.crearElementDom("div", "dockModalAP");
            containerModal.innerHTML = html;
            nodoContainer.append(containerModal);
            const bodyModal = document.querySelector('#modalAvisoPago .modal-body');
            bodyModal.append(this.generateForm());
            const modal = new bootstrap.Modal(document.querySelector('#modalAvisoPago'));
            modal.show();
        })
    }

    generateForm() {
        const formData = this.generateDataForm();
        const objForm = new Form(formData, "form-avisoPago").generateComponent();
        return objForm;
    }

    generateDataForm() {
        const obj = [
            {id:'input-cod_cliente', name:'cod_cliente', type:'text', content:'Cliente:'},
            {id:'input-numeroRec', name:'numeroRec', type:'number', content:'Nº Recibo:'},
            {id:'input-importeRec', name:'importeRec', type:'number', content:'Importe Recibo:'},
            {id:'input-importeEfec', name:'importeEfec', type:'number', content:'Importe en efectivo:'},
            {id:'input-importeCheque', name:'importeCheque', type:'number', content:'Importe en cheques:'},
            {id:'input-importeDepo', name:'importeDepo', type:'number', content:'Importe en depositos:'},
            {id:'input-importeRet', name:'importeRet', type:'number', content:'Importe en retenciones:'}
        ]
        return obj;
    }
}