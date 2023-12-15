//common ajax functions



function getHTTPObject() {
	var xmlhttp;
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		try {
			xmlhttp = new XMLHttpRequest();
		}
		catch (e) {
			xmlhttp = false;
		}
	}
	return xmlhttp;
}

var vIdControl = '';
var vDesc = '';
var xmlhttp;
var xmlhttp2;

//timer de búsqueda de los controles
var gTimeoutFnBusqueda = null;


/**
 * Makes the ajax call to translate code-description
 * @param {string} xCode 
 * @param {*} xDesc 
 * @param {*} xquery 
 * @param {*} xMasterSel 
 * @param {*} xMasterField 
 * @param {*} xMasterDesc 
 * @returns 
 */
function lookUp(xCode, xDesc, xquery, xMasterSel, xMasterField, xMasterDesc) {
	vIdControl = xCode;
	vDesc = xDesc;
	masterValue = "";
	var code = document.getElementById(xCode).value;
	if (xMasterSel != "") {
		masterValue = document.getElementById(xMasterSel).value;
		if (masterValue == "" && code != "") {
			//$xextendedFilter
			alert("Elija antes un valor en el campo '" + xMasterDesc + "'");
			document.getElementById(xMasterSel).focus;
			return;
		}
	}

	//el * abre la ventana
	if (code == '*') {
		l = xCode.length;
		objOpenName = xCode + '_oc';
		url = document.getElementById(objOpenName);
		if (url != null) {
			verUrl(url.href);
			document.getElementById(xCode).value = '';
			return false;
		}
	}

	//si ingresa caracter, va al otro control
	if (code.match(/[a-z]/i) || code.match(/[A-Z]/i)) {
		document.getElementById(xCode).value = '';
		document.getElementById(xCode + 'desc').value = code;
		document.getElementById(xCode + 'desc').focus();
		return false;
	}

	if (gTimeoutFnBusqueda)
		clearTimeout(gTimeoutFnBusqueda);

	//vamos a buscar, pero después de 0,5 seg
	gTimeoutFnBusqueda = setTimeout(function () {
		lookUpDelayed(xCode, xquery, xMasterField, code);
	}, 500);
}


/**
 * Finalmente busca después de un tiempo
 */
function lookUpDelayed(xCode, xquery, xMasterField, code) {

	document.getElementById(vDesc).value = "buscando...";
	url = "sc-ajax-selector.php";
	xmlhttp = getHTTPObject();
	url = url + "?query=" + xquery + "&control1=" + xCode + "&id=" + code + "&mfield=" + xMasterField + "&mid=" + masterValue;

	xmlhttp.open("GET", url, true);
	xmlhttp.onreadystatechange = handleLookUp;
	xmlhttp.send(null);
}

/**
 * Llegaron los resultados
 */
function handleLookUp() {
	if (xmlhttp.readyState == 4) {
		document.getElementById(vDesc).value = xmlhttp.responseText;
		onTranslateFn = vIdControl + "_OnTranslate";
		eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');
	}
}

//encola los requerimientos ajax, espera 5 caracteres y 0.3 seg para buscar
var lookUp2AfterTranslateScript = '';
var delayedAjaxUrl = '';
var delayedAjaxObj = '';
var delayedAjaxWaiting = false;

//llevado a var global en include-head.php
//var lookUpMinLongitud = 5;

/*
Dada la descripcion, busca el codigo
*/
function lookUp2(xCode, xDesc, xquery, xMasterSel, xMasterField, xMasterDesc, xlookUp2AfterTranslateScript, xIgnorePrefix) {
	vDesc2 = xDesc;
	vCode2 = xCode;

	lookUp2AfterTranslateScript = xlookUp2AfterTranslateScript;
	masterValue = "";
	if (xMasterSel != "") {
		masterValue = document.getElementById(xMasterSel).value;
		if (masterValue == "") {
			alert("Elija antes un valor en el campo '" + xMasterDesc + "'");
			document.getElementById(xMasterSel).focus;
			return;
		}
	}

	var desc = document.getElementById(xDesc).value;

	if (xIgnorePrefix != '' && desc.startsWith(xIgnorePrefix))
		return;

	if ((desc.length >= lookUpMinLongitud) && (!delayedAjaxWaiting)) {
		url = "sc-ajax-selector2.php";
		url = url + "?query=" + xquery + "&mfield=" + xMasterField + "&mid=" + masterValue;
		delayedAjaxUrl = url;
		delayedAjaxObj = xDesc;

		setTimeout(function () { lookUp2Delayed() }, 300);
		delayedAjaxWaiting = true;
	}
}

