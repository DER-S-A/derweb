/**
 * Esta clase contiene el control datagrid.
 * Fecha: 03/02/2021
 * Autor: LINFOW - Leonardo D. Zulli
 */

class LFWDataGrid {
    /**
     * Constructor de la clase.
     * @param {string} xidControl Nombre del Id. del datagrid especificado en el HTML.
     * @param {string} xCampoClave Nombre del campo clave. Por default es vacío.
     */
    constructor(xidControl, xCampoClave = "") {
        this.__columns = new Array();
        this.__idControl = xidControl;
        this.__tabla = null;
        this.__contenedor = document.getElementById(this.__idControl);
        this.__idControlTable = this.__idControl + "_" + "tabla";
        this.__idControlHeader = this.__idControl + "_tabla_header";
        this.__idControlBody = this.__idControl + "_tabla_body";
        this.__dataSet = new Array();
        this.__rows = new Array();
        this.__rowCount = 0;    
        this.__cacheName = this.__idControl + "_rows";
        this.__permitirEditar = false;
        this.__permitirEliminar = false;
        this.__campoClave = xCampoClave;
        this.__asociatedFormId = "";
        this.__permitirFiltros = false;
        this.__permitirOrden = false;
        this.__ultimoCampoOrdenado = "";
        this.__useCheckBox = false;
        this.__optionCheckFunctionName = "";
        this.__editJavascriptFunctionName = "";
        this.__iconEditButton = "";
        this.__editButtonTitle = "";

        this.__createControl();
    }

    /**
     * Crea la estructura HTML de la tabla.
     */
     __createControl() {
        this.__contenedorResponsive = document.createElement("div");
        this.__contenedorResponsive.classList.add("table-responsive");

        this.__tabla = document.createElement("table");
        this.__tablaTHead = document.createElement("thead");
        this.__tablaTBody = document.createElement("tbody");

        // Le pongo un id para identificar al etiqueta html
        this.__tabla.id = this.__idControlTable;
        this.__tabla.classList.add("table");
        this.__tabla.classList.add("table-hover");
        this.__tabla.classList.add("lfw-datagrid");

        this.__tablaTHead.id = this.__idControlHeader;
        this.__tablaTHead.classList.add("lfw-datagridHeader");

        this.__tablaTBody.id = this.__idControlBody;
        this.__tablaTBody.classList.add("lfw-dataGridBody");

        // Agrego las etiquetas a la tabla.
        this.__contenedorResponsive.appendChild(this.__tabla);
        this.__contenedor.appendChild(this.__contenedorResponsive);
        this.__tabla.appendChild(this.__tablaTHead);
        this.__tabla.appendChild(this.__tablaTBody);

        // Si no viene seteado el nombre de un campo clave, entonces,
        // creo un campo rowid para identificar la fila.
        if (this.__campoClave === "") {
            this.agregarColumna("ID.", "rowid", "numeric", 0, false);
            this.__campoClave = "rowid";
        }
    }

    /**
     * Obtiene el key de sessionStorage en donde se guarda la información de este grid.
     * @returns {string} Obtiene el nombre de la caché.
     */
    getCacheName() {
        return this.__cacheName;
    }

    /**
     * Indica si muestra el ícono para editar un registro.
     * @param {boolean} xvalue
     */
    setPermitirEditarRegistro(xvalue) {
        this.__permitirEditar = xvalue;
    }

    /**
     * Indica si muestra el ícono para eliminar un registro.
     * @param {boolean} xvalue 
     */
     setPermitirEliminarRegistro(xvalue) {
        this.__permitirEliminar = xvalue;
    }

    /**
     * Id. de formulario al que se encuentra asociada el datagrid.
     * @param {string} xidForm 
     */
    setAsociatedFormId(xidForm) {
        this.__asociatedFormId = xidForm;
        this.__createInputSelectedRow();
        this.__createInputEditionMode();
    }

