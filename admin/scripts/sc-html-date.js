/**
 * Funciones de fecha, usadas en sc-html-date.php
 * Autor: SC3 - Ezequiel
 */


function comparaFechaHasta(inputD, inputH, combo)
{
	inputDesde = document.getElementById(inputD);
	inputHasta = document.getElementById(inputH);
	if (inputDesde.value > inputHasta.value)
	{
		inputDesde.value = inputHasta.value;
	}
	document.getElementById(combo).value = 'otro';
}

function comparaFechaDesde(inputD, inputH, combo)
{
	inputDesde = document.getElementById(inputD);
	inputHasta = document.getElementById(inputH);
	if (inputDesde.value > inputHasta.value)
	{
		inputHasta.value = inputDesde.value;
	}
	document.getElementById(combo).value = 'otro';
}


function cambiarIntervalos(inputD, inputH, combo)
{
	let inputDesde = document.getElementById(inputD);
	let inputHasta = document.getElementById(inputH);
	let opciones = document.getElementById(combo);
	let anio = new Date();
	let mes = new Date();
	let dia = new Date();
	let restar = dia.getDate()-1;
	
	if (opciones.value == "" || opciones.value == "mes_actual") 
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate()-restar);
		
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);           
	}
	if (opciones.value == "ultimo_mes")
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()-1, dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);   
	}
	if (opciones.value == "ultimos_tres_meses")
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()-3, dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);   
	}
	if (opciones.value == "ultimo_mes")
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()-1, dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "proximo_mes")
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()+1, dia.getDate()-restar);
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth()+2, 0);
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "ultimos_tres_meses")
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()-3, dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "ultimo_anio")
	{
		fechaDesde = new Date(anio.getFullYear()-1, mes.getMonth(), dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "mes_pasado")
	{
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()-1, dia.getDate()-restar);
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), 0);
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "anio_pasado")
	{
		fechaDesde = new Date(anio.getFullYear()-1, 0, 1);
		fechaHasta = new Date(anio.getFullYear()-1, 12, 0);
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "hoy")
	{   
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "ayer")
	{   
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate()-1);
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate()-1);
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == "ultimos_6_meses")
	{        
		fechaDesde = new Date(anio.getFullYear(), mes.getMonth()-6, dia.getDate());
		fechaHasta = new Date(anio.getFullYear(), mes.getMonth(), dia.getDate());
		inputDesde.value = formatFechaSql(fechaDesde);
		inputHasta.value = formatFechaSql(fechaHasta);
	}
	if (opciones.value == 'ultima_decada')
	{
		inputDesde.value = '2000-01-01';
		inputHasta.value = formatFechaSql(new Date(anio.getFullYear() + 10, 0, 1));
	}

}