/*
 * Luego de un tiempo... busca
 * */
function lookUp2Delayed() {
	var desc = document.getElementById(delayedAjaxObj).value;
	url = delayedAjaxUrl + "&desc=" + desc;
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, true);
	xmlhttp.onreadystatechange = handleLookUp2;
	delayedAjaxWaiting = false;
	xmlhttp.send(null);
}

/*
Si viene la rta es de la forma CODIGO#VALOR
se hace un split por "#" para obtener clave y valor
*/
function handleLookUp2() {
	if (xmlhttp.readyState == 4) {
		rta = xmlhttp.responseText;
		if (rta != "") {
			rtas = rta.split("#");
			document.getElementById(vCode2).value = rtas[0];
			document.getElementById(vDesc2).value = rtas[1];

			//en modo mobile el code muestra el id
			objCode = document.getElementById(vCode2 + 'code');
			if (objCode != null)
				objCode.innerHTML = rtas[0];

			//si hay script luego de traducir lo evalua
			if (lookUp2AfterTranslateScript != '') {
				templookUp2AfterTranslateScript = lookUp2AfterTranslateScript;
				lookUp2AfterTranslateScript = '';
				eval(templookUp2AfterTranslateScript);
			}
		}
		else {
			//no hay RTA 
			var desc = document.getElementById(delayedAjaxObj).value;
			if (desc.length > 7) {
				alert('No se ha encontrado ' + desc);
				document.getElementById(vCode2).value = '';
				document.getElementById(vDesc2).value = '';
			}
		}
	}
}

//invoca ajax pero espera resultado
function sc3ObtenerParamJS(xparametro, xvalor) {
	url = "sc-ajax-obtenerparametro.php?parametro=" + xparametro + "&valor=" + xvalor;
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText;
}

//invoca ajax pero espera resultado
function sc3ObtenerValorJS(xtabla, xid, xfield) {
	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=" + xid + "&field=" + xfield;
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText;
}

//invoca ajax pero espera resultado
function sc3ObtenerIdJS(xtabla, xvalor, xfield, xvalor2, xfield2) {
	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&valor=" + xvalor + "&field=" + xfield + "&valor2=" + xvalor2 + "&field2=" + xfield2;
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText * 1;
}

function sc3ObtenerAutoincrement(xtabla) {
	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=AUTOINCREMENT";
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText;
}


/**
 * Obtiene un Registro completo de una tabla y con un ID
 * TODO: hacer Asincronico
 */
function sc3ObtenerRowJS(xtabla, xkeyfield, xid) {

	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=" + xid + "&field=ROW&keyfield=" + xkeyfield;
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	strRta = xmlhttp.responseText;

	arrObj = JSON.parse(strRta);

	var d = new Date();
	var millis = d.getTime();
	//en minutos desde 1970
	arrObj['__TIMESTAMP'] = Math.floor(millis / 60000);
	return arrObj;
}


/**
 * Obtiene un Registro completo de una tabla y con un ID e invoca funcion callback when ready
 * @param {string} xtabla 
 * @param {string} xkeyfield 
 * @param {int} xid 
 * @param {function} xfn 
 * @returns 
 */
function sc3ObtenerRowCB(xtabla, xkeyfield, xid, xfn) {

	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=" + xid + "&field=ROW&keyfield=" + xkeyfield;
	xmlhttp = getHTTPObject();

	xmlhttp.open("GET", url, true);
	xmlhttp.onreadystatechange = xfn;
	xmlhttp.send(null);
	return true;
}


//Obtiene un Registro completo de una tabla y con un ID
function sc3ObtenerRowWhereJS(xtabla, xwhere) {

	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=-1&field=ROW_WHERE&where=" + xwhere;
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	strRta = xmlhttp.responseText;

	arrObj = JSON.parse(strRta);
	return arrObj;
}


function sc3Invoke(xfn, xaparam) {
	var jparam = JSON.stringify(xaparam);
	url = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);
	xmlhttp = getHTTPObject();
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText;
}

/**
 * invoca asincrónico
 * Usar sc3InvokeServerFn() mejor !
 */
