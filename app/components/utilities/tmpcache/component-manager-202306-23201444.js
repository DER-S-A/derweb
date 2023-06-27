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

    /**
     * Limpia el contenido de un contenedor div.
     * @param {string} xidContainer Id de contenedor
     */
    clearContainer(xidContainer) {
        document.getElementById(xidContainer).innerHTML = "";
    }

    /**
     * Permite obtener un template HTML.
     * @param {string} xurl URL donde se ubica el template
     * @param {Callback function} xcb_funcion Función callback para procesar el template.
     */
    getTemplate(xurl, xcb_funcion) {
        fetch(xurl, {
            method: "GET",
            cache: "no-cache",
            headers: {
                "Content-Type": "text/html"
            }
        }).then(response => response.text())
            .then(html => {
                xcb_funcion(html);
            });
    }

    /**
     * Permite reemplazar los parámetros de un template HTML.
     * @param {string} xhtml Establece el código html obtenido de un template a procesar.
     * @param {string} xname Establece el nombre del parámetro.
     * @param {string} xvalue Establece el valor del parámetro.
     */
    setTemplateParameters(xhtml, xname, xvalue) {
        if (typeof(xhtml) === 'object')
            return null;
        return xhtml.replaceAll("{" + xname + "}", xvalue);
    }

    /**
     * Crea un dom element.
     * @param {etiqueta} xvalue etiqueta html
     * @param {string} xclass nombre de la clase de la etiqueta html
     * @param {int or string} xid id de la etiqueta html
     * @param {array} xatributes primer valor es lo de la izquierda del igual por ejemplo type y segundo valor es el de la derecha por ejemplo button.
     * @return {objDomElement} 
     */

    crearElementDom(xvalue, xclass="", xid="", xatributes = []) {
        let objDomElement = document.createElement(xvalue);
        if(xclass != "") objDomElement.className = xclass;
        if(xid != "") objDomElement.id = xid;
        if(xatributes.length!=0) {
            let valorA;
            let valorB;
            xatributes.forEach((value,i)=>{
                if(i===0||i%2===0){
                    valorA = value;
                } else {
                    valorB = value;
                    objDomElement.setAttribute(valorA, valorB);
                }
            })
        }   
        return objDomElement;
    }
}