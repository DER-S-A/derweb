
//Funciones sc3 (www.sc3.com.ar)
//Autor: Marcos C.
//SC3

var opId = '';

function PopWindow(strDemoFile, strWindowName, strWidth, strHeight, strScroll, strPos) {
	if (strWidth == '') {
		strWidth = '636';
	}
	if (strHeight == '') {
		strHeight = '555';
	}
	if (strScroll == '') {
		strScroll = 'yes';
	}
	if (strPos == '') {
		strPos = 'default'
	}
	if (strPos == 'random') {
		LeftPosition = (screen.availWidth) ? Math.floor(Math.random() * (screen.availWidth - strWidth)) : 50;
		TopPosition = (screen.availHeight) ? Math.floor(Math.random() * ((screen.availHeight - strHeight) - 75)) : 50;
	}
	if (strPos == 'center') {
		LeftPosition = (screen.availWidth) ? (screen.availWidth - strWidth) / 2 : 50;
		TopPosition = (screen.availHeight) ? (screen.availHeight - strHeight) / 2 : 50;
	}
	if (strPos == 'right') {
		LeftPosition = screen.availWidth - strWidth;
		TopPosition = 100;
	}
	if (strPos == 'right-top') {
		LeftPosition = 500;
		TopPosition = 0;
	}
	if (strPos == 'default') {
		LeftPosition = 50;
		TopPosition = 50;
	}
	if ((strPos != 'center' && strPos != 'random' && strPos != 'default' && strPos != 'right-top' && strPos != 'right') || strPos == null) {
		LeftPosition = 100;
		TopPosition = 100;
	}
	var strAttribs = 'width=' + strWidth + ',height=' + strHeight + ',top=' + TopPosition + ',left=' + LeftPosition + ',scrollbars=' + strScroll + ',location=no,directories=no,status=yes,menubar=no,toolbar=no,resizable=yes,dependent=yes';

	if (strWindowName == 'submit') {
		strWindowName = 'PopWindow';
	}
	myPOPwindow = window.open(strDemoFile, strWindowName, strAttribs);

	if (myPOPwindow.focus) {
		myPOPwindow.focus();
	}
	return myPOPwindow;
}

function setControlValue(xid, xvalue) {
	document.getElementById(xid).value = xvalue;
}

function clearControl(xid) {
	setControlValue(xid, '');
}

function trim(xStr) {
	return xStr.trim();
}


/**
 * Limpia selector e invoca _onTranslate()
 * @param {} xControl1 
 * @param {} xControl2 
 */
function clearSelector(xControl1, xControl2) {
	clearControl(xControl1);
	if (isUndefined(xControl2))
		xControl2 = xControl1 + 'desc';

	clearControl(xControl2);

	objCode = document.getElementById(xControl1 + 'code');
	if (objCode != null)
		objCode.innerHTML = '';

	document.getElementById(xControl2).focus();

	onTranslateFn = xControl1 + "_OnTranslate";
	eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');
}

/**
 * Limpia el selector (ambos controles)
 * No invoca evento _OnTranslate
 * @param {string} xControl1 
 */
function clearSelector2(xControl1) {
	clearControl(xControl1);
	clearControl(xControl1 + 'desc');
}


/**
 * Cambia la visibilidad de un objeto
 * @param {string} xId 
 * @param {int} xVisible 
 */
function changeVisibility(xId, xVisible) {
	var div1 = document.getElementById(xId);
	if (div1 != null) {
		if (xVisible == 1) {
			div1.style.visibility = 'visible';
			div1.style.display = '';
		}
		else {
			div1.style.visibility = 'hidden';
			div1.style.display = 'none';
		}
	}
}


/**
 * Invierte la visibilidad de un objeto, de hidden a visible y viceversa
 * @param {string} xId 
 */
function switchVisibility(xId) {
	var div1 = document.getElementById(xId);
	if (div1 != null) {
		if (div1.style.visibility == 'hidden') {
			div1.style.visibility = 'visible';
			div1.style.display = '';
		}
		else {
			div1.style.visibility = 'hidden';
			div1.style.display = 'none';
		}
	}
}

function pleaseWait() {
	changeVisibility("div1", 0);
	changeVisibility("div2", 1);
	changeVisibility("div3", 0);
}

//si está indefinido da vacio
function checkUndefined(xvar) {
	if (typeof (xvar) == "undefined")
		return '';
	return xvar;
}

//si est� indefinido 
function isUndefined(xvar) {
	if (typeof (xvar) == "undefined")
		return true;
	return false;
}


/**
 * Ubica un combo con el texto dado
 * @param xComboId
 * @param xTexto
 * @returns
 */
function ubicarCombo(xComboId, xTexto) {
	var dd = document.getElementById(xComboId);
	for (var i = 0; i < dd.options.length; i++) {
		if (dd.options[i].text === xTexto) {
			dd.selectedIndex = i;
			break;
		}
	}
}


/**
 * Muestra el engranage girando, indicando que está esperando al servidor
 */
