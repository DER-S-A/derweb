/**
 * Esta clase contiene la funcionalidad de rendiciones.
 */
class Rendiciones extends ComponentManager {
    /**
     * Constructor de clase
     * @param {string} xidcontainer Establece el id. del contenedor.
     */
    constructor(xidcontainer) {
        super();
        this.appContainer = document.getElementById(xidcontainer);
        // Limpio el contenedor principal.
        if (xidcontainer !== null)
            this.clearContainer(xidcontainer);
    }

    async generateComponent() {
        this.getRendiciones();
        const formTotales = this.__generateForm('form-totalesRendiciones mx-3');
        const contenedorTotales = this.crearElementDom('div', 'contenedor-totales my-4', 'contenedorTotales');
        contenedorTotales.innerHTML = "<h5 class='mx-2 mt-1'>Totales</h4><hr>";
        this.appContainer.appendChild(contenedorTotales).appendChild(formTotales);
        const contenedorEnvioRendi = this.crearElementDom('div', 'contenedor-envioRendi my-4', 'c-envioRendi');
        this.appContainer.append(contenedorEnvioRendi);
        const formEnvio = this.__generateForm('form-enviarRendicion', true);
        contenedorEnvioRendi.append(formEnvio);
        this.__enviarRendicion();
    }

    /**
    * Obtiene la lista de rendiciones del venededor actualmente logueado.
    */
    async getRendiciones() {
        const templateDataTable = document.createElement('div');
        templateDataTable.className = 'gridContainerRendicion';
        templateDataTable.innerHTML = " \
        <table id='tabla-rendiciones' class='table table-bordered table-hover' style='font-size: 11px; width: 100%' data-page-length='10'> \
            <thead style='background: #009ada; color: white;'>\
                <th>Nro Cuenta</th>\
                <th>Razón Social</th>\
                <th>Fecha</th>\
                <th>Nº Recibo</th>\
                <th>Efectivo</th>\
                <th>Cheques</th>\
                <th>Depósito</th>\
                <th>Retenciones</th>\
                <th>Total</th>\
            </thead>\
        </table>";
        this.appContainer.innerHTML = `<h1 class='my-5' style='text-align:center'>Rendiciones de Cobranza</h1>`
        this.appContainer.append(templateDataTable);
        var dataTableMov = $("#tabla-rendiciones").DataTable({
            searching: true,
            paging: true,
            responsive: true,
            scrollY: 260
        });
        const objCacheUtils = new CacheUtils("derweb", false);
        const url = new App().getUrlApi("rendiAbi");
        const id_vendedor = objCacheUtils.get("sesion")["id_vendedor"];
        const body = {id_vendedor: id_vendedor}
        const movimientos = await new Promise((resolve, reject) => {
            new APIs().call(url, body, "POST", datos => {console.log(datos)
                resolve(datos.movimientos)
            }, true, error => {reject(error)})
        });
        if(movimientos == "") {
            return;
        }
        objCacheUtils.set("id_rendicion", movimientos[0].id_rendicion);
        let totales = [0, 0, 0, 0, 0]; //imp efectivo, impor chques, imp dep, imp ret, tot reb 
        movimientos.forEach(movto => {
            totales = this.__generarTotalesInputs(totales, [movto.importe_efectivo, movto.importe_cheques, movto.importe_deposito, movto.importe_retenciones, movto.total_recibo]);
            const ofecha = new Date(movto.fecha);
            let sfecha = ofecha.getDate().toString() + "/" + (ofecha.getMonth()+1).toString() + "/" + ofecha.getFullYear().toString();
            const arrTabla = [
                movto.cliente_cardcode,
                movto.cliente,
                sfecha,
                movto.numero_recibo,
                movto.importe_efectivo,
                movto.importe_cheques,
                movto.importe_deposito,
                movto.importe_retenciones,
                movto.total_recibo
            ]
            dataTableMov.row.add(arrTabla);
        });
        dataTableMov.draw();
        this.__llenarInputsTotales(totales);
    }

    /**
    * Permite generar componente formulario.
    */
    __generateForm(clase, option2 = false) {
        let objForm;
        let formData;
        if(option2) {
            formData = this.__generateDataForm('textarea', 'form-control', 3);
            objForm = new Form(formData, clase, 'Enviar').generateComponent();
            objForm.querySelector('#input-totEfecCobrado').setAttribute('disabled', '');
            objForm.querySelector('#input-efvoEntdo').setAttribute('disabled', '');
            let inputs = objForm.querySelectorAll('input');
            inputs.forEach(input => {
                input.value = 0;
            });
        } else {
            formData = this.__generateDataForm();
            objForm = new Form(formData, clase).generateComponent();
            let inputs = objForm.querySelectorAll('input');
            inputs.forEach(input => {
                input.setAttribute('disabled', '');
            });
        }
        return objForm;
    }

