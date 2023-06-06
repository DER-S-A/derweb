class AvisoPago extends ComponentManager {
    constructor(){
        super();
        this.arrayCli;
        this.arraySuc;
        this.objCacheUtils = new CacheUtils("derweb", false);
    }
    async generateComponent() {
        try {
           const clientes = await this.__getClientes();
           this.arrayCli = clientes;
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
                this.__llamarEventos();
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
        } catch (error) {
            console.error(error);
            return null;
        }
    }

    generateForm() {
        const formData = this.generateDataForm();
        let objForm = new Form(formData, "form-avisoPago").generateComponent();
        this.__validarCamposFront(objForm)
        return objForm;
    }

    __validarCamposFront(objForm) {
        const inputs = objForm.querySelectorAll('.form-control');
        inputs.forEach(element => {
            element.setAttribute('disabled', '');
        });
    }

    generateDataForm() {
        const arrayOptionsCli = this.__generarOptions();
        arrayOptionsCli.unshift({value:0,text:'Selecciona un cliente'});
        const arrayOptionsSuc = [{value:0, text:'Selecciona una sucursal'}]
        const obj = [
            {tag:'select', id:'sec-cod_cliente', name:'cod_cli', options:arrayOptionsCli},
            {tag:'select', id:'sec-cod_suc', name:'cod_suc', options:arrayOptionsSuc},
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

    __getClientes() {
        return new Promise((resolve, reject) => {
            const aSesion = this.objCacheUtils.get("sesion");
            const url = (new App()).getUrlApi("app-entidades-getClientesByVendedor");
            fetch(url + "?id_vendedor=" + aSesion["id_vendedor"])
            .then(response => response.json())
            .then(clientes => {
                resolve(clientes);
            },  error => reject(error));      
        })
    }

    __getSuc() {
        return new Promise((resolve, reject) => {
            const clienteSession = this.objCacheUtils.get("clienteSession");
            const url = new App().getUrlApi("app-entidades-getSucursalesByEntidad");
            new APIs().call(url, 'id_entidad=' + clienteSession, 'GET', clientes => {
                resolve(clientes);
            }, false, error => reject(error));
        })
    }

    __generarOptions() {
        const options = this.arrayCli.map(({id,codusu}) => ({value:id,text:codusu}));
        return options;
    }

    async __llamarEventos() {
        const objSelect = document.querySelector('#sec-cod_cliente');
        objSelect.addEventListener('change', async () => {
            this.objCacheUtils.set('clienteSession', objSelect.value);
            this.arraySuc = await this.__getSuc();
            const arrayOptionsSuc = this.__generarOptionsSuc();
            this.__limpiarOptionsSuc();
            this.__llenarOptionsSuc(arrayOptionsSuc);
        });
    }

    __generarOptionsSuc() {
        const options = this.arraySuc.map(({id,nombre}) => ({value:id,text:nombre}));
        return options
    }

    __llenarOptionsSuc(arrayOptions) {
        const select = document.querySelector('#sec-cod_suc');
        console.log(arrayOptions)
        arrayOptions.forEach(element => {
            const option = document.createElement('option');
            option.value = element.value;
            option.textContent = element.text;
            select.append(option);
        });
    }

    __limpiarOptionsSuc() {
        const select = document.querySelector('#sec-cod_suc');
        let options = select.querySelectorAll('option')
        options.forEach((element, index) => {
            if(index != 0) {
                element.remove();
            }
        });
    }
}