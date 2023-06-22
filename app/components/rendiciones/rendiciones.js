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

    generateComponent() {
        const table = this.getRendiciones();
        this.appContainer.append(table);
    }

    /**
    * Obtiene la lista de rendiciones del venededor actualmente logueado.
    */
    getRendiciones() {
        let templateDataTable = document.createElement('gridContainerRendicion');
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

        var dataTableClientes = $("#tabla-rendiciones").DataTable({
            searching: true,
            paging: true,
            responsive: true,
            scrollY: 260
        });
        return templateDataTable
    }
}