function pleaseWait2() {
	div1 = document.getElementById("divejecutando");
	if (div1 != null)
		changeVisibility("divejecutando", 1);
	else {
		/*<div class="boxejecutando" id="divejecutando" style="visibility: hidden;">
			<div class="w3-center">
				<i class="fa fa-cog fa-spin fa-3x fa-fw w3-text-blue"></i>
				procesando...
			</div>
		</div>
		*/

		//crea el DIV ya visible
		div1 = domCreateDIV("divejecutando", "boxejecutando", "");

		div2 = domCreateDIV("", "w3-center", "");
		i = document.createElement('i');
		i.className = "fa fa-cog fa-spin fa-4x fa-fw w3-text-blue";
		div2.appendChild(i);
		div1.appendChild(div2);
		document.body.appendChild(div1);
	}
}

/**
 * Apaga divejecutando
 */
function pleaseWaitStop() {
	div1 = document.getElementById("divejecutando");
	if (div1 != null)
		changeVisibility("divejecutando", 0);
}


/**
 * Muestra el engranage girando, indicando que está esperando a la base de datos
 * @param {integer} xMostrar para indicar si muestra o no
 */
function pleaseWaitDB(xMostrar) {

	if (xMostrar == 1) {

		div1 = document.getElementById("divWaitDB");
		if (div1 != null)
			changeVisibility("divWaitDB", 1);
		else {
			//crea el DIV ya visible
			div1 = domCreateDIV("divWaitDB", "boxejecutando", "");

			div2 = domCreateDIV("", "w3-center", "");
			i = document.createElement('i');
			i.className = "fa fa-refresh fa-spin fa-5x fa-fw w3-text-green";
			div2.appendChild(i);
			div1.appendChild(div2);
			document.body.appendChild(div1);
		}
	}
	else {
		// apagar todo
		div1 = document.getElementById("divWaitDB");
		if (div1 != null)
			changeVisibility("divWaitDB", 0);
	}
}


function openCatalog(xControl1, xControl2, xQuery, xMasterSel, xMasterField, xMasterDesc) {
	masterValue = "";
	if (xMasterSel != "") {
		masterValue = document.getElementById(xMasterSel).value;
		if (masterValue == "") {
			alert("Elija antes un valor en " + xMasterDesc);
			document.getElementById(xMasterSel).focus;
			return;
		}
	}

	valorBusqueda = document.getElementById(xControl2).value;
	valorKey = document.getElementById(xControl1).value;
	var ancho = (screen.width / 2);
	var alto = screen.height - 100;

	PopWindow('sc-opencatalog.php?query=' + xQuery + '&control1=' + xControl1 + '&control2=' + xControl2 + "&mfield=" + xMasterField + "&mid=" + masterValue + '&search=' + valorBusqueda, 'catalogo', ancho, alto, '', 'right');
}

function openCatalogGrid(xControl1, xControl2, xQuery, xSql) {
	valorBusqueda = document.getElementById(xControl2).value;
	valorKey = document.getElementById(xControl1).value;
	var ancho = (screen.width / 2) + 100;
	var alto = screen.height - 100;

	PopWindow('sc-openCatalogGrid.php?query=' + xQuery + '&control1=' + xControl1 + '&control2=' + xControl2 + "&sql=" + xSql + '&search=' + valorBusqueda, 'catalogo', ancho, alto, '', 'right');
}

function openCatalogExtra(xUrl, xControlV, xControlK) {
	valor = document.getElementById(xControlV).value;
	key = document.getElementById(xControlK).value;
	var ancho = (screen.width / 2) + 10;
	var alto = screen.height - 100;

	PopWindow(xUrl + '?controlValue=' + xControlV + '&controlKey=' + xControlK + '&key=' + key + '&valor=' + valor, 'catalogo', ancho, alto, '', 'right');
}


function fijarSelector(xControl1, xControl2, xQuery) {
	valor2 = document.getElementById(xControl2).value;
	valor1 = document.getElementById(xControl1).value;
	PopWindow('sc-fijarselector.php?query=' + xQuery + '&valor1=' + valor1 + '&valor2=' + valor2, 'catalogo', '350', '350', '', 'right');
}

function selRec(xControl1, xControl2, xValue1, xValue2) {
	top.opener.document.getElementById(xControl1).value = xValue1;
	if (top.opener.document.getElementById(xControl2) != null) {
		top.opener.document.getElementById(xControl2).value = xValue2;

		//luego de elegir la opcion, intenta invocar funcion ....__OnTranslate()
		onTranslateFn = xControl1 + "_OnTranslate";
		top.opener.eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');
	}
	top.close();
}


function fechaToUrl(xControl) {
	anio = document.getElementById(xControl + '_a').value;
	if (anio == '')
		return '';

	fecha = anio + "-" +
		document.getElementById(xControl + '_m').value + "-" +
		document.getElementById(xControl + '_d').value;

	return fecha;
}

//retorna la fecha de hoy en dormato dd/mm/aaaa
function getFechaHoy() {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth() + 1; //January is 0!
	var yyyy = today.getFullYear();

	if (dd < 10) {
		dd = '0' + dd
	}

	if (mm < 10) {
		mm = '0' + mm
	}

	today = dd + '/' + mm + '/' + yyyy;
	return today;
}


function getHora() {
	var today = new Date();
	var hours = today.getHours();
	var minutes = today.getMinutes();
	if (minutes < 10)
		minutes = '0' + minutes;
	ahora = hours + ':' + minutes;
	return ahora;
}


