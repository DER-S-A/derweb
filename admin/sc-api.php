<?php
include("funcionesSConsola.php");
include("app-cel.php");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

/**
 * Madre de todas las API
 * feb-2021: SC3
 */

$debugApi = getParameterInt("sc3-api-debug", 0);

$fn = Request("fn");
$p = Request("p");
$version = Request("version", "");

$aParams = json_decode(base64_decode($p), true);
if (isset($aParams["id_usuario"])) {
	$idusuario = $aParams["id_usuario"];
	setSession("idusuario-logueado", (int) $idusuario);
}

if (esVacio($p))
	$aParams = array();

if ($debugApi == 1) {
	apiDebug($fn, $aParams, 1, $version);
}

if (esVacio($fn)) {
	$aRta = getAjaxResponseArray("", 0);
	$aRta["error"] = "Falta parametro fn";
} else {
	//no existe la funcion
	if (!function_exists($fn)) {
		$aRta = getAjaxResponseArray("", 0);
		$aRta["error"] = "Funcion $fn no existe";
	} else {
		eval('$aRta = ' . $fn . '($aParams);');
	}


	if ($debugApi == 1) {
		apiDebug("$fn RTA", $aRta);
	}
}

//Va rta
echo (json_encode($aRta));

/**
 * Guarda en el LOG
 */
function apiDebug($xfn, $xaDatos, $xInicio = 0, $version = "")
{
	$INICIO = "\r\n";
	if ($xInicio == 1)
		$INICIO .= "\r\n";

	$handle = fopen("logs/api-" . date("Y-m-d") . ".log", "a+");
	fwrite($handle, $INICIO . date("H:i:s") . " $version $xfn: " . substr(json_encode($xaDatos), 0, 5000));
	fclose($handle);
}

/**
 * Sólo test!
 */
function apiTest($xaDatos)
{
	$aRta = getAjaxResponseArray("apiTest", 1);
	$aRta["msg"] = "OK";
	return $aRta;
}
