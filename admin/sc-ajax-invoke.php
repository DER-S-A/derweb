<?php 
require("funcionesSConsola.php");

//TODO: hacer login
//checkUsuarioLogueado();

//siempre UTF-8
header('Content-type: application/json; charset=utf-8');

$funcion = RequestSafe("fn");
$params = Request("p");

$aparams = array();
if (!esVacio($params))
{
	$aparams = json_decode($params, true);
	if ($aparams === NULL)
	{	
		$params = str_replace("\\", "", $params);
		$aparams = json_decode($params, true);
	}
	
	if ($aparams === NULL)
	{
		//echo("aun vacio: $params");
		$params = "[{\"idproveedor\":45}]";	
		$aparams = json_decode($params, true);
	}
}

if (!isset($aparams[0]))
	$aparams[0] = "";

$modulo = "";
//Recupero Ajax Helper que tiene funciones registradas para ser invocadas desde javascript via AJAX, asincr�nico
$ajaxH = sc3GetAjaxHelper();
if ($ajaxH->isRegisteredFunction($funcion))
{
	$modulo = $ajaxH->getModuloFunction($funcion);
	
	if (!esVacio($modulo))
		require $modulo;
	
	$aResult = array();
	eval('$aResult = ' . $funcion . '($aparams[0]);');

	//MC: 15-may-2018: codificar
	//Casos como "La Reina" generaban problemas por la comillas
	//$json = str_replace("\u0022","\\\\\"", json_encode($aResult, JSON_HEX_QUOT));
	$json = json_encode($aResult, JSON_HEX_QUOT);
	echo($json);
	
	// echo(json_encode($aResult));
	
	return;
}

if ($funcion == "VEN_PEDIDO_UPDATE")
{
	require("app-ven.php");
	echo(venPedidoUpdate($aparams[0]));
}
else
{
    //$aparams['fn'] = "funcion no registrada $funcion";
	print_r($aparams[0]);
}?>