    /**
     * Establece el nombre de la función javascript a ejecutar en el botón
     * edit.
     * @param {string} xvalue 
     */
    setEditJavascriptFunctionName(xvalue) {
        this.__editJavascriptFunctionName = xvalue;
    }

    /**
     * Establece el ícono para personalizar el botón de edición.
     * @param {string} xvalue
     */
    setIconEditButton(xvalue) {
        this.__iconEditButton = xvalue;
    }

    /**
     * Establece el título del botón de edición.
     * {@param} xvalue
     */
    setEditButtonTitle(xvalue) {
        this.__editButtonTitle = xvalue;
    }

    /**
     * Crea el input hidden para guardar el id seleccionado editar
     * una fila.
     */
    __createInputSelectedRow() {
        var formulario = document.getElementById(this.__asociatedFormId);
        var input_selected_id = document.createElement("input");
        input_selected_id.setAttribute("type", "hidden");
        input_selected_id.id = this.__idControl + "_selectedRow";
        input_selected_id.name = this.__idControl + "_selectedRow";
        formulario.appendChild(input_selected_id);        
    }

    /**
     * Genero input para el modo de edición.
     */
    __createInputEditionMode() {
        var formulario = document.getElementById(this.__asociatedFormId);
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.id = this.__idControl + "_editionMode";
        input.name = this.__idControl + "_editionMode";
        input.value = 0;
        formulario.appendChild(input);              
    }

    /**
     * Indica si se permite realizar filtros.
     * @param {boolean} xvalue 
     */
    setPermitirFiltros(xvalue) {
        this.__permitirFiltros = xvalue;;
    }

    /**
     * Indica si el grid va a tener la funcionalidad de ordenamiento.
     * @param {boolean} xvalue 
     */
    setPermitirOrden(xvalue) {
        this.__permitirOrden = xvalue;
    }

    /**
     * Indica que debe agregar una columna con checkboxes para
     * permitir selección múltiple.
     */
    setUseCheckBox(xvalue = true) {
        this.__useCheckBox = xvalue;
        this.agregarColumna("Sel.", "selected", "numeric", 20, true, true);
    }

    /**
     * Establece la funcion javascript que se ejecuta al hacer clic sobre
     * un option.
     */
    setFunctionCheckBoxName(xvalue) {
        this.__optionCheckFunctionName = xvalue;
    }

    /**
     * Este método permite agregar y definir columnas a mostrar en el datagrid
     * @param {string} xtitulo Título de columna
     * @param {string} xcampo Nombre del campo asociado a la columna.
     * @param {string} xtipodato Tipo de datos. (String | Numeric)
     * @param {double} xancho Ancho de columnas. Por defecto -1px, HTML decide el ancho de columna
     * @param {boolean} xvisible Indica si se debe mostrar la columna o no.
     * @param {boolean} xuseCheckBox Indica si esta columna contiene checkbox para seleccionar.
     */
    agregarColumna(xtitulo, xcampo, xtipodato, xancho = -1, xvisible = true, xuseCheckBox = false) {
        // Array que contiene la definición de las propiedades de las columnas.
        var columna = {
            titulo: xtitulo,
            ancho: xancho,
            campo: xcampo,
            tipodato: xtipodato,
            visible: xvisible,
            orden: 'none',
            use_check: xuseCheckBox
        };

        // Agrego la columna la propiedad __columns que corresponde al array de columnas.
        this.__columns.push(columna);
        this.__crearColumnas();
    }

