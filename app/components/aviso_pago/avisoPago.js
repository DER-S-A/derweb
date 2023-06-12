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

    /**
    * Permite generar componente formulario.
    */
    generateForm() {
        const formData = this.generateDataForm();
        let objForm = new Form(formData, "form-avisoPago").generateComponent();
        this.__validarCamposFront(objForm)
        return objForm;
    }

    /**
    * Permite ingresar a la operación de pedidos rápidos.
    */
    __validarCamposFront(objForm) {
        const inputs = objForm.querySelectorAll('.form-control');
        inputs.forEach(element => {
            if(objForm.querySelector('#sec-cod_suc').value == 0 ) {
                element.setAttribute('disabled', '');
            } else element.removeAttribute('disabled');
            if(element.id == 'input-importeEfec' || element.id == 'input-importeCheque' || element.id == 'input-importeDepo' || element.id == 'input-importeRet')
            {
                element.value = 0;
            }
        });
    }

    generateDataForm() {
        const arrayOptionsCli = this.__generarOptions();
        arrayOptionsCli.unshift({value:0,text:'Selecciona un cliente'});
        const arrayOptionsSuc = [{value:0, text:'Selecciona una sucursal'}];
        const claseLabel = "form-label";
        const claseInput = "form-control";
        const obj = [
            {tag:'select', id:'sec-cod_cliente', class:'form-select', name:'cod_cli', options:arrayOptionsCli},
            {tag:'select', id:'sec-cod_suc', class:'form-select', name:'cod_suc', options:arrayOptionsSuc},
            {tag:'input', id:'input-numeroRec', class:claseInput, classL:claseLabel, name:'numeroRec', type:'text', content:'Nº Recibo:', requerid:'requerid'},
            {tag:'input', id:'input-importeRec', class:claseInput, classL:claseLabel, name:'importeRec', type:'number', content:'Importe Recibo:', requerid:''},
            {tag:'input', id:'input-importeEfec', class:claseInput, classL:claseLabel, name:'importeEfec', type:'number', content:'Importe en efectivo:'},
            {tag:'input', id:'input-importeCheque', class:claseInput, classL:claseLabel, name:'importeCheque', type:'number', content:'Importe en cheques:'},
            {tag:'input', id:'input-importeDepo', class:claseInput, classL:claseLabel, name:'importeDepo', type:'number', content:'Importe en depositos:'},
            {tag:'input', id:'input-importeRet', class:claseInput, classL:claseLabel, name:'importeRet', type:'number', content:'Importe en retenciones:'}
        ]
        return obj;
    }

    enviarAviso(value) {
        const url = (new App()).getUrlApi("aviso-pago");
        new APIs().call(url, value, "POST", (datos) => {
            console.log(datos)
            swal({title:datos.mensaje, icon:datos.result})
            .then(() => {
                location.reload(true);
            })
        }, true)

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
            if(document.querySelector(`#sec-cod_cliente option[value="0"]`) != null) 
            {
                document.querySelector(`#sec-cod_cliente option[value="0"]`).remove();
            }
            this.objCacheUtils.set('clienteSession', objSelect.value);
            this.arraySuc = await this.__getSuc();
            const arrayOptionsSuc = this.__generarOptionsSuc();
            this.__limpiarOptionsSuc();
            this.__llenarOptionsSuc(arrayOptionsSuc);
            this.__validarCamposFront(document.querySelector('#form-avisoPago'));
        });

        const objSelSuc = document.querySelector('#sec-cod_suc');
        objSelSuc.addEventListener('change', () => {
            this.__validarCamposFront(document.querySelector('#form-avisoPago'));
        });

        const botonApli = document.querySelector("#modalAvisoPago #aplicar");
        botonApli.addEventListener("click", ()=> {
            const miSession = new CacheUtils('derweb').get('sesion');
            const FecDat = new Date();
            const fecha = `${FecDat.getFullYear()}-${FecDat.getMonth()+1}-${FecDat.getDay()}`
            const bodyJson = {
                "id_vendedor":miSession.id_vendedor,
                "id_cliente":parseInt(document.querySelector("#sec-cod_cliente").value),
                "id_sucursal":parseInt(document.querySelector("#sec-cod_suc").value),
                "fecha":fecha,
                "numero_recibo":document.querySelector("#input-numeroRec").value,
                "importe_recibo":parseFloat(document.querySelector("#input-importeRec").value),
                "importe_efectivo":parseFloat(document.querySelector("#input-importeEfec").value),
                "importe_cheques":parseFloat(document.querySelector("#input-importeCheque").value),
                "importe_deposito":parseFloat(document.querySelector("#input-importeDepo").value),
                "importe_retenciones":parseFloat(document.querySelector("#input-importeRet").value),

            }
            console.log(bodyJson);
            const {importe_recibo, importe_efectivo, importe_cheques, importe_deposito, importe_retenciones} = bodyJson;
            const arrayImp = [importe_recibo, importe_efectivo, importe_cheques, importe_deposito, importe_retenciones]
            if(!this.__validarImpTotalRec(arrayImp)) {
                swal("IMPORTE RECIBO ERROR", "El importe debe coincidir con lo que sume los importes de efectivo, cheques, deposito y retenciones", "info");
                return;
            }
            this.enviarAviso(bodyJson);
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

    __validarImpTotalRec(array) {
        let subtotal = 0;
        for(let i = 1; i < array.length; i++) {subtotal += array[i]}
        const resultado = array[0] === subtotal ? true : false;
        return resultado;
    }
}