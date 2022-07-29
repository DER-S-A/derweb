
/**
 * Funciones de cache en localStorage o sessionStorage
 * Autor: Marcos C (info@sc3.com.ar)
 * Fecha: may-2018
 * Prefijo: sc3LC 
 **/


/**
 * Borra todo lo almacenado en Caché
 */
function sc3LCClear()
{
	console.log('Limpiando sessionStorage...');
	sessionStorage.clear();	
}


//arma una clave por dia, para que venzan los contenidos
function sc3LCKey()
{
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth() + 1;
	var yyyy = today.getFullYear();
	var hh = today.getHours();
	
	if(dd < 10) 
	{
	    dd = '0' + dd;
	} 
	if(mm < 10) 
	{
	    mm = '0' + mm;
	} 

	result = yyyy + '-' + mm + '-' + dd;	
}


/**
 * Guarda una clave en el sessionStorage
 */
function sc3LCSet(xkey, xvalor)
{
	console.log('  Local Cache (sessionStorage): guardando ' + xkey);
	sessionStorage.setItem(xkey, xvalor);
}


function sc3LCGet(xkey)
{
	console.log('Local Cache (sessionStorage): buscando ' + xkey);
	valor = sessionStorage.getItem(xkey);
	if (valor === undefined || valor === null)
	{
		console.log(' ->No encontrado.');
		valor = '';
	}
	else
		console.log(' ->HIT ');

	return valor;
}