function sc3InvokeCallback(xfn, xaparam, xcallback) {

	var jparam = JSON.stringify(xaparam);
	url = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);
	xmlhttp2 = getHTTPObject();
	xmlhttp2.onreadystatechange = xcallback;
	xmlhttp2.open("GET", url, true);
	xmlhttp2.send(null);
}


/**
 * Invoca una funcion del SERVER, 
 * Luego que termina invoca funcion callback
 * USAR esta por sobre las 2 superiores
 * @xfn nombre de la funcion
 * @xaparam Array json
 * @returns
 */
function sc3InvokeServerFn(xfn, xaparam, xcallback) {

	var jparam = JSON.stringify(xaparam);
	url = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);

	//version con "falsas" promesas
	getAjaxCall(url).then(function (response) {
		aRta = JSON.parse(response);
		if (xcallback)
			xcallback(aRta);
	},
		function (error) {
			console.error("sc3InvokeServerFn()", xfn, "ERROR: ", error);
		});
}


/**
 * Invoca una funcion del SERVER, dando URL y funcion. Codifica JSON de parámetros con BASE64
 * Es similar a sc3InvokeServerFn() pero no requiere registrar antes la funcion. Y en el servidor
 * se debe incluir sc-api.php que decodifica parámetros fn y p= que vienen en BASE64
 * Luego que termina invoca funcion callback
 * @param {string} xurl URL a la que se invoca
 * @param {string} xfn nombre de la funcion
 * @xaparam Array json
 * @returns
 */
function sc3InvokeServerApi(xurl, xfn, xaparam, xcallback) {
	//VIAJA En BASE 64
	var jparam = btoa(JSON.stringify(xaparam));
	var url = xurl + "?fn=" + xfn + "&p=" + jparam;

	//version con "falsas" promesas
	getAjaxCall(url).then(function (response) {
		aRta = JSON.parse(response);
		if (xcallback)
			xcallback(aRta);
	},
		function (error) {
			console.error("sc3InvokeServerApi()", xfn, "ERROR: ", error);
		});
}


//Obtiene un Registro completo de una tabla y con un ID e invoca funcion callback when ready
//VERSIÓN con "Falsas Promesas"
function sc3ObtenerRowCB3(xtabla, xkeyfield, xid, xcallback) {

	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=" + xid + "&field=ROW&keyfield=" + xkeyfield;

	//version con promesas
	getAjaxCall(url).then((response) => {
		aRta = JSON.parse(response);
		if (xcallback)
			xcallback(aRta);
	},
		function (error) {
			console.error("sc3ObtenerRowCB3()", xtabla, xid, "ERROR: ", error);
		});

}


/**
 * Obtiene un Registro completo de una tabla y con un WHERE e invoca funcion callback when ready
 * VERSIÓN con "Falsas Promesas"
 * @param {string} xtabla 
 * @param {string} xwhere 
 * @param {funcion} xcallback 
 */
function sc3ObtenerRowWhereCB(xtabla, xwhere, xcallback) {
	xwhere = encodeURI(xwhere);
	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=-1&field=ROW_WHERE&where=" + xwhere;

	//version con promesas
	getAjaxCall(url).then(function (response) {
		aRta = JSON.parse(response);
		if (xcallback)
			xcallback(aRta);
	},
		function (error) {
			console.error("sc3ObtenerRowWhereCB()", xtabla, xwhere, "ERROR: ", error);
		});
}


//var globales para carga de divs ------------------------------------------------------------------------------------
var divContent = '';
var xmlhttpDivs;

function loadDivUrl(xdiv, xurl) {
	divContent = xdiv;
	xmlhttpDivs = getHTTPObject();
	xmlhttpDivs.open("GET", xurl, true);
	xmlhttpDivs.onreadystatechange = handleDivContent;
	xmlhttpDivs.send(null);
}

function handleDivContent() {
	if (xmlhttpDivs.readyState == 4) {
		if (xmlhttpDivs.status == 200) {
			console.log('cargando div', divContent);
			document.getElementById(divContent).innerHTML = xmlhttpDivs.responseText;
		}
		else {
			alert("Problema al cargar DIV: " + xmlhttpDivs.statusText);
		}
	}
}


//variables globales para carga de combos ------------------------------------------------------------
var vSelectToLoadObj = '';
var xmlhttpCombo;

