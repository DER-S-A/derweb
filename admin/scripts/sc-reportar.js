/**
 * Scripts de sc-reportar.php 
 * Armado de filtros
 *
*/

var CANT_BUSQUEDAS = 0;

/**
 * Abre sección de filtos especiales
 */
function busquedaAvanzada()
{
	divBuscador = document.getElementById("barra-buscador");

	//div con todo el filtro
	divFiltro = document.createElement("div");
	divFiltro.className = "barra-buscador-filtro";
	divFiltro.id = "divFiltro_" + CANT_BUSQUEDAS;

	// botón quitar -------------------------------------------
	hrefQuitar = document.createElement("a");
	hrefQuitar.className = "w3-margin-small btn-flat";
	hrefQuitar.tittle = "Eliminar filtro";
	hrefQuitar.href="javascript:quitarFiltro(" + CANT_BUSQUEDAS + ")";

	imgQuitar = document.createElement("i");
	imgQuitar.className = "fa fa-minus-circle fa-lg";
	hrefQuitar.appendChild(imgQuitar);
	
	divFiltro.appendChild(hrefQuitar);


	// campo de búsqueda ---------------------------------------
	var sel = document.createElement("select");
	sel.id = "busqcampo_" + CANT_BUSQUEDAS;
	sel.name = "busqcampo_" + CANT_BUSQUEDAS;
	CANT_BUSQUEDAS++;
	sel.className = "w3-margin-small";

	// copiamos el buscador de campos
	selCampos = document.getElementById("listacampos");
	sel.innerHTML = selCampos.innerHTML;
	sel.addEventListener('change', cargarFiltro);

	divFiltro.appendChild(sel);

	divBuscador.appendChild(divFiltro);
	divBuscador.classList.remove("oculto");
}


function quitarFiltro(indice)
{
	divFiltro = document.getElementById("divFiltro_" + indice);
	divFiltro.innerHTML = '';
	divFiltro.parentNode.removeChild(divFiltro);
}


/**
 * Cuando elijo campo se cargan sus condiciones
 */
function cargarFiltro()
{
	nombre = this.id;
	aNombre = nombre.split("_");
	indice = aNombre[1];
	campo = this.value;

	console.log('cargarFiltro()', queryname, campo, indice);

	var params = [{"query":queryname,
					"campo":campo,
					"indice":indice}];

	sc3InvokeServerFn('sc3FiltroAsociado', params, cargarFiltroCB);
}

/**
 * Llegaron los filtros para este campo
 * @param {array} xResult 
 */
function cargarFiltroCB(xResult)
{
	console.log("cargarFiltroCB()");

	condicionHtml = xResult["condicion"];
	controlHtml  = xResult["control"];
	indice = xResult["indice"];
	jscript = xResult["jscript"];

	divFiltro = document.getElementById( "divFiltro_" + indice);

	//busca si ya existe el control de la condicion
	spanCondicion = document.getElementById("span_condicion_" + indice);
	if (spanCondicion == null)
	{
		spanCondicion = document.createElement("span");
		spanCondicion.id = "span_condicion_" + indice;
		divFiltro.appendChild(spanCondicion);
	}
	else
		spanCondicion.innerHTML = '';

	spanCondicion.innerHTML = condicionHtml;

	//busca si ya existe el control del control
	spanControl = document.getElementById("span_control_" + indice);
	if (spanControl == null)
	{

		spanControl = document.createElement("span");
		spanControl.id = "span_control_" + indice;
		divFiltro.appendChild(spanControl);
	}
	else
		spanControl.innerHTML = '';

	spanControl.innerHTML = controlHtml;

	if (jscript != '')
		eval(jscript);
}