    /**
     * Creo las columnas en el HTML que va a tener el datagrid.
     */
     __crearColumnas() {
        var htmlColHead = null;
        var htmlInputText = null;
        this.__tablaTHead.innerHTML = "";

        // Recorro las columnas definidas en array __columns mediante agregarColumna().
        this.__columns.forEach((xcolumna, xindex) => {
            // Solo creo en el HTML las columnas que son visibles.
            if (xcolumna["visible"] === true) {
                htmlColHead = document.createElement("th");
                if (xcolumna["ancho"] !== -1)
                    htmlColHead.setAttribute("style", "width: " + xcolumna["ancho"] + "px;");
                htmlColHead.innerHTML = xcolumna["titulo"] + " " + "<i id=\"" + this.__idControl + "_" + xcolumna["campo"] + "\"></i>";
                htmlColHead.classList.add("text-center");

                if (this.__permitirFiltros) {
                    htmlInputText = document.createElement("input");
                    htmlInputText.classList.add("form-control");
                    htmlInputText.classList.add("lfw-input-filters");
                    htmlInputText.id = this.__idControl + "-" + xcolumna["campo"];
                    htmlInputText.name = this.__idControl + "-" + xcolumna["campo"];

                    // Este evento se ejecuta mientras el usuario va tipeando en el input.
                    htmlInputText.addEventListener("keyup", (event) => {
                        this.__filter(xcolumna["campo"]);
                    }, false);

                    htmlColHead.appendChild(htmlInputText);
                }

                // Este evento se ejecuta al hacer clic sobre el encabezado de una
                // columna si es que permite ordenar por columna.
                if (this.__permitirOrden)
                    htmlColHead.addEventListener("click", (event) => {
                        this.__sortByColumn(xcolumna, xindex);
                    }, false);

                this.__tablaTHead.appendChild(htmlColHead);
            }
        });

        if (this.__permitirEditar || this.__permitirEliminar) {
            var th = document.createElement("th");
            th.innerHTML = "Acciones";
            th.setAttribute("style", "width: 50px;");
            this.__tablaTHead.appendChild(th);
        }           
     
    }    

    /**
     * Dato a mostrar en cada fila del grid.
     * @param {string} xdato 
     */
    agregarFila(xrow) {
        var datagrid_cache = new Array();
        this.__rows = [];
        this.__rowCount = 0;

        // Lleno la grilla con los datos.
        this.__dataSet.push(xrow);
        this.__fill(this.__dataSet);

        // Guardo todos los datos del grid incluyendo sus propiedades en SessionStorage.
        datagrid_cache = {
            columns: this.__columns,
            rows: this.__rows,
            rowCount: this.__rowCount
        };
        sessionStorage.setItem(this.__cacheName, JSON.stringify(datagrid_cache));
        this.__generateInputFormRows();
    }