//carga un select con el resultado de una función del server
function sc3LoadSelectFromServer(xfn, xaparam, xselect, xcampoValor, xcampoDesc) {
	var jparam = JSON.stringify(xaparam);
	url = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);

	vSelectToLoadObj = xselect;
	xmlhttpCombo = getHTTPObject();
	xmlhttpCombo.open("GET", url, true);
	xmlhttpCombo.onreadystatechange = sc3LoadSelectFromServerCB;
	xmlhttpCombo.send(null);
}

//llega el resultado, carga select
function sc3LoadSelectFromServerCB() {
	if (xmlhttpCombo.readyState == 4) {
		if (xmlhttpCombo.status == 200) {
			strRta = xmlhttpCombo.responseText;
			arrObj = JSON.parse(strRta);
			objSelect = document.getElementById(vSelectToLoadObj);
			if (objSelect != null && (arrObj.length > 0)) {
				//primero lo vacía
				removeOptions(objSelect);

				//si hay mas de uno agrega el " - Seleccione - "
				/*if (arrObj.length > 1)
				{*/
				var option = document.createElement("option");
				option.text = " - Seleccione -";
				option.value = "";
				objSelect.add(option);
				//}

				i = 0;
				while (i < arrObj.length) {
					var option = document.createElement("option");
					option.text = arrObj[i].descripcion;
					option.value = arrObj[i].valor;

					//si arranca con '--' le cambia el estilo
					if (arrObj[i].descripcion.substring(0, 2) == '--')
						option.className = 'option-destacado';

					// may-2019: Si es el único lo deja seleccionado
					//TODO: ojo si no dispara eventos
					if (arrObj.length == 1)
						option.selected = true;

					objSelect.add(option);
					i++;
				}
			}
		}
		else {
			alert("Problema al cargar COMBO: " + xmlhttpCombo.statusText);
		}
	}
}


//vacia un SELECT
function clearSelect(xid) {
	objSelect = document.getElementById(xid);
	if (objSelect != null) {
		removeOptions(objSelect);
	}
}


function removeOptions(selectbox) {
	var i;
	for (i = selectbox.options.length - 1; i >= 0; i--) {
		selectbox.remove(i);
	}
}

/**
 * Si hay dos elementos, borra la opcion vacia: " - Seleccione - "
 * @param selectbox
 * @returns
 */
function removeEmptyOption(xid) {
	selectbox = document.getElementById(xid);
	if (selectbox != null) {
		var cant;
		cant = selectbox.options.length * 1.00;
		if (cant == 1) {
			selectbox.remove(0);
		}
	}
}

// codifica un texto reemplazando los \n por [ENTER] y aplicando encodeURI
function ajaxEncode(xstr) {
	str2 = xstr.split("\n").join(" [ENTER]");
	retorno = encodeURI(str2);
	return retorno;
}


/**
 * Hace llamada AJAX con (falsas) promesas !!!
 * @param url
 * @returns
 */
function getAjaxCall(url) {
	return new Promise(function (resolve, reject) {

		var req = getHTTPObject();
		req.open('GET', url);

		req.onload = function () {
			// This is called even on 404 etc so check the status
			if (req.status == 200) {
				// Resolve the promise with the response text
				resolve(req.response);
			}
			else {
				// Otherwise reject with the status text
				reject(Error(req.statusText));
			}
		};

		// Handle network errors
		req.onerror = function () {
			reject(Error("Network Error"));
		};

		// Make the request
		req.send();
	});
}


/**
 * Función: sc3AjaxCallStaticMethod
 * Ejecuta un método estático que se encuentra dentro de una clase en PHP usando
 * ajax-call-function.php como script principal.
 * @param {string} xclassname       Nombre de la clase donde se encuentra el método a ejecutar
 * @param {string} xmethodname      Nombre del método a ejecutar
 * @param {JSON Array} xfnargs      Array JSON con los parámetros que recibe el método a ejecutar
 * @param {JS function} xcallback   Función callback para procesar la repuesta.
 * @param {bool} xasync             Indica si se ejecuta asyncrónico o no. Por defecto true.
 */
function sc3AjaxCallStaticMethod(xclassname, xmethodname, xfnargs, xcallback, xasync = true) {
	let xmlRequest = new XMLHttpRequest();
	xmlRequest.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			xcallback(this.responseText);
		}
	};

	let data = "method_name=" + JSON.stringify([xclassname, xmethodname]) + "&args=" + JSON.stringify(xfnargs);
	xmlRequest.open("GET", "ajax-call-function.php?" + data, xasync);
	xmlRequest.send();
}


