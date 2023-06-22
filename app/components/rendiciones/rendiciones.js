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
            new APIs().call(url, body, "POST", datos => {console.log('0')
                resolve(datos.movimientos)
            }, true, error => {reject(error)})
        });
        console.log(movimientos)
        movimientos.forEach(movto => {
            const arrTabla = [
                movto.cliente_cardcode,
                movto.cliente,
                movto.fecha,
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
    __generateForm() {
        const formData = this.__generateDataForm();
        let objForm = new Form(formData, "form-totalesRendiciones").generateComponent();
        //this.__validarCamposFront(objForm);
        return objForm;
    }

    /**
    * Permite preparar los datos para generar el formulario.
    * @return {objElement}
    */
    __generateDataForm() {
        const claseLabel = "form-label";
        const claseInput = "form-control";
        const obj = [
            {tag:'input', id:'input-importeEfec-ret', class:claseInput, classL:claseLabel, name:'importeEfec', type:'number', content:'Efectivo:'},
            {tag:'input', id:'input-importeCheque-ret', class:claseInput, classL:claseLabel, name:'importeCheque', type:'number', content:'Cheques:'},
            {tag:'input', id:'input-importeDepo-ret', class:claseInput, classL:claseLabel, name:'importeDepo', type:'number', content:'Depósitos / Transferencias:'},
            {tag:'input', id:'input-importeRet-ret', class:claseInput, classL:claseLabel, name:'importeRet', type:'number', content:'Retenciones:'},
            {tag:'input', id:'input-importeRec-ret', class:claseInput, classL:claseLabel, name:'importeRec', type:'number', content:'Total Recibos:'}
        ]
        return obj;
    }
}