function clearFecha(xControl) {
	document.getElementById(xControl + '_a').value = "";
	document.getElementById(xControl + '_m').value = "";
	document.getElementById(xControl + '_d').value = "";
}

//fecha en formato YYYY-MM-DD
function formatFechaSql(date) {
	var d = new Date(date);
	month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

	if (month.length < 2) month = '0' + month;
	if (day.length < 2) day = '0' + day;

	return [year, month, day].join('-');
}


function ultimoDiaMes(y, m) {
	return new Date(y, m + 1, 0).getDate();
}

function sc3DisplayDiv(xid, xdisplay) {
	if (xdisplay == '')
		xdisplay = 'block';
	document.getElementById(xid).style.display = xdisplay;
}


//muestra un error en un div acorde, retorna si lo pudo mostrar
function sc3DisplayError(xdiv, xmsg) {
	div = document.getElementById(xdiv);
	if (div != null) {
		changeVisibility(xdiv, true);
		div.className = "error";
		div.innerHTML = '<i class="fa fa-bomb fa-2x" ></i> ' + xmsg;

		setTimeout(function () {
			changeVisibility(xdiv, false);
		},
			6500);
		return true;
	}
	return false;
}

//muestra un warning en un div acorde, retorna si lo pudo mostrar
function sc3DisplayWarning(xdiv, xmsg) {
	div = document.getElementById(xdiv);
	if (div != null) {
		changeVisibility(xdiv, true);
		div.className = "warning";
		div.innerHTML = '<i class="fa fa-info fa-fw fa-2x" ></i> ' + xmsg;

		setTimeout(function () {
			changeVisibility(xdiv, false);
		},
			20500);
		return true;
	}
	return false;
}

//muestra un error en un div acorde
function sc3DisplayMsg(xdiv, xmsg) {
	div = document.getElementById(xdiv);
	if (div != null) {
		changeVisibility(xdiv, true);
		div.className = "ok";
		div.innerHTML = '<i class="fa fa-check-square fa-fw fa-2x"></i> ' + xmsg;

		setTimeout(function () {
			changeVisibility(xdiv, false);
		},
			5500);
		return true;
	}
	return false;
}


/**
 * Crea cartel emergente y lo abre por un tiempo
 * @param {string} xmsg 
 */
function sc3DisplayMsgEmergente(xmsg) {
	cartelId = "sc3-mensaje";
	var cartel = document.getElementById(cartelId);
	if (cartel) {
		cartel.removeChild(cartel.lastChild);
	}
	else {
		var cartel = document.createElement("div");
		cartel.id = cartelId;
		cartel.className = "sc3-cartel";
		document.body.appendChild(cartel);
	}

	var p = document.createElement("p");
	p.innerHTML = '<i class="fa fa-check-square fa-fw fa-2x"></i> ' + xmsg;
	cartel.appendChild(p);
	changeVisibility(cartelId, true);

	setTimeout(function () {
		changeVisibility(cartelId, false);
	},
		2500);
}


function clearMsg() {
	msgdiv = document.getElementById('divmsg');
	if (msgdiv != null)
		msgdiv.innerHTML = "";
}

//reemplaza todas las ocurrencias, 4to param TRUE para case i
function replaceAll(str, str1, str2, ignore) {
	return str.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"), (ignore ? "gi" : "g")), (typeof (str2) == "string") ? str2.replace(/\$/g, "$$$$") : str2);
}


function decodeHTMLEntities(text) {
	var entities = [
		['amp', '&'],
		['apos', '\''],
		['#x27', '\''],
		['#x2F', '/'],
		['#39', '\''],
		['#47', '/'],
		['lt', '<'],
		['gt', '>'],
		['nbsp', ' '],
		['middot', ' '],
		['quot', '"'],
		['aacute', 'á'],
		['eacute', 'é'],
		['iacute', 'í'],
		['oacute', 'ó'],
		['uacute', 'ú'],
		['ntilde', 'ñ']
	];

	for (var i = 0, max = entities.length; i < max; ++i)
		text = text.replace(new RegExp('&' + entities[i][0] + ';', 'g'), entities[i][1]);

	return text;
}



function showWarn(xmsg) {
	sc3DisplayWarning('divmsg', xmsg);
}

function showWarn2(xmsg) {
	sc3DisplayWarning('divmsg', xmsg);
}


/**
 * Tilda o destilda todos en una grilla
 * Limita a 600
 * @param {string} xidchecks 
 * @param {string} xidbutton 
 * @param {function} xfn 
 */
function tildarTodos(xidchecks, xidbutton, xfn) {

	let MAX = 500;
	console.clear();
	console.log("tildarTodos(): " + xidchecks + ", " + xidbutton + ", " + xfn);

	valorTodos = '<i class="fa fa-check-square"></i> todos';
	valorNinguno = '<i class="fa fa-check-square-o"></i> ninguno';

	var checkboxes = document.getElementsByName(xidchecks);
	var button = document.getElementById(xidbutton);

	if (button.innerHTML.trim() == valorTodos) {
		//tilda !
		for (var i in checkboxes) {
			if (i < MAX)
				checkboxes[i].checked = 'FALSE';
		}
		button.innerHTML = valorNinguno;
	}
	else {
		//destilda
		for (var i in checkboxes) {
			checkboxes[i].checked = '';
		}
		button.innerHTML = valorTodos;
	}

	if (xfn != '')
		eval(xfn);
}