    /**
     * Llena el datagrid con las filas que hay que llenar.
     * @param {array} xrows Array con las filas a llenar en el datagrid
     */
    __fill(xrows) {
        this.__tablaTBody.innerHTML = "";

        xrows.forEach((xrow, index) => {
            var tr = document.createElement("tr");
            var td = null;
            var tdEdicion = null;
            var checkbox = null;

            // Valido si xrow es null porque cuando se elimina un elemento del array,
            // queda la posición en null. En ese caso corto la ejecución para que no
            // genere error de javascript.
            if (xrow === null)
                return;

            tr.id = this.__idControl + "_row_" + this.__rowCount;

            // Recorro las columnas para ir completando las filas del grid con los datos
            // a mostrar.
            for (var i = 0; i < this.__columns.length; i++) {
                // Solo cargo los datos de las columnas que son visibles.
                if (this.__columns[i]["visible"] === true) {
                    // Si el valor del campo clave viene en 0 (cero) entonces lo calculo asignando
                    // un valor correlativo.

                    if (xrow[this.__campoClave] === 0)
                        xrow[this.__campoClave] = index + 1;

                    td = document.createElement("td");
                    if (this.__columns[i]["use_check"]) {
                        checkbox = document.createElement("input");
                        checkbox.id = this.__columns[i]["campo"] + "_" + xrow[this.__campoClave];
                        checkbox.name = this.__columns[i]["campo"] + "[]";
                        checkbox.type = "checkbox";
                        checkbox.value = xrow[this.__campoClave];

                        // Agrego un evento click al checkbox para marcar la fila
                        // seleccionada.
                        checkbox.addEventListener("click", () => {
                            // Al seleccionar el checkbox marco el registro como seleccionado
                            // y viceversa.
                            var selected_row = new Array();
                            selected_row = this.getRowByCampoClave(checkbox.value);
                            selected_row[0].selected = checkbox.checked;
                            this.updateDataRow(selected_row[0], false);
                            this.__optionCheckFunctionName(checkbox);
                        });

                        td.appendChild(checkbox);
                    } else {
                        // Verifico si el tipo de datos es numérico para que formatee.
                        if (this.__columns[i]["tipodato"] === "numeric")
                            xrow[this.__columns[i]["campo"]] = parseFloat(xrow[this.__columns[i]["campo"]]).toFixed(2);

                        td.innerHTML = xrow[this.__columns[i]["campo"]];

                        // Verifico el tipo de datos para la alineación
                        if ((this.__columns[i]["tipodato"] === "numeric") 
                                || (this.__columns[i]["tipodato"] === "datetime"))
                            this.__formatearValorNumerico(td);
                    }
                    tr.appendChild(td);
                }
            }
            this.__tablaTBody.appendChild(tr);

            // Si permite editar, entonces, muestro el ícono editar.
            if (this.__permitirEditar) {
                let jsFunctinName = "";
                let iconEditButton = "";
                let editButtonTitle = "";
                tdEdicion = document.createElement("td");

                // Si el nombre del a función de javascript no está establecido le
                // pongo un nombre predeterminado.
                if (this.__editJavascriptFunctionName === "")
                    jsFunctinName = this.__idControl + "_edit(" + xrow[this.__campoClave] + ");"
                else
                    jsFunctinName = this.__editJavascriptFunctionName + "(" + xrow[this.__campoClave] + ");";

                if (this.__iconEditButton === "")
                    iconEditButton = "fa-edit";
                else
                    iconEditButton = this.__iconEditButton;

                if (this.__editButtonTitle === "")
                    editButtonTitle = "Editar";
                else
                    editButtonTitle = this.__editButtonTitle;

                tdEdicion.innerHTML = "<a href='javascript:" + jsFunctinName + "' title='" + editButtonTitle + "'><i class=\"fa " + iconEditButton + " fa-lg\"></i></a> &nbsp;";
                tr.appendChild(tdEdicion);
            }

            // Si permite eliminar, entonces muestro el ícono para eliminar una fila.
            if (this.__permitirEliminar) {
                if (tdEdicion === null)
                    tdEdicion = document.createElement("td");
                tdEdicion.innerHTML += "<a href='javascript:" + this.__idControl + "_delete(" + xrow[this.__campoClave] + ");'><i class=\"far fa-trash-alt fa-lg\"></i></a>";
                tr.appendChild(tdEdicion);
            }


            this.__rows.push(xrow);
            this.__rowCount++;
        });
    }

    /**
     * Genera las filas en un input hidden y lo insreta en un formulario.
     */
    __generateInputFormRows() {
        var formulario = document.getElementById(this.__asociatedFormId);
        var input_hidden_row = document.createElement("input");
        var rows = this.getRows();
        input_hidden_row.setAttribute("type", "hidden");
        input_hidden_row.id = this.__idControl + "_rows";
        input_hidden_row.name = this.__idControl + "_rows";
        input_hidden_row.value = JSON.stringify(rows);

        var input_eliminar = document.getElementById(this.__idControl + "_rows");
        if (input_eliminar !== null)
            formulario.removeChild(input_eliminar);

        formulario.appendChild(input_hidden_row);
    }

    /**
     * Permite formatear los valores numéricos
     * @param {DOMObject} xtd Componente HTML de la columna
     */
     __formatearValorNumerico(xtd) {
        xtd.setAttribute("style", "text-align: right;");
    }    

    /**
     * Obtiene todos los datos de la grilla incluyendo definiciones de columnas, cantidad de filas y las
     * filas propiamente dicha.
     * @returns {Array}
     */
    getDataGrid() {
        return JSON.parse(sessionStorage.getItem(this.getCacheName()));
    }

