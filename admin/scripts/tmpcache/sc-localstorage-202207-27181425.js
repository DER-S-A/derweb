/**
 * Desarrollado por SC3, funciones de LocalStorage
 * Fecha: jul-2019
 * Autor: Marcos C.
 */

function sc3LSClear()
{
	console.log('Limpiando localStorage...');
	localStorage.clear();	
}

/**
 * Guarda una clave en el localStorage
 */
function sc3LSSet(xkey, xvalor)
{
	localStorage.setItem(gCacheName + "-" + xkey, xvalor);
}


function sc3LSGet(xkey)
{
//	console.log('Local Cache (localStorage): buscando ' + xkey);
	valor = localStorage.getItem(gCacheName + "-" + xkey);
	if (valor === undefined || valor === null)
	{
		console.log(' ->No encontrado.');
		valor = '';
	}
	/*
	else
		console.log(' ->HIT ');
	 */
	return valor;
}

function sc3LSSetArray(xkey, xArr)
{
	sc3LSSet(xkey, JSON.stringify(xArr));	
}


/**
 * Recuperar el array JSON 
 * @param {string} xkey 
 * @returns array
 */
function sc3LSGetArray(xkey)
{
	txt = sc3LSGet(xkey);
	var arr = [];
	if (txt != '')
		return JSON.parse(txt);
	return arr;
}

/**
 * Recupera tabla
 * @param {string} xkey
 * @returns array
 */
function sc3LSGetTable(xkey)
{
	return sc3LSGetArray(xkey);
}


function sc3GetRowById(xtbl, xid)
{
	i = 0;
	while (i < xtbl.length)
	{
		if (xid == xtbl[i].id)
		{
			return xtbl[i];
		} 
		i++;
	}
	
	return '';
}


function sc3FiltrarTabla1(xtbl, xcampo, xvalor)
{
	var result = [];
	i = 0;
	while (i < xtbl.length)
	{
		if (xvalor == xtbl[i][xcampo])
		{
			result.push(xtbl[i]);
		} 
		i++;
	}
	
	return result;
}

/**
 * Borra un registro
 * @param xtbl
 * @param xid
 * @returns
 */
function sc3BorrarPorId(xtbl, xid)
{
	var result = [];
	i = 0;
	while (i < xtbl.length)
	{
		if (xid != xtbl[i].id)
		{
			result.push(xtbl[i]);
		} 
		i++;
	}
	
	return result;
}



function sc3FiltrarTablaLike(xtbl, xcampo, xvalor)
{
	var result = [];
	i = 0;
	while (i < xtbl.length)
	{
		if (xtbl[i][xcampo].toUpperCase().includes(xvalor.toUpperCase()))
		{
			result.push(xtbl[i]);
		} 
		i++;
	}
	
	return result;
}



function sc3RowVacio(xrow)
{
	if (Array.isArray(xrow) || xrow instanceof Object)
		return false;
	return true;
}
		

function sc3ArrayVacio(xarr)
{
	return xarr.length == 0;
}


/**
 * Carga combo con tabla contenido en Local Storage
 */
function sc3CargarComboTabla(xTabla, xCampoId, xCampoDesc, xSelect, xAddSeleccione)
{
	arrObj = sc3LSGetTable(xTabla);
	return sc3CargarComboArray(arrObj, xCampoId, xCampoDesc, xSelect, xAddSeleccione)
}
	
function sc3CargarComboArray(arrObj, xCampoId, xCampoDesc, xSelect, xAddSeleccione)
{
	if (xAddSeleccione)
	{
		var option = document.createElement("option");
		option.text = ' - Seleccione - ';
		option.value = 0;
		xSelect.add(option);
	}
	
	i = 0;
	while (i < arrObj.length)
	{
		var option = document.createElement("option");
		option.text = arrObj[i][xCampoDesc].substring(0, 35);
		option.value = arrObj[i][xCampoId];

		if (arrObj.length == 1 && !xAddSeleccione)
			option.selected = true; 
		
		xSelect.add(option);
		i++;
	}
}

