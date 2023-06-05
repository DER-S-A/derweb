class AvisoPago extends ComponentManager {
    constructor(){
        super();
    }
    generateComponent() {
        const miSession = new CacheUtils('derweb').get('sesion');
        if(!document.querySelector("#modalAvisoPago")) {
            const nodoContainer = document.querySelector("#app-container");
            this.getTemplate(new App().getUrlTemplate("aviso-pago"), html => {
                let containerModal = this.crearElementDom("div", "dockModalAP");
                containerModal.innerHTML = html;
                nodoContainer.append(containerModal);
                const bodyModal = document.querySelector('#modalAvisoPago .modal-body');
                bodyModal.append(this.generateForm());
                const modal = new bootstrap.Modal(document.querySelector('#modalAvisoPago'));
                modal.show();
                const botonApli = document.querySelector("#modalAvisoPago #aplicar");
                botonApli.addEventListener("click", ()=> {
                    const FecDat = new Date();
                    const fecha = `${FecDat.getFullYear()}-${FecDat.getMonth()}-${FecDat.getDay()}`
                    const bodyJson = {
                        "id_vendedor":miSession.id_vendedor,
                        "id_cliente":document.querySelector("#input-cod_cliente").value,
                        "id_sucursal":"28278",
                        "fecha":fecha,
                        "numero_recibo":document.querySelector("#input-numeroRec").value,
                        "importe_recibo":document.querySelector("#input-importeRec").value,
                        "importe_efectivo":document.querySelector("#input-importeEfec").value,
                        "importe_cheques":document.querySelector("#input-importeCheque").value,
                        "importe_deposito":document.querySelector("#input-importeDepo").value,
                        "importe_retenciones":document.querySelector("#input-importeRet").value,

                    }
                    console.log(bodyJson);
                    this.enviarAviso(bodyJson);
                })
            })
        } else {
            const modal = new bootstrap.Modal(document.querySelector('#modalAvisoPago'));
            modal.show();
        }
    }

    generateForm() {
        const formData = this.generateDataForm();
        const objForm = new Form(formData, "form-avisoPago").generateComponent();
        return objForm;
    }

    generateDataForm() {
        /*const obj = [
            {id:'input-cod_cliente', name:'cod_cliente', type:'text', content:'Cliente:'},
            {id:'input-cod_suc', name:'cod_suc', type:'text', content:'Sucursal:'},
            {id:'input-numeroRec', name:'numeroRec', type:'text', content:'Nº Recibo:'},
            {id:'input-importeRec', name:'importeRec', type:'number', content:'Importe Recibo:'},
            {id:'input-importeEfec', name:'importeEfec', type:'number', content:'Importe en efectivo:'},
            {id:'input-importeCheque', name:'importeCheque', type:'number', content:'Importe en cheques:'},
            {id:'input-importeDepo', name:'importeDepo', type:'number', content:'Importe en depositos:'},
            {id:'input-importeRet', name:'importeRet', type:'number', content:'Importe en retenciones:'}
        ]*/
        const obj = [
            {tag:'select', id:'sec-cod_cliente', name:'cod_cli', options:[{

                value:0, text:'Selecciona un cliente'},
                {value:15402, text:'cliente prueba'}
            ]},
            {tag:'select', id:'sec-cod_suc', name:'cod_suc', options:[{

                value:0, text:'Selecciona una sucursal'},
                {value:98412, text:'sucursal Principal'}
            ]},
            {tag:'input', id:'input-numeroRec', name:'numeroRec', type:'text', content:'Nº Recibo:'},
            {tag:'input', id:'input-importeRec', name:'importeRec', type:'number', content:'Importe Recibo:'},
            {tag:'input', id:'input-importeEfec', name:'importeEfec', type:'number', content:'Importe en efectivo:'},
            {tag:'input', id:'input-importeCheque', name:'importeCheque', type:'number', content:'Importe en cheques:'},
            {tag:'input', id:'input-importeDepo', name:'importeDepo', type:'number', content:'Importe en depositos:'},
            {tag:'input', id:'input-importeRet', name:'importeRet', type:'number', content:'Importe en retenciones:'}
        ]
        return obj;
    }

    enviarAviso(value) {
        const url = (new App()).getUrlApi("aviso-pago");
        new APIs().call(url, value, "POST", (datos) => {
            console.log(datos)
        }, value)

    }
}