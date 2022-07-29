/**
 * https://www.w3schools.com/howto/howto_js_autocomplete.asp
 * Fecha: set-2019
 * Autor Marcos C.
 * www.sc3.com.ar
 * Cambiada para ser usada con SesionStorage y metadata
 */


function autocomplete(xidcontrol, xquery) {
	//carga la meta informacion y la info
	sc3LoadQueryMetadata(xquery);
	sc3LoadQueryData(xquery);

	inp = document.getElementById(xidcontrol + 'desc');

	/*the autocomplete function takes two arguments,
	the text field element and an array of possible autocompleted values:*/
	var currentFocus;

	/*execute a function when someone writes in the text field:*/
	inp.addEventListener("input", function (e) {
		//recupera datos de SessionStorage
		var arr = sc3SSGetTable(cacheQueryDataName(xquery));
		var aMeta = sc3SSGetArray(cacheQueryName(xquery));

		var a, b, i, val = this.value;
		/*close any already open lists of autocompleted values*/
		closeAllLists();
		if (!val) {
			return false;
		}
		currentFocus = -1;

		/*create a DIV element that will contain the items (values):*/
		a = document.createElement("DIV");
		a.setAttribute("id", this.id + "autocomplete-list");
		a.setAttribute("class", "autocomplete-items");
		/*append the DIV element as a child of the autocomplete container:*/
		this.parentNode.appendChild(a);
		/*for each item in the array...*/
		for (i = 0; i < arr.length; i++) {
			/*check if the item starts with the same letters as the text field value:*/
			var valorActual = arr[i][aMeta.combofield_];
			if (valorActual.toUpperCase().includes(val.toUpperCase())) {
				/*create a DIV element for each matching element:*/
				b = document.createElement("DIV");
				/*make the matching letters bold:*/
				var comienza = valorActual.toUpperCase().indexOf(val.toUpperCase())

				//ilumina el tramo que coincide
				b.innerHTML = htmlSpan("autocomplete-code", arr[i][aMeta.keyfield_]) + " " + valorActual.substr(0, comienza);
				b.innerHTML += "<strong>" + valorActual.substr(comienza, val.length) + "</strong>";
				b.innerHTML += valorActual.substr(comienza + val.length);

				/*insert a input field that will hold the current array item's value:*/
				b.innerHTML += "<input type='hidden' value='" + valorActual + "'>";
				b.innerHTML += "<input type='hidden' value='" + arr[i][aMeta.keyfield_] + "'>";
				/*execute a function when someone clicks on the item value (DIV element):*/
				b.addEventListener("click", function (e) {
					/*insert the value for the autocomplete text field:*/
					inp = document.getElementById(xidcontrol + 'desc');
					inpId = document.getElementById(xidcontrol);
					inpCode = document.getElementById(xidcontrol + 'code');

					inp.value = this.getElementsByTagName("input")[0].value;
					inpId.value = this.getElementsByTagName("input")[1].value;
					if (inpCode != null)
						inpCode.innerHTML = this.getElementsByTagName("input")[1].value;

					/*close the list of autocompleted values,
					(or any other open lists of autocompleted values:*/
					closeAllLists();

					onTranslateFn = xidcontrol + "_OnTranslate";
					eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');
				});
				a.appendChild(b);
			}
		}
	});

	/*execute a function presses a key on the keyboard:*/
	inp.addEventListener("keydown", function (e) {
		var x = document.getElementById(this.id + "autocomplete-list");
		if (x) x = x.getElementsByTagName("div");
		if (e.keyCode == 40) {
			/*If the arrow DOWN key is pressed,
			increase the currentFocus variable:*/
			currentFocus++;
			/*and and make the current item more visible:*/
			addActive(x);
		}
		else if (e.keyCode == 38) { //up
			/*If the arrow UP key is pressed,
			decrease the currentFocus variable:*/
			currentFocus--;
			/*and and make the current item more visible:*/
			addActive(x);
		}
		else if (e.keyCode == 13) {
			/*If the ENTER key is pressed, prevent the form from being submitted,*/
			e.preventDefault();
			if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) x[currentFocus].click();
			}
		}
	});

	function addActive(x) {
		/*a function to classify an item as "active":*/
		if (!x) return false;
		/*start by removing the "active" class on all items:*/
		removeActive(x);
		if (currentFocus >= x.length)
			currentFocus = 0;
		if (currentFocus < 0)
			currentFocus = (x.length - 1);
		/*add class "autocomplete-active":*/
		x[currentFocus].classList.add("autocomplete-active");
	}

	function removeActive(x) {
		/*a function to remove the "active" class from all autocomplete items:*/
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
		}
	}

	function closeAllLists(elmnt) {
		/*close all autocomplete lists in the document,
		except the one passed as an argument:*/
		var x = document.getElementsByClassName("autocomplete-items");
		for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}

	/*execute a function when someone clicks in the document:*/
	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}