    /**
     * Obtiene los registros del datagrid.
     */
    getRows() {
        var result = [];
        if (sessionStorage.getItem(this.getCacheName()) === null)
            result = null;
        else
            result = JSON.parse(sessionStorage.getItem(this.getCacheName())).rows;
        return result;
    }

    /**
     * Devuelve la cantidad de filas.
     * @returns int
     */
    getRowCount() {
        let datagrid = this.getDataGrid();
        return datagrid.rowCount;
    }

    /**
     * Refresca los datos del datagrid.
     */
    refresh() {
        var rows = this.getRows();
        this.__fill(rows);
    }

    /**
     * Permite eliminar un registro a partir del valor seleccionado según el campo
     * clave.
     * @param {int} xid Id. de registro definido en campo clave.
     */
    deleteRowByCampoClave(xid) {
        var rows = this.getRows();
        var dataGridProperties = this.getDataGrid();
        rows.find((xelement, xindex) => {
            if (xelement !== null)
                if (parseInt(xelement[this.__campoClave]) === parseInt(xid))
                    delete rows[xindex];
        });
        dataGridProperties.rows = rows;
        this.__dataSet = rows;
        sessionStorage.setItem(this.getCacheName(), JSON.stringify(dataGridProperties));
        this.refresh();
        this.__generateInputFormRows();
    }

    /**
     * Permite obtener la fila seleccionada actualmente.
     * @param {int} xid Id. del campo clave.
     */
    getRowByCampoClave(xid) {
        var rows = this.getRows();
        return rows.filter((xelement) => {
            // Valido que el elemento no sea nulo por las dudas que eliminen
            // un registro antes de editar otro.
            if (xelement !== null)
                if (parseInt(xelement[this.__campoClave]) === parseInt(xid))
                    return true;
            return false;
        }, false);
    }

    /**
     * Actualiza los datos de la fila selecionada
     * @param {array} xrow Fila a actualizar
     * @param {array} xrefreshGrid Indica si debe refrescar la grilla.
     */
    updateDataRow(xrow, xrefreshGrid = true) {
        var dataGridProperties = this.getDataGrid();
        dataGridProperties.rows.find((xelement, xindex) => {
            if (xelement !== null)
                if (parseInt(xelement[this.__campoClave]) === parseInt(xrow[this.__campoClave]))
                    dataGridProperties.rows[xindex] = xrow;
        });
        sessionStorage.setItem(this.getCacheName(), JSON.stringify(dataGridProperties));
        if (xrefreshGrid)
            this.refresh();
        this.__generateInputFormRows();
    }

    /**
     * Permite realizar filtrar por un campo en base a lo que el usuario busca.
     * Para evitar las diferencias entre mayúsculas y minúsculas, se convierte todo
     * a mayúsculas para la comparación.
     * @param {string} xcampo Nombre del campo a buscar
     */
    __filter(xcampo) {
        var dataGridProperties = this.getDataGrid();
        var filteredRows = dataGridProperties.rows.filter((xelement) => {
            var valorBuscado = document.getElementById(this.__idControl + "-" + xcampo).value;
            if (xelement[xcampo].toUpperCase().startsWith(valorBuscado.toUpperCase())) {
                return true;
            } else {
                if (xelement[xcampo].toUpperCase().includes(valorBuscado.toUpperCase())) {
                    return true;
                }
            }
        });
        this.__fill(filteredRows);
    }

