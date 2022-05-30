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
    deleteContent (xidContent, xidContentAEliminar) {
        var objContenedor = document.getElementById(xidContent);
        var objContenedorAEliminar = document.getElementById(xidContentAEliminar);
        if (objContenedorAEliminar != null)
            objContenedor.removeChild(objContenedorAEliminar);
    }

    /**
     * Agrega una fila del layout de bootstrap.
     * @returns DOM element
     */
     addBootstrapRow() {
        var objRow = document.createElement("div");
        objRow.classList.add("row");
        return objRow;
    }

    /**
     * Agrega una columna del layout de bootstrap.
     * @param {array} xClassColumns Array con las clases a asociar al div.
     * @returns DOM element
     */
    addBoostralColumn(xClassColumns) {
        var objCol = document.createElement("div");
        xClassColumns.forEach(xElement => {
            objCol.classList.add(xElement);
        });
        return objCol;
    }
}