function validarint(xvalue, xcontrol) {
	if (xvalue != "") {
		if (isNaN(parseInt(xvalue))) {
			alert(xvalue + " no es un entero valido.")
			xcontrol.value = 0;
		}
		else
			xcontrol.value = parseInt(xvalue);
	}
}

function validarfloat(xcontrol) {
	control = document.getElementById(xcontrol);
	if (control == null)
		return;

	valor = control.value
	if (valor != "") {
		//analiza si tiene expresion matem�tica
		if ((valor.indexOf("+") > -1) || (valor.indexOf("-") > -1) || (valor.indexOf("*") > -1) || (valor.indexOf("/") > -1)) {
			valor = eval(valor);
		}
		if (isNaN(parseFloat(valor))) {
			alert(valor + " no es un numero valido.");
			control.value = "0.00";
		}
		else
			control.value = parseFloat(valor).toFixed(2);
	}
}

function validarfloatDec(xcontrol, xdecimales) {
	control = document.getElementById(xcontrol);
	if (control == null)
		return;

	valor = control.value
	if (valor != "") {
		//analiza si tiene expresi�n matem�tica
		if ((valor.indexOf("+") > -1) || (valor.indexOf("-") > -1) || (valor.indexOf("*") > -1) || (valor.indexOf("/") > -1)) {
			valor = eval(valor);
		}
		if (isNaN(parseFloat(valor))) {
			alert(valor + " no es un numero valido.");
			control.value = "0.00";
		}
		else
			control.value = parseFloat(valor).toFixed(xdecimales);
	}
}


/*
echo roundTo(87.23, 20); //80 
echo roundTo(-87.23, 20); //-80 
echo roundTo(87.23, .25); //87.25 
echo roundTo(.23, .25); //.25 
 */
function roundTo(number, redondeo) {
	if (redondeo == 0)
		return number;
	dif = redondeo / 3;
	return Math.round((number + dif) / redondeo, 0) * redondeo;
}

/**
 * Del valor tomado del innerHtml retorna el valor float
 * @param {string} xvalor 
 */
function valorFloatDeCelda(xvalor) {
	valor = xvalor;
	signo = 1.00;
	if (xvalor.indexOf("-") != -1) {
		signo = -1.00;
		valor = xvalor.replace("-", "");
	}

	valor = valor.replace(",", "");
	valor = valor.replace(",", "");
	valor = valor.replace(",", "");
	valor = valor.replace("$ ", "");
	valor = valor.replace("U$S ", "");
	valor = valor.replace("<font color=\"red\">", "");
	valor = valor.replace("</font>", "");

	if (isNaN(valor))
		return 0;

	valor = valor * 1.00;
	valor = signo * valor;
	return valor;
}


/**
 * Retorna una marca de timestamp, en minutos
 * @returns
 */
function sc3Timestamp() {
	var d = new Date();
	var millis = d.getTime();
	//en minutos desde 1970
	return Math.floor(millis / 60000);
}


//borra el contenido de un campo
function borrar_campo(xEdit) {
	foto = document.getElementById(xEdit);
	if (confirm("Esta seguro de borrar esta foto ?")) {
		foto.value = "";
		divPreview = document.getElementById('previewImagen' + xEdit);
		if (divPreview != null)
			divPreview.innerHTML = "Arrastre agu&iacute;";
	}
}

/**
 * Abre url en nuevo tab
 * @param {string} xurl 
 */
function openInNewTab(xurl) {
	Object.assign(document.createElement('a'), {
		target: '_blank',
		href: xurl,
	}).click();
}

/**
 * Abre una ventana emergente sobre la derecha que ocupa la mitad del ancho de la pantalla.
 * @param {string} theURL 
 * @param {string} winName 
 */
function openWindow(theURL, winName) {
	var viewportwidth = screen.width;
	var viewportheight = screen.height;

	//limita el ancho a 650
	if (viewportwidth > 1300)
		viewportwidth = 1300;
	//limita la altura a 1000 
	if (viewportheight > 1000)
		viewportheight = 1000;

	var ancho = (viewportwidth / 2);
	//evita las barras de arriba y abajo
	var alto = viewportheight - 180;

	//alineada a derecha
	var leftX = screen.width - ancho;

	var desktop = window.open(theURL, winName, 'width=' + ancho + ',height=' + alto + ',left=' + leftX + ',top=80');
	desktop.creator = this;
}


function openWindowGmaps2(theURL, winName) {
	openWindow(theURL, winName);
}


function canFocus(xType) {
	if (xType == 'hidden')
		return false;
	if (xType == 'text' || xType == 'textarea' || xType == 'date' || xType == 'checkbox' || xType == 'select')
		return true;
	return false;
}