    /**
     * Ordeno las filas según el campo seleccionado.
     * @param {string} xcampo 
     */
    __sortByColumn(xcolumna, xindex) {
        var dataGridProperties = this.getDataGrid();

        // Verifico si hago click en sobre la misma columna que ordené por última vez,
        // si es así hago el orden inverso, en caso contrario, ordeno en forma ascendente
        // por la nueva columna que se está ordenando.
        if (this.__ultimoCampoOrdenado === xcolumna["campo"])
            if (xcolumna["orden"] === "none" || xcolumna["orden"] === "desc") {
                dataGridProperties.rows.sort((a, b) => {
                    return a[xcolumna["campo"]].localeCompare(b[xcolumna["campo"]]);
                });
                xcolumna["orden"] = "asc";
            }
            else {
                dataGridProperties.rows.sort((a, b) => {
                    return b[xcolumna["campo"]].localeCompare(a[xcolumna["campo"]]);
                });
                xcolumna["orden"] = "desc";
            }
        else {
            dataGridProperties.rows.sort((a, b) => {
                return a[xcolumna["campo"]].localeCompare(b[xcolumna["campo"]]);
            });
            xcolumna["orden"] = "asc";
        }

        this.__cambiarIconoOrdenEnColumna(xcolumna);
        
        // Inicializo la propiedad orden en none antes de actualizar por qué
        // campo fué el último orden.
        dataGridProperties.columns.forEach((xelemento, xidx) => {
            xelemento["orden"] = "none";
            dataGridProperties.columns[xidx] = xelemento;
        });

        // Actualizo la propiedad orden de la columna.
        dataGridProperties.columns[xindex] = xcolumna;
        // Indico por qué campo se ordenó.
        this.__ultimoCampoOrdenado = xcolumna["campo"];
        this.__fill(dataGridProperties.rows);

        // Actualizo los valores de las propiedades del datagrid en sessionStorage.
        sessionStorage.setItem(this.getCacheName(), JSON.stringify(dataGridProperties));
    }

    /**
     * Permite cambiar el ícono de la columna indicando por qué se encuentra ordenada.
     * @param {array} xcolumna Columna seleccionada por el usuario.
     */
    __cambiarIconoOrdenEnColumna(xcolumna) {
        var icono = document.getElementById(this.__idControl + "_" + xcolumna["campo"]);
        var icono_columna_anterior = document.getElementById(this.__idControl + "_" + this.__ultimoCampoOrdenado);
        if (icono_columna_anterior !== null)
            icono_columna_anterior.setAttribute("class", "");
        if (xcolumna["orden"] === "asc") {
            icono.setAttribute("class", "");
            icono.classList.add("fas");
            icono.classList.add("fa-sort-up");
        }
        else {
            icono.setAttribute("class", "");
            icono.classList.add("fas");
            icono.classList.add("fa-sort-down");
        }
    }

    /**
     * Inicia el modo de edición de fila
     * @param {int} xid Identificador de fila
     */
    beginEditionRow(xid) {
        var selectedRow = this.getRowByCampoClave(xid);
        document.getElementById(this.__idControl + "_editionMode").value = 1;
        document.getElementById(this.__idControl + "_selectedRow").value = JSON.stringify(selectedRow);
    }

    /**
     * Finaliza el modo de edición de fila.
     * @param {Array} xrow Fila a reemplazar
     */
    endEditionRow(xrow) {
        document.getElementById(this.__idControl + "_editionMode").value = 0;
        document.getElementById(this.__idControl + "_selectedRow").value = "";
        this.updateDataRow(xrow);
    }

    /**
     * Obtiene el rowid actual.
     * @returns 
     */
    getRowId() {
        var result = 0;
        if (this.isEditionMode()) {
            var selected_row = this.getSelectedRow();
            result = selected_row[0][this.__campoClave];
        } else
            result = this.__rowCount + 1;

        return result;
    }    

    /**
     * Indica si se habilitó el modo de edición.
     * @returns {boolean} Devuelve true si está en modo de edición y false en caso contrario.
     */
    isEditionMode() {
        var ok;
        if (parseInt(document.getElementById(this.__idControl + "_editionMode").value) === 1)
            ok = true;
        else
            ok = false;
        return ok;
    }

    getSelectedRow() {
        return JSON.parse(document.getElementById(this.__idControl + "_selectedRow").value);
    }
}