//timer de búsqueda de los controles de au
var gTimeoutFnBusqueda2 = null;


/**
 * Cuando ingresa un código, lo traduce
 * @param string xidcontrol 
 * @param string xquery 
 */
function traducirCodigoSelector(xid, xquery) {
	var code = document.getElementById(xid).value;

	//el * abre la ventana
	if (code == '*') {
		openCatalogDiv(xid, xquery);
	}
	else {
		//si ingresa caracter, va al otro control
		if (code.match(/[a-z]/i) || code.match(/[A-Z]/i)) {
			document.getElementById(xid).value = '';
			document.getElementById(xid + 'desc').value = code;
			document.getElementById(xid + 'desc').focus();
			return false;
		}

		var aMeta = sc3SSGetArray(cacheQueryName(xquery));
		if (sc3ArrayVacio(aMeta)) {
			console.log('traducirCodigoSelector()', xquery, 'sin metadata cargada');
			return;
		}

		if (gTimeoutFnBusqueda2)
			clearTimeout(gTimeoutFnBusqueda2);

		//vamos a buscar, pero después de 0,5 seg
		gTimeoutFnBusqueda2 = setTimeout(function () {
			traducirCodigoSelectorDelayed(xid, xquery, aMeta, code);
		}, 500);
	}

}

/**
 * Después de un tiempo busca
 * @param {string} xid 
 * @param {string} xquery 
 */
function traducirCodigoSelectorDelayed(xid, xquery, aMeta, code) {

	var aDatos = sc3SSGetTable(cacheQueryDataName(xquery));
	var valor = '';
	
	i = 0;
	// recorre datos para determinar traduccion
	while (i < aDatos.length) {
		if (code == aDatos[i][aMeta.keyfield_]) {
			valor = aDatos[i][aMeta.combofield_];
		}

		i++;
	}

	document.getElementById(xid + 'desc').value = valor;

	//analiza si hay funcion de ... OnTranslate()
	if (valor != '') {
		onTranslateFn = xid + "_OnTranslate";
		eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');
	}
}


let DIV_PREFIX = 'divCatalog';