//sets the focus to the next control of the form if the ENTER key was pressed
function nextFocus(xform, xfield, xevent) {
	var next = 0;
	var found = false;
	var f = xform;
	/*
	if(xevent.keyCode != 13)
		return;
	*/
	for (var i = 0; i < f.length; i++) {
		if (xfield.name == f[i].name) {
			next = i + 1;
			found = true;
			break;
		}
	}
	while (found) {
		c = f[next];
		if (c.disabled == false && canFocus(c.type) && (c.type == 'text' || c.type == 'textarea')) {
			c.focus();
			break;
		}
		else {
			if (next < f.length - 1)
				next = next + 1;
			else
				break;
		}
	}
}

//sets the focus to the first control of the form
function firstFocus() {
	var f = document.getElementById('form1');
	if (f == null)
		return;

	for (var i = 0; i < f.length; i++) {
		if (f[i].disabled == false && canFocus(f[i].type)) {
			f[i].focus();
			break;
		}
	}
}


function redondear(suma) {
	suma = Math.round(suma * 100) / 100;
	suma = parseFloat(suma).toFixed(2);
	return suma;
}

/**
 * Redondea a una cantidad de decimales
 * @param suma
 * @param decimales
 * @returns
 */
function redondear2(suma, decimales) {
	suma = Math.round(suma * 1000) / 1000;
	suma = parseFloat(suma).toFixed(decimales);
	return suma;
}


/**
 * Quita todos los símbolos y retorna el valor numerico
 * @param {string} xvalor 
 * @returns 
 */
function numeroSinFormato(xvalor) {
	valor = xvalor.replaceAll(",", "");
	valor = valor.replace("$ ", "");
	valor = valor.replace("U$S ", "");

	valor = valor.replace('<font color="red">', "");
	valor = valor.replace('</font>', "");
	valor2 = valor;
	valor2 = valor2.replace("-", "");
	valor = valor * 1.00;
	if (sumEsNumero(valor2)) {
		return valor;
	}

	return 0;
}

function openMenu(xanchor, xdiv) {
	var win = new PopupWindow(xdiv);
	win.autoHide();
	win.offsetX = -150;
	win.offsetY = -10;
	win.showPopup(xanchor);
}

function openMenu2(xanchor, xdiv) {
	changeVisibility(xdiv, true);
	var testpopup1 = new PopupWindow(xdiv);
	testpopup1.offsetY = -120;
	testpopup1.offsetX = -180;
	testpopup1.autoHide();
	testpopup1.showPopup(xanchor);
}


function verUrl(xurl) {
	document.location.href = xurl;
}

//si la tecla presionada coincide
function goOnKey(xkey, xkey2, xlink) {
	if (xkey == xkey2) {
		url = document.getElementById(xlink);
		if (url != null) {
			eval(url.href);
			return false;
		}
	}
	return true;
}

function diaSemana(d) {
	var weekday = new Array(7);
	weekday[0] = "Domingo";
	weekday[1] = "Lunes";
	weekday[2] = "Martes";
	weekday[3] = "Miercoles";
	weekday[4] = "Jueves";
	weekday[5] = "Viernes";
	weekday[6] = "Sabado";

	return weekday[d.getDay()];
}

/**
 * Busca un link/boton por clase y lo evalua
 * Para que un botón reaccione a F9 (ej) hay que ponerle la clase "f9"
 * @param {string} xClass 
 */
function evalFnKey(xClass) {

	aBtns = document.getElementsByClassName(xClass);

	if (aBtns != null) {

		for (var i in aBtns) {

			var elem = aBtns[i];

			//tiene HREF o onclick

			if (elem.href) {
				eval(elem.href);
				return false;
			}
			if (elem.onclick) {
				elem.onclick.apply(elem);
				return false;
			}
		}
	}
}


