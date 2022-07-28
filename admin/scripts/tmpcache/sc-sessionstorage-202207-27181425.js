/**
 * Desarrollado por SC3, funciones de SessionStorage
 * CACHE1
 * Fecha: ago-2019
 * Autor: Marcos C.
 */

/**
 * Borrando...
 */
function sc3SSClear()
{
	console.log('Limpiando sessionStorage...');
	sessionStorage.clear();	
}

/**
 * Guarda una clave en el localStorage
 */
function sc3SSSet(xkey, xvalor)
{
	sessionStorage.setItem(gCacheName + "-" + xkey, xvalor);
}

/**
 * Borra la cache de la tabla dada. Usado en sc-upitem.php para que borre en caso de actualizar
 * TODO: hacerlo por registro
 * @param xtabla
 * @returns
 */
function sc3SSInvalidarCache1(xtabla)
{
	sessionStorage.deleteItem(gCacheName + "-" + xtabla);
}


function sc3SSGet(xkey)
{
	//console.log('Local Cache (sessionStorage): buscando ' + xkey);
	valor = sessionStorage.getItem(gCacheName + "-" + xkey);
	if (valor === undefined || valor === null)
	{
		valor = '';
	}
	return valor;
}

function sc3SSSetArray(xkey, xArr)
{
	sc3SSSet(xkey, JSON.stringify(xArr));	
}

/**
 * Recuperar el array JSON
 * Array vacio si no lo encuentra 
 */
function sc3SSGetArray(xkey)
{
	txt = sc3SSGet(xkey);
	var arr = [];
	if (txt != '')
		return JSON.parse(txt);
	return arr;
}



/**
 * Recupera la tabla, administra sola la caché
 * @param xkey Nombre de la tabla
 */
function sc3SSGetTable(xkey)
{
	aTbl = sc3SSGetArray(xkey);
	if (aTbl.data)
	{
		if (Array.isArray(aTbl.data))
			return aTbl.data;
			
		return JSON.parse(aTbl.data);
	}
	return aTbl;	
}


/**
 * Carga una tabla vía AJAX y la almacena en Session Storage
 * Luego que termina invoca funcion callback
 * Usar sc3LoadTableCache1() mejor porque verifica checksum
 * @returns
 */
function sc3LoadTableIntoCache1(xtabla, xforzarRefresh, xcallback)
{
	aTbl = sc3SSGetArray(xtabla);
	//si encuentra datos y no fuerza el refresh, usa esos datos e invoca callback fn 
	if (sc3ArrayVacio(aTbl) || xforzarRefresh)
	{
		//si no hay datos o fuerza el refresh, avanza
		url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&all=1";
	
		getAjaxCall(url).then(function(response) {
								//console.log("sc3LoadTableIntoCache1(), por guardar tabla", xtabla, 'fuerza refresh', xforzarRefresh);
								sc3SSSet(xtabla, response);
								if (xcallback) xcallback();
				}, 
				function(error) {
					console.error("sc3LoadTableIntoCache1(", xtabla ,"): ERROR: ", error);
				});
	}
	else
	{
		if (xcallback) xcallback();
		return;
	}
}

/**
 * Actualiza un registro por ID con el xid dado. 
 * Luego almacena tabla en CACHE1
 * @param xtabla
 * @param xid
 * @param xrow
 * @returns
 */
function sc3UpdateTableIdCache1(xtabla, xid, xrow)
{
	aTbl = sc3SSGetArray(xtabla);
	//borra el registro viejo, si existe
	aTbl = sc3BorrarPorId(aTbl, xid);
	//agrega el registro nuevamente
	aTbl.push(xrow);
	//finalmente la graba
	sc3SSSetArray(xtabla, aTbl);
}

/**
 * Carga un registro de una tabla vía AJAX y la almacena en Session Storage
 * Luego que termina invoca funcion callback
 * @returns
 */
function sc3UpdateTableRowCache1(xtabla, xid, xcallback)
{
	//... copiado de sc3ObtenerRowJS(xtabla, xkeyfield, xid)
	url = "sc-ajax-obtenervalor.php?tabla=" + xtabla + "&id=" + xid + "&field=ROW&keyfield=id";

	getAjaxCall(url).then(function(response) {
							console.log("sc3UpdateTableRowCache1()", xtabla, 'id', xid);

							//para determinar edad del dato
							response['__TIMESTAMP'] = sc3Timestamp();

							//actualiza la tabla en CACHE1
							sc3UpdateTableIdCache1(xtabla, xid, response);
							
							if (xcallback) xcallback();
			}, 
			function(error) {
				console.error("sc3UpdateTableRowCache1(", xtabla ,"):ERROR: ", error);
			});
}



/**
 * Carga una tabla vía AJAX y la almacena en Session Storage
 * Luego que termina invoca funcion callback
 * @param xwaitForIt: parametro opcional si requiere datos frescos si o si
 * @returns
 */
function sc3LoadTableCache1(xtabla, xcallback, xwaitForIt)
{
	chk = -1;
	aTbl = sc3SSGetArray(xtabla);

	//17-may: cambiado por defecto a false...!
	if (xwaitForIt === undefined)
		xwaitForIt = false;

	//no hay datos
	if (sc3ArrayVacio(aTbl))
	{
		//si no hay datos: a buscarlos avanza
		url = "sc-ajax-cache.php?tabla=" + xtabla;
	
		getAjaxCall(url).then(function(response) {
								//a veces viene "deslogueado"
								if (response.substring(0, 1) != '<')
								{
									sc3SSSet(xtabla, response);
									if (xcallback) xcallback();
								}
					}, 
					function(error) {
						console.error("sc3LoadTableCache1(", xtabla ,"): ERROR: ", error);
					});
	}
	else
	{
		chk = aTbl.checksum;

		//ha decidido no esperar nuevos datos: invoca callback (y puede estar offline). 
		if (!xwaitForIt)
			if (xcallback) xcallback();

		//no sabemos si los datos son buenos (frescos)!
		url = "sc-ajax-cache.php?tabla=" + xtabla + "&chk=" + chk;
	
		getAjaxCall(url).then(function(response) {
								aResponse = JSON.parse(response);
								//si no requiere reload, la opcion .data viene vacía.
								if (aResponse.reload == 1)
									sc3SSSet(xtabla, response);

								//habia decidido esperar nuevos datos: llegaron (o ya estaban vigentes)!
								if (xwaitForIt)
									if (xcallback) xcallback();						
						}, 
						function(error) {
							console.error("sc3LoadTableCache1(", xtabla ,"): ERROR: ", error);
						});
		
		return;
	}
}


/**
 * Carga una tabla (filtrada por un where)vía AJAX y la almacena en Session Storage
 * Luego que termina invoca funcion callback
 * @param xwaitForIt: parametro opcional si requiere datos frescos si o si
 * @returns
 */
function sc3LoadTableCache1Where(xtabla, xwhere, xcallback)
{
	chk = -1;
	//guarda con un nombre similar a la tabla, y siempre pisa
	tablaKey = xtabla + '-PARCIAL';

	//busca los datos si o si, el where va en base64
	url = "sc-ajax-cache.php?tabla=" + xtabla + '&where=' + btoa(xwhere);

	getAjaxCall(url).then(function(response) {
								//a veces viene "deslogueado"
								if (response.substring(0, 1) != '<')
								{
									sc3SSSet(tablaKey, response);
									if (xcallback) 
										xcallback();
								}
					}, 
			function(error) {
				console.error("sc3LoadTableCache1Where(", xtabla ,"): ERROR: ", error);
			});

}

