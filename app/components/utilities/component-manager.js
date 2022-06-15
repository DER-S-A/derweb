/**
 * Clase: ComponentManager
 * Descripción:
 *  Esta clase contiene métodos para manejar funcionalidades de componentes.
 */

class ComponentManager {
    /**
     * Permite eliminar el conteniedo de un contenedor.
     * @param {string} xidContent 
     * @param {string} xidContentAEliminar 
     */
    __deleteContent (xidContent, xidContentAEliminar) {
        var objContenedor = document.getElementById(xidContent);
        var objContenedorAEliminar = document.getElementById(xidContentAEliminar);
        if (objContenedorAEliminar != null)
            objContenedor.removeChild(objContenedorAEliminar);
    }

    /**
     * Agrega una fila del layout de bootstrap.
     * @returns DOM element
     */
     __addBootstrapRow() {
        var objRow = document.createElement("div");
        objRow.classList.add("row");
        return objRow;
    }

    /**
     * Agrega una columna del layout de bootstrap.
     * @param {array} xClassColumns Array con las clases a asociar al div.
     * @returns DOM element
     */
    __addBoostralColumn(xClassColumns) {
        var objCol = document.createElement("div");
        xClassColumns.forEach(xElement => {
            objCol.classList.add(xElement);
        });
        return objCol;
    }

    /**
     * Verifica si existe un componente por su ID.
     * @param {string} xid Id. del elemento DOM a verificar
     * @returns {boolean}
     */
    __existsComponent(xid) {
        var obj = document.getElementById(xid);
        if ((obj === null) || (obj === undefined))
            return false;
        else
            return true;
    }

    clearContainer(xidContainer) {
        document.getElementById(xidContainer).innerHTML = "";
    }
}