function openCatalogDiv(xid, xquery) {
	var aMeta = sc3SSGetArray(cacheQueryName(xquery));
	div1 = document.getElementById(DIV_PREFIX + xid);
	if (div1 == null) {
		// arma ventana modal
		/*<div id="divAgregarTurno" class="w3-modal">
		<div class="w3-modal-content w3-animate-zoom">
		<header class="w3-container w3-purple">
			<h4>Turno <span id="agregarHr"></span>
			
				<input class="w3-check" type="checkbox" id="agregarSobreturno" onclick="galCrearSobreturno();">
				<label for="agregarSobreturno"> Sobreturno</label>
			</h4>
			<span id="linkcerrar" onclick="document.getElementById('divAgregarTurno').style.display='none'" class="w3-button w3-display-topright w3-light-grey">
				<i class='fa fa-window-close-o fa-lg'></i>
			</span>
		</header>
		*/

		div1 = document.createElement('DIV');
		div1.id = DIV_PREFIX + xid;
		div1.className = "w3-modal";
		document.body.appendChild(div1);

		divInn = document.createElement('DIV');
		divInn.className = "w3-modal-content w3-animate-zoom";
		div1.appendChild(divInn);

		head = document.createElement('header');
		head.className = "w3-container w3-win8-olive";
		divInn.appendChild(head);

		h4 = document.createElement('H4');
		h4.innerHTML = "Buscar";
		h4.id = 'selectorTitulo' + xid;
		head.appendChild(h4);

		sp = document.createElement('span');
		sp.className = 'w3-button w3-display-topright w3-light-grey w3-hover-grey';
		sp.addEventListener('click', function () { document.getElementById(DIV_PREFIX + xid).style.display = 'none'; });
		sp.innerHTML = "<i class='fa fa-window-close-o fa-lg w3-text-dark-grey'></i>";
		head.appendChild(sp);

		//cuerpo, con buscador y abajo la tabla
		div2 = document.createElement('DIV');
		div2.className = 'w3-padding-small w3-white';
		divInn.appendChild(div2);

		divSearch = document.createElement('DIV');
		divSearch.className = "w3-center";
		div2.appendChild(divSearch);

		input = document.createElement("input");
		input.id = "search" + xid;
		input.type = "text";
		input.size = 25;
		input.className = "input-buscar";
		input.placeholder = "buscar...";
		input.addEventListener('keyup', function () { drawCatalogTable(xid, xquery); });
		divSearch.appendChild(input);

		spcant = document.createElement('span');
		spcant.id = "selectorCantidad" + xid;
		spcant.className = "w3-tiny w3-text-blue";
		spcant.innerHTML = " # ";
		divSearch.appendChild(spcant);

		divTabla = document.createElement('DIV');
		divTabla.className = 'w3-responsive w3-white div-tabla-selector';
		div2.appendChild(divTabla);

		tbl = document.createElement('TABLE');
		tbl.id = 'tbl' + xid;
		tbl.className = "w3-table-all w3-hoverable";
		divTabla.appendChild(tbl);

		spcanLeyenda = document.createElement('span');
		spcanLeyenda.id = "leyendaAbajo" + xid;
		spcanLeyenda.className = "w3-small w3-text-blue";
		spcanLeyenda.innerHTML = "";
		div2.appendChild(spcanLeyenda);

		//Posibilidad de agregar uno nuevo 
		if (aMeta.caninsert) {
			divInf = document.createElement('DIV');
			divInf.id = 'divAgregar' + xquery;
			divInf.className = 'w3-padding-small w3-white oculto';
			divInn.appendChild(divInf);

			spAgregar = document.createElement('span');
			spAgregar.className = 'w3-button w3-light-blue w3-hover-grey';
			spAgregar.addEventListener('click', function () {
				openWindow('sc-edititem.php?insert=1&modocatalog=2&query=' + xquery);
				document.getElementById(DIV_PREFIX + xid).style.display = 'none';
			});
			spAgregar.innerHTML = "<i class='fa fa-plus fa-lg w3-text-dark-grey'></i> Agregar";
			divInf.appendChild(spAgregar);
		}

		/*<div class="w3-padding-small">
			<div class="w3-center">
				<input name="search" id="search" type="text" size="25" maxlength="60" value="" class="" placeholder="buscar..." onkeyup="drawTable('selectorTabla', query);">
				<span id="selectorCantidad" class="w3-tiny w3-text-blue"> # </span>
			</div>
		
			<div id="" class="w3-responsive w3-white div-tabla-selector">
				<table id="selectorTabla" class="w3-table-all w3-hoverable">
				
				</table>
			</div>
			
			<br>
		</div> */
	}

	div1.style.display = 'block';
	document.getElementById("search" + xid).focus();
	drawCatalogTable(xid, xquery);

	//Luego de dibujar la tabla y abrir ventana modal, buscar si hay datos mas nuevos
	//para cuando el usuario comience a buscar
	sc3LoadQueryData(xquery);
}

/**
 * Muestra el resultado de la busqueda, o todos
 * @param xtable 
 * @param xquery 
 */