//controla que se presione una tecla de funcion (ESC, F2, F3, F4, Delete, flechas de navegacion)
document.onkeyup = function (e) {

	let Esc = (window.event) ? 27 : e.DOM_VK_ESCAPE;
	let ENTER = 13;
	let INSERT = 45;

	let F2 = 113;
	let F4 = 115;
	let F7 = 118;
	let F8 = 119;
	let F9 = 120;
	let F10 = 121;

	if (!e)
		e = window.event;
	var kC = e.keyCode;
	if (e.charCode && kC == 0)
		kC = e.charCode;

	isControlKeyOn = 0;
	if (e.ctrlKey == 1)
		isControlKeyOn = 1;

	if (kC == Esc) {
		url = document.getElementById('linkcerrar');
		if (url != null) {
			href = url.href;
			if (href !== undefined && href != '')
				verUrl(url.href);
			else {
				//cuando el linkcerrar no es un botón pero tiene evento onclick
				href = url.onclick;
				if (href != '') {
					url.onclick.apply(url);
				}
			}
			return false;
		}

		url = document.getElementById('linkcerrar2');
		if (url != null) {
			href = url.href;
			if (href !== undefined && href != '')
				verUrl(url.href);
			else {
				//cuando el linkcerrar no es un bot�n pero tiene evento onclick
				href = url.onclick;
				if (href != '') {
					url.onclick.apply(url);
				}
			}
			return false;
		}

		url = document.getElementById('linkcerrar3');
		if (url != null) {
			href = url.href;
			if (href !== undefined && href != '')
				verUrl(url.href);
			else {
				//cuando el linkcerrar no es un bot�n pero tiene evento onclick
				href = url.onclick;
				if (href != '') {
					url.onclick.apply(url);
				}
			}
			return false;
		}

		url = document.getElementById('linkcerrarW');
		if (url != null) {
			window.close();
			return false;
		}

		//llegamos hasta acá, ningún ESC fue encontrado, probamos el padre
		var padre = window.parent.document;
		if (padre != null) {
			url = padre.getElementById('linkcerrar');
			if (url != null) {
				href = url.href;
				if (href !== undefined && href != '') {
					padre.location.href = url.href;
				}
			}
		}
	}

	// -- F2: editar ----------------------------
	if (kC == F2) {
		url = document.getElementById('linkeditar');
		if (url != null) {
			verUrl(url.href);
			return false;
		}
	}

	//INSERT: Nuevo
	if (kC == INSERT) {
		url = document.getElementById('linknuevo');
		if (url != null) {
			verUrl(url.href);
			return false;
		}
	}

	// -- F4: Notas & buscador de operaciones -------------------------------------
	if (kC == F4) {
		url = document.getElementById('linknotas');
		if (url != null) {
			verUrl(url.href);
			return false;
		} else {
			//busca el buscador de operaciones
			buscador = document.getElementById('buscarop');
			if (buscador != null) {
				buscador.focus();
				return false;
			} else {
				iframeMenu = window.parent.document.getElementById('indice');
				if (iframeMenu != null) {
					buscador = iframeMenu.contentDocument.getElementById('buscarop');
					if (buscador != null) {
						buscador.focus();
						return false;
					}
				}
			}

		}
	}

	// -- F7: ACEPTAR pero sin imprimir/facturar -------------------------------
	if (kC == F7) {
		chkPrint = document.getElementById('autoprint');
		if (chkPrint != null) {
			chkPrint.value = "0";

			url = document.getElementById('bsubmit');
			if (url != null) {
				submitForm();
				return false;
			}
		} else {
			evalFnKey("f7");
		}
	}

	// -- F8: ACEPTAR --------------------------------------------------------
	if (kC == F8) {
		url = document.getElementById('bsubmit');
		if (url != null) {
			submitForm();
			return false;
		} else {
			evalFnKey("f8");
		}
	}

	// -- F9: ACEPTAR y Seguir -----------------------------------------------
	if (kC == F9) {
		url = document.getElementById('bsubmit2');
		if (url != null) {
			submitForm2();
			return false;
		} else {
			evalFnKey("f9");
		}
	}

	// -- F10: CANCELAR/INICIALIZAR ------------------------------------------
	if (kC == F10) {
		url = document.getElementById('linkcancelar');
		if (url != null) {
			verUrl(url.href);
			return false;
		}
		else {
			//intenta buscarlo en el iframe	
			if2 = document.getElementById('detalleremito');
			if (if2 != null) {
				var innerDoc = (if2.contentWindow || if2.contentDocument);

				url = innerDoc.document.getElementById('linkcancelar');
				if (url != null) {
					innerDoc.location = url;

					vaciarVenta();
					return false;
				}
			} else {
				evalFnKey("f10");
			}
		}
	}

	//DELETE: Borrar
	var DELETE = 46;
	if (kC == DELETE) {
		url = document.getElementById('linkborrar');
		if (url != null) {
			eval(url.href);
			return false;
		}
	}


	//CTRL + <-
	var ANT = 37;
	if (kC == ANT && isControlKeyOn) {
		url = document.getElementById('linkatras');
		if (url != null) {
			verUrl(url.href);
			return false;
		}
	}

	var FLECHA_ARRIBA = 38;
	if (kC == FLECHA_ARRIBA) {
		if (isControlKeyOn) {
			controlAct = document.activeElement.id;
			l = controlAct.length;
			if (l > 4) {
				//busca que sea un campo ej: idarticulodesc -> idarticulo_oc
				objOpenName = controlAct.substring(0, l - 4) + '_oc';
				url = document.getElementById(objOpenName);
				if (url != null) {
					verUrl(url.href);
					return false;
				}
			}
		}
	}

	//CTRL + ->
	var SIG = 39;
	if (kC == SIG && isControlKeyOn) {
		url = document.getElementById('linksiguiente');
		if (url != null) {
			verUrl(url.href);
			return false;
		}
	}

	//ENTER, prox foco
	/*
	if (kC == ENTER)
	{
		controlAct = document.activeElement.id;
		f = document.getElementById('form1');
		c = document.getElementById(controlAct);
		if (c.type == 'text')
		{	
			nextFocus(f, c, kC);
		}
	}
	*/

	//intenta ver si el nro acumulado en sucesivos keys es un link de una operacion visible
	var num = String.fromCharCode(kC);

	//analiza si presionó teclado numérico
	if (kC >= 96 && kC <= 105)
		num = kC - 96;

	if (isNaN(parseInt(num))) {
		opId = '';
		return true;
	}

	opId = opId + '' + num;

	url = document.getElementById('op' + opId);
	if (url != null) {
		opId = '';
		verUrl(url.href);
		return false;
	}

	return true;
}

/**
 * Selecciona todo el contenido de un elemento HTML, usado para input text
 * @param {string} xid 
 */
