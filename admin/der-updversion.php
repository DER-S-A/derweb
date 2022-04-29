<?php
/**
 * Este script contiene la actualización de versión del sistema
 * DERWEB.
 */

require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
include("der-updversion-clientes-potenciales.php");

// DERWEB Core
agregarOperGenerarEndPoint();

// Clientes potenciales.
agregarOperCliPot_CambiarEstado();
agregarOperCliPot_AgregarNotas();


/**
 * agregarOperGenerarEndPoint
 * Agrega la operación que permite generar el código base para un EndPoint.
 * @return void
 */
function agregarOperGenerarEndPoint() {
	$opid = sc3AgregarOperacion(
		"Agregar EndPoint", 
		"der-agregar-end-point.php", 
		"images/code.gif", 
		"Permite generar el código de un EndPoint a partir del nombre de una tabla.", 
		"", 
		"Desarrollador", 
		0, 
		"Root", 
		"", 
		0, 
		"");
}
?>