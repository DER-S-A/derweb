
/**
 * Funciones AJAX con cache en localStorage
 * Autor: Marcos C.
 * Fecha: may-2018
 * Incluir tambien sc-localcache.js
 **/

var ajaxObj = null;
var gConectado = 1;
var gUrl = '';


function getHTTPObject2() 
{ 
	var ajaxObj2 = null; 
	try 
	{ 
		ajaxObj2 = new XMLHttpRequest(); 
	} 
	catch (e) 
	{ 
		console.log('Error al crear XMLHttpRequest()');
	} 
	return ajaxObj2; 
}


//Obtiene un Registro completo de una tabla y con un ID e invoca funcion callback when ready
function sc3ObtenerRowCB2(xtabla, xkeyfield, xid, xfn)
{
	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=" + xid + "&field=ROW&keyfield=" + xkeyfield;
	ajaxObj = getHTTPObject2();

	ajaxObj.open("GET", url, true);
	ajaxObj.onreadystatechange = xfn;
	ajaxObj.send(null);
	return true;
}


//invoca asincrónico
function sc3InvokeCallback2(xfn, xaparam, xcallback)
{
	var jparam = JSON.stringify(xaparam);
	url = "sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);
	ajaxObj = getHTTPObject2();
	ajaxObj.onreadystatechange = xcallback;
	ajaxObj.open("GET", url, true);
	ajaxObj.send(null);
}


//
/**
 * Invoca funcion php asincrónico, usa cache 
 * @param xfn
 * @param xaparam
 * @param xcallback
 * @param xuseCache 
 * @returns
 */
function sc3InvokeCallback2Parent(xfn, xaparam, xcallback, xuseCache)
{
	var jparam = JSON.stringify(xaparam);
	url = "../sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);

	//guarda URL en var global para luego guardar key/resultado en CACHE
	gUrl = url;

	//Si usa caché, primero busca 
	if (xuseCache)
	{
		resultado = sc3LCGet(url);
		if (resultado != '')
		{
			xcallback(resultado);
			return;
		}
	}
		
	ajaxObj = getHTTPObject2();
	ajaxObj.onreadystatechange = xcallback;
	ajaxObj.open("GET", url, true);
	ajaxObj.send(null);
}


//Envia comando al server 
//TODO: guardar en lista de actiones pendientes en caso de estar desconectado
function sc3InvokeCallback2ParentAction(xfn, xaparam, xcallback)
{
	var jparam = JSON.stringify(xaparam);
	url = "../sc-ajax-invoke.php?fn=" + xfn + "&p=" + encodeURIComponent(jparam);
	
	ajaxObj = getHTTPObject2();
	ajaxObj.onreadystatechange = xcallback;
	ajaxObj.open("GET", url, true);
	ajaxObj.send(null);
}

