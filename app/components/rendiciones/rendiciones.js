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
            new APIs().call(url, body, "POST", datos => {
                resolve(datos.movimientos)
            }, true, error => {reject(error)})
        });
        console.log(movimientos)
        movimientos.forEach(movto => {
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
        } else {console.log('1')
            formData = this.__generateDataForm();
            objForm = new Form(formData, clase).generateComponent();
        }
        //this.__validarCamposFront(objForm);
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

}