function drawCatalogTable(xid, xquery) {
	let MAX = 25;

	var palabra = document.getElementById("search" + xid).value;
	var tbl = document.getElementById('tbl' + xid);
	if (tbl == null) {
		console.log('drawCatalogTable()', 'tbl' + xid, 'no existe');
		return;
	}

	//la limpia
	tbl.innerHTML = '';

	var aMeta = sc3SSGetArray(cacheQueryName(xquery));
	if (sc3ArrayVacio(aMeta)) {
		console.log('drawCatalogTable()', xquery, 'sin metadata cargada');
		return;
	}

	titulo = aMeta.querydescription;
	document.getElementById('selectorTitulo' + xid).innerHTML = titulo;

	// 1 - arma encabezado -------------------------------------------------------------
	var thead = document.createElement('thead');
	var aTitulos = aMeta.titulos;
	i = 0;
	while (i < aTitulos.length) {
		var th = document.createElement('th');
		th.className = 'grid_header';
		th.innerHTML = aTitulos[i];
		thead.appendChild(th);
		i++;
	}
	tbl.appendChild(thead);


	// 2 - arma datos ----------------------------------------------------------------
	var aDatos = sc3SSGetTable(cacheQueryDataName(xquery));

	var aCampos = aMeta.titulos_fields;
	var tbody = document.createElement('tbody');

	cantDatos = 0;
	i = 0;
	while (i < aDatos.length) {
		var tr = document.createElement('tr');
		tr.className = 'w3-hoverable tr_clickeable';
		var pasaFiltro = false;
		aCampos.forEach((campo) => {
			var td = document.createElement('td');

			//definicion del campo
			var aFieldDef = aMeta.fieldsdef[campo];

			var valor = aDatos[i][campo];
			if (campo != null && valor != null) {
				if (campo == aMeta.keyfield_)
					valorId = valor;
				if (campo == aMeta.combofield_)
					varlorDesc = valor;

				valor = checkUndefined(valor);

				//con cualquier campo que se incluya, pasa
				if (valor.toUpperCase().includes(palabra.toUpperCase()))
					pasaFiltro = true;
			}
			else
				valor = '';

			//booleanos SI/NO
			if (aFieldDef !== undefined) {
				if (aFieldDef.type == 1) {
					if (valor == 1)
						valor = "<span style='color:green'>Si</span>";
					else
						valor = "<span style='color:red'>No</span>";
				}
			}

			td.innerHTML = valor;

			//si el campo termina en _fk, le quita para ver su definicion
			if (campo.substring(campo.length - 3, campo.length) == '_fk')
				campo = campo.substring(0, campo.length - 3);

			td.className = aMeta.fieldsdef[campo].orientacion;
			tr.appendChild(td);
		});

		tr.innerHTML += "<input type='hidden' value='" + valorId + "'>";
		tr.innerHTML += "<input type='hidden' value='" + varlorDesc + "'>";

		tr.addEventListener('click', function (e) {
			var target = event.target || event.srcElement || event.originalTarget;
			id = target.parentElement.getElementsByTagName("input")[0].value;
			desc = target.parentElement.getElementsByTagName("input")[1].value;
			document.getElementById(xid).value = id;
			document.getElementById(xid + 'desc').value = desc;
			objCode = document.getElementById(xid + 'code');
			if (objCode != null)
				objCode.innerHTML = id;

			//invoca funcion de traduccion, si existe	
			onTranslateFn = xid + "_OnTranslate";
			eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');

			document.getElementById(DIV_PREFIX + xid).style.display = 'none';
		});

		if (pasaFiltro) {
			if (cantDatos < MAX)
				tbody.appendChild(tr);
			cantDatos++;
		}
		i++;
	}
	tbl.appendChild(tbody);

	leyendaAbajo = "";
	txtCant = '#' + cantDatos + '';
	if (cantDatos > MAX) {
		txtCant += ' (primeros ' + MAX + ')';
		leyendaAbajo = "Hay mas datos, refine su búsqueda. " + txtCant;
	}

	document.getElementById("selectorCantidad" + xid).innerHTML = txtCant;
	document.getElementById("leyendaAbajo" + xid).innerHTML = leyendaAbajo;

	divAgregar = document.getElementById('divAgregar' + xquery);
	if (divAgregar != null) {
		if (cantDatos == 0) {
			divAgregar.classList.remove("oculto");
		}
		else {
			divAgregar.classList.add("oculto");
		}
	}
}