    /**
    * Permite preparar los datos para generar el formulario.
    * @param {string} textArea establece la etiqueta a usar.
    * @param {string} clase establece la clase css a usar.
    * @param {string} row establece un atributo a usar (tiene q ser numerico). 
    * @return {objElement}
    */
    __generateDataForm(textArea, clase, row) {
        const claseLabel = "form-label";
        const claseInput = "form-control";
        let obj;
        if(textArea == null) {
            obj = [
                {tag:'input', id:'input-importeEfec-ret', class:claseInput, classL:claseLabel, name:'importeEfec', type:'number', content:'Efectivo:'},
                {tag:'input', id:'input-importeCheque-ret', class:claseInput, classL:claseLabel, name:'importeCheque', type:'number', content:'Cheques:'},
                {tag:'input', id:'input-importeDepo-ret', class:claseInput, classL:claseLabel, name:'importeDepo', type:'number', content:'Depósitos / Transferencias:'},
                {tag:'input', id:'input-importeRet-ret', class:claseInput, classL:claseLabel, name:'importeRet', type:'number', content:'Retenciones:'},
                {tag:'input', id:'input-importeRec-ret', class:claseInput, classL:claseLabel, name:'importeRec', type:'number', content:'Total Recibos:'}
            ]
        } else {
            obj = [
                {tag:'input', id:'input-totEfecCobrado', class:claseInput, classL:claseLabel, name:'totalEfec', type:'number', content:'Total efectivo cobrado:'},
                {tag:'input', id:'input-ret', class:claseInput, classL:claseLabel, name:'retiro', type:'number', content:'Retiró:'},
                {tag:'input', id:'input-efectDepo', class:claseInput, classL:claseLabel, name:'efectDepo', type:'number', content:'Efectivo depositado:'},
                {tag:'input', id:'input-gastosTrans', class:claseInput, classL:claseLabel, name:'gastosTransporte', type:'number', content:'Gastos de transporte:'},
                {tag:'input', id:'input-gastosGral', class:claseInput, classL:claseLabel, name:'gastosGral', type:'number', content:'Gastos generales:'},
                {tag:'input', id:'input-efvoEntdo', class:claseInput, classL:claseLabel, name:'efvoEntregado', type:'number', content:'Efectivo entregado:'},
                {tag:textArea, id:'textarea-observaciones', name:'observaciones', class:clase, classL:claseLabel, row:row, content:'Observaciones:'}
            ]
        }
        
        return obj;
    }

    /**
    * Permite generar los totales que se usaran para completar los inputs de totales.
    * @param {Array} arrTotales los totales en cero.
    * @param {Array} arrParciales los valores parciales de los campos de importes de cada registro. 
    * @return {Array}
    */
    __generarTotalesInputs(arrTotales, arrParciales) {
        const nuevosTotales = arrTotales.map((total, i) => total + parseFloat(arrParciales[i]));
        return nuevosTotales;
    }

    /**
    * Permite llenar los inputs de totales.
    * @param {Array} arrTotales los totales para pegar en los inputs. 
    */
    __llenarInputsTotales(arrTotales) {
        // Primmeros campos
        let arrElements = ['input-importeEfec-ret', 'input-importeCheque-ret', 'input-importeDepo-ret', 'input-importeRet-ret', 'input-importeRec-ret'];
        arrElements.forEach((element, i) => {
            document.getElementById(element).value = arrTotales[i].toFixed(2);
        });
        // Segundos campos
        document.getElementById('input-totEfecCobrado').value = arrTotales[0].toFixed(2);
        let totalEntre = arrTotales[0];
        document.getElementById('input-efvoEntdo').value = totalEntre.toFixed(2);
        this.__generarEventoChange(totalEntre);
    }

    /**
    * Permite realizar la operacion que muestra el input de efectivo entregado.
    * @param {float} total Es el total de efectivo declarado.
    * @return {number} 
    */
    __generarTotalEntregar(total) {
        let retiro = parseFloat(document.getElementById('input-ret').value);
        let depositado = parseFloat(document.getElementById('input-efectDepo').value);
        let transporte = parseFloat(document.getElementById('input-gastosTrans').value);
        let generales = parseFloat(document.getElementById('input-gastosGral').value);
        total = total - (retiro + depositado + transporte + generales);
        return total;
    }

    /**
    * Permite realizar la operacion que muestra el input de efectivo entregado.
    * @param {number} total Es el total de efectivo declarado.
    */
    __generarEventoChange(totalEntre) {
        let domEntregado = document.getElementById('input-efvoEntdo');
        const inputs = document.querySelectorAll('input:not([disabled])');
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                const total = this.__generarTotalEntregar(totalEntre);
                domEntregado.value = total.toFixed(2);
            });
        });
    }

    __enviarRendicion() {
        const objCacheUtils = new CacheUtils("derweb", false);
        const oEnviar = document.getElementById('btn-form-enviarRendicion');
        oEnviar.addEventListener('click', e => {
            e.preventDefault();
            let id_rendicion = objCacheUtils.get("id_rendicion");
            if(id_rendicion == "") {
                swal('ERROR', 'No tiene pre-rendiciones pendientes a enviar', 'error')
                return;
            }
            const objBody = {
                idRendicion: id_rendicion,
                importe_retiro: document.getElementById('input-ret').value,
                efectivo_depositado: document.getElementById('input-efectDepo').value,
                gastos_transporte: document.getElementById('input-gastosTrans').value,
                gastos_generales: document.getElementById('input-gastosGral').value,
                observaciones: document.getElementById('textarea-observaciones').value
            }
            console.log(objBody)
            if(parseFloat(document.getElementById('input-efvoEntdo').value) > -1 ) {
                this.__enviarAviso(objBody);
                objCacheUtils.set("id_rendicion", "");
            } else {
                swal('ERROR RECAUDACION', 'El campo de efectivo entregado tiene q ser ceo o un numero mayor.', 'error');
                return;
            }
        });
    }

    /**
    * Envio aviso de pago al endpoint.
    * @param {Object} value
    */
    __enviarAviso(value) {
        const url = (new App()).getUrlApi("enviarRendi");
        new APIs().call(url, value, "POST", (datos) => {
            console.log(datos)
            swal({title:datos.mensaje, icon:datos.result})
            .then(() => {
                window.open(datos.archivo_pdf, "_blank");
                window.location.href = "/derweb/app/main-vendedores.php";
            })
        }, true)

    }
}