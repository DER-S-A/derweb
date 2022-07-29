/**
 * Funciones de metadata del sistema
 * Fecha: set-2019
 * Autor: Marcos C
*/

function cacheQueryName(xquery) {
	return xquery + '-metadata';
}


function cacheQueryDataName(xquery) {
	return xquery + '-data';
}

/**
 * Carga la metadata y luego la data si hace falta
 * @param xquery 
 */
function sc3LoadQueryMetadata(xquery) {
	console.log('sc3LoadQueryMetadata()', xquery);

	tbl = sc3SSGetTable(cacheQueryName(xquery));
	if (sc3ArrayVacio(tbl)) {
		//invoca funcion al server
		var params = [{ "query": xquery }];
		sc3InvokeServerFn('sc3LoadMetadata', params, sc3LoadQueryMetadataCB);
	}
}


function sc3LoadQueryMetadataCB(aResult) {
	query = aResult['queryname'];
	console.log("loadQueryMetadataCB()", query);
	sc3SSSetArray(cacheQueryName(query), aResult);
}

/**
 * Carga la tabla y analiza si requiere actualizar
 * @param {*} xquery 
 */
function sc3LoadQueryData(xquery, xcallback) {
	console.log('sc3LoadQueryData()', xquery);

	tbl = sc3SSGetTable(cacheQueryDataName(xquery));
	chk = -1;

	var params = [{
		"query": xquery,
		"chk": chk
	}];

	if (!sc3ArrayVacio(tbl)) {
		//hay datos pero vamos a revisar el checksum
		tbl = sc3SSGetArray(cacheQueryDataName(xquery));
		chk = tbl.checksum;

		//hay datos: invoca callback (por velocidad), luego busca actualización 
		if (xcallback)
			xcallback();

		var params = [{
			"query": xquery,
			"chk": chk
		}];

		sc3InvokeServerFn('sc3LoadData', params, function (aResult) {
			query = aResult['queryname'];
			if (aResult["reload"] == 1)
				sc3SSSetArray(cacheQueryDataName(query), aResult);
		});
	}
	else {
		sc3InvokeServerFn('sc3LoadData', params, function (aResult) {
			query = aResult['queryname'];
			if (aResult["reload"] == 1)
				sc3SSSetArray(cacheQueryDataName(query), aResult);

			if (xcallback)
				xcallback();
		});
	}
}


function sc3LoadQueryDataCB(aResult) {
	console.log("sc3LoadQueryDataCB()");
	query = aResult['queryname'];
	if (aResult["reload"] == 1)
		sc3SSSetArray(cacheQueryDataName(query), aResult);
}


class ScQueryInfo {
	constructor(xquery) {
		this.mQueryName = xquery;
		this.mTable = '';
	};

}


/**
 * Carga la metadata y luego la data si hace falta
 * @param xquery 
 */
function sc3LoadQueryOperaciones(xquery) {
	tbl = sc3SSGetTable(xquery + '-op');
	if (sc3ArrayVacio(tbl)) {
		//invoca funcion al server
		var params = [{ "query": xquery }];
		sc3InvokeServerFn('sc3LoadQueryOperaciones', params, sc3LoadQueryOperacionesCB);
	}
}


/**
 * Llegaron las operaciones para este QUERY
 * @param {*} aResult 
 */
function sc3LoadQueryOperacionesCB(aResult) {
	query = aResult['queryname'];
	sc3SSSetArray(query + '-op', aResult);
}