function sc3SelectAll(xid) {
	document.getElementById(xid).focus();
	document.getElementById(xid).select();
}

//expande un atributo con el nombre dado
function sc3expand(x_strExpand, x_strExpansor, xlin, xnombre) {
	styleObj = document.getElementById(x_strExpand).style;
	styleLin = document.getElementById(xlin).style;
	if (styleObj.display == 'none') {
		styleObj.display = '';
		styleLin.display = 'none';
		document.getElementById(x_strExpansor).innerHTML = '- ' + xnombre;
	}
	else {
		styleObj.display = 'none';
		styleLin.display = '';
		document.getElementById(x_strExpansor).innerHTML = '+ ' + xnombre;
	}
}


function sc3expand2(x_strExpand, x_strExpansor, xnombre) {
	styleObj = document.getElementById(x_strExpand).style;

	if (styleObj.display == 'none') {
		styleObj.display = '';

		scrollY = 0;
		if (document.getElementById(x_strExpand).parentElement != null) {
			scrollY = document.getElementById(x_strExpand).parentElement.offsetTop;
			if ((scrollY <= 3) && document.getElementById(x_strExpand).parentElement.parentElement != null) {
				scrollY = document.getElementById(x_strExpand).parentElement.parentElement.offsetTop;
				if ((scrollY <= 3) && document.getElementById(x_strExpand).parentElement.parentElement.parentElement != null)
					scrollY = document.getElementById(x_strExpand).parentElement.parentElement.parentElement.offsetTop;
			}
		}
		document.getElementById(x_strExpansor).innerHTML = xnombre + ' <i class="fa fa-angle-double-up fa-lg"></i>';

		//si hay scroll lo usa
		if (scrollY > 1)
			window.scrollTo(0, scrollY);
	}
	else {
		styleObj.display = 'none';
		document.getElementById(x_strExpansor).innerHTML = xnombre + ' <i class="fa fa-angle-double-down fa-lg"></i>';
	}
}


function sc3expandMaster(x_strExpand, x_strExpansor, xnombre) {
	styleObj = document.getElementById(x_strExpand).style;
	if (styleObj.display == 'none') {
		styleObj.display = '';
	}
	else {
		styleObj.display = 'none';
	}
}


//recalcula una expresion float, por si pone "124 * 1.21" y luego presiona Enter
function mathEvalExpresion(xid) {
	obj = document.getElementById(xid);
	objdiv = document.getElementById(xid + '__exp');

	if (obj == null)
		return 0;

	valor = obj.value;
	//muestra el cálculo realizado
	if (objdiv != null)
		objdiv.innerHTML = '<small> ' + valor + '</small>';

	valor = eval(valor);
	valor = Math.round(valor * 1000) / 1000;
	obj.value = valor;
}

//presionado el ENTER, eval�a expresi�n
function mathEvalKey(xid, xevent) {
	if (xevent.keyCode == 13) {
		mathEvalExpresion(xid);
	}
}

function isNumeric(xdesc) {
	return !isNaN(xdesc);
}

//flechas y home, end
function esTeclaNavegacion(kC) {
	if (kC >= 33 && kC <= 40)
		return true;
	return false;
}

//si las flechas hacia arriba o abajo son presionadas busca un control con el nombre igual seguido de +1 o -1 de indice
//EJ: pedido15, busca pedido14 o pedido16 segun que flecha presione y posa el foco ah�
function handleFlechas(i, kC, control) {
	var FLECHA_ARRIBA = 38;
	if (kC == FLECHA_ARRIBA) {
		obj = document.getElementById(control + (i - 1));
		if (obj != null)
			obj.focus();
		return;
	}

	var FLECHA_ABAJO = 40;
	if (kC == FLECHA_ABAJO) {
		obj = document.getElementById(control + (i + 1));
		if (obj != null)
			obj.focus();
		return;
	}
}


//---------------------------------Funciones JS de aplicaciones ------------------------------------------

/**
 * Busca la existencia de la funcion para el evento dado y la invoca
 * @param {string} xquery 
 * @param {string} xfield 
 * @param {string} xevent 
 * @param {string} xevent2 
 */
function sc3firejs(xquery, xfield, xevent, xevent2) {
	//busca determinar si el campo es float para evaluar la expresi�n ingresada
	obj = document.getElementById(xfield + '__f');
	if (obj != null) {
		mathEvalKey(xfield, xevent2);
	}

	fncName = xquery + '_' + xfield + '_' + xevent;
	eval('if(typeof ' + fncName + ' == \'function\') { ' + fncName + '();}');
}


/*
Busca la existencia de la funcion para el evento dado y la invoca
*/
function sc3fireValidar(xquery) {
	resultado = true;
	fncName = xquery + '_validar';
	eval('if(typeof ' + fncName + ' == \'function\') { resultado = ' + fncName + '();}');
	return resultado;
}

//convierte milisegundos a MM:SS
function convertTimeToMinSeg(miliseconds) {
	var totalSeconds = Math.floor(miliseconds / 1000);
	var minutes = Math.floor(totalSeconds / 60);
	var seconds = totalSeconds - minutes * 60;
	if (seconds < 10)
		seconds = '0' + seconds;
	return minutes + ':' + seconds;
}

//convierte milisegundos a minutos
function convertTimeToMin(miliseconds) {
	var totalSeconds = Math.floor(miliseconds / 1000);
	var minutes = Math.floor(totalSeconds / 60);
	return minutes;
}



/*==================================================
  Set the tabber options (must do this before including tabber.js)
  ==================================================*/

var tabberOptions = {
	'cookie': "tabber", /* Name to use for the cookie */
	'manualStartup': "true",
	'onLoad': function (argsObj) {
		var t = argsObj.tabber;
		var i;
		/* Optional: Add the id of the tabber to the cookie name to allow
		   for multiple tabber interfaces on the site.  If you have
		   multiple tabber interfaces (even on different pages) I suggest
		   setting a unique id on each one, to avoid having the cookie set
		   the wrong tab.
		*/
		if (t.id) {
			t.cookie = t.id + t.cookie;
		}

		/* If a cookie was previously set, restore the active tab */
		i = parseInt(getCookie(t.cookie));
		if (isNaN(i)) { return; }

		//reemplazando linea de abajo por: t.tabShow(i); al abrir setea la solapa abierta anteriormente
		t.tabShow(0);
	},
	'onClick': function (argsObj) {
		var c = argsObj.tabber.cookie;
		var i = argsObj.index;
		//descomentando esta l�nea recuerda el indice del tab para la proxima vez
		//setCookie(c, i);
	}
};


/*==================================================
  Cookie functions
  ==================================================*/

function setCookie(name, value, expires, path, domain, secure) {
	document.cookie = name + "=" + escape(value) +
		((expires) ? "; expires=" + expires.toGMTString() : "") +
		((path) ? "; path=" + path : "") +
		((domain) ? "; domain=" + domain : "") +
		((secure) ? "; secure" : "");
}

function getCookie(name) {
	var dc = document.cookie;
	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);
	if (begin == -1) {
		begin = dc.indexOf(prefix);
		if (begin != 0) return null;
	}
	else {
		begin += 2;
	}
	var end = document.cookie.indexOf(";", begin);
	if (end == -1) {
		end = dc.length;
	}
	return unescape(dc.substring(begin + prefix.length, end));
}

function deleteCookie(name, path, domain) {
	if (getCookie(name)) {
		document.cookie = name + "=" +
			((path) ? "; path=" + path : "") +
			((domain) ? "; domain=" + domain : "") +
			"; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

//llamado desde mil lugares
function initRTE() {

}

// vieja funcion de Rich text RTE, ahora invoca la version de Quill.js si existe
function updateRTEs() {
	fn = 'updateQuillValue';
	eval('if(typeof ' + fn + ' == \'function\') { ' + fn + '();}');
}

//ilumina una fila y luego de 0.8 segundos la apaga
function iluminarFila(i, xtimeout) {
	if (isUndefined(xtimeout))
		xtimeout = 800;

	row = document.getElementById('tr' + i);
	if (row != null) {
		row.classList.add("iluminada");
		setTimeout(function () { row.classList.remove("iluminada"); }, xtimeout);
	}
}


/**
 * Retorna true si todas las palabras buscadas están en el texto (sin importar el orden)
 * @param {string} text 
 * @param {Array} searchWords 
 */
function multiSearchAnd(text, searchWords) {
	if (searchWords == '')
		return true;

	return searchWords.every((el) => {
		if (text.toLowerCase().includes(el))
			return true;
		return false;
	});
}


/**
 * Muestra un mensaje flotante durante el tiempo dado en milisegundos y se oculta
 * @param {*} texto 
 * @param {*} tiempo 
 */
function mensajeEmergente(texto, tiempo = 3000) {

	var cartel = document.getElementById("cartel");
	var p = document.createElement("p");
	textoNode = document.createTextNode(texto);

	if (cartel) {
		cartel.removeChild(cartel.lastChild);
		p.appendChild(textoNode);
		cartel.appendChild(p);
	} else {
		var crear = document.createElement("div");
		crear.id = "cartel";
		p.appendChild(textoNode);
		crear.appendChild(p);
		document.body.appendChild(crear);
	}

	cartel = document.getElementById("cartel");
	cartel.style.display = "block";
	setTimeout(function () {
		cartel.style.opacity = "0.8";
	}, 20);
	setTimeout(function () {
		cartel.style.opacity = 0;
		cartel.style.display = "none";
	}, tiempo);
}

/**
 * Por compatibilidad con condiciones de PHP
 * @param {string} xA 
 * @param {string} xB 
 */
function sonIguales(xA, xB) {
	return xA == xB;
}

function strcmp(xA, xB) {
	if (xA == xB)
		return 0;
	return 1;
}

/**
 * Retorna un GUID unico
 * @returns 
 */
function GUID() {
	var date = new Date;
	var year = date.getFullYear();
	var mes = date.getMonth() + 1;
	var dia = date.getDate();
	var seconds = date.getSeconds();
	var minutes = date.getMinutes();
	var hour = date.getHours();
	var requestGUID = 'G' + year + mes + dia + '-' + hour + minutes + seconds + '-' + Math.random().toString(36).substring(2, 8);
	return requestGUID;
}

/**
 * Se duerme X segundos
 * @param {*} ms 
 * @returns 
 */
async function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}

