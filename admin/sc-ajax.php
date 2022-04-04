<?php

$AJAX_SESSION_KEY = "_AJAX_HELPER";

/**
 * Clase que registra las funciones que pueden invocarse via AJAX
 * @author marcos
 * @version 2015.08
 */
class ScAjaxHelper
{
	var $aFunctions = array();

	function __construct()
	{
		//ya registrado por el sistema 
		$this->registerFunction("sc3LoadMetadata", "");
		$this->registerFunction("sc3LoadData", "");
		$this->registerFunction("sc3LoadQueryOperaciones", "");
	}

	/**
	 * Registra una funcion para ser invocada via AJAX
	 * @param string $xfunc
	 * @param string $xcallbackFunc
	 */
	function registerFunction($xfunc, $xmodulo = "")
	{
		if (!$this->isRegisteredFunction($xfunc))
			$this->aFunctions[] = array("function" => $xfunc, 'modulo' => $xmodulo);
	}

	/**
	 * Retorna si la funcion está registrada
	 * @param strin $xfunc
	 * @return boolean
	 */
	function isRegisteredFunction($xfunc)
	{
		foreach ($this->aFunctions as $i => $fn) {
			if (sonIguales($xfunc, $fn["function"]))
				return true;
		}

		return false;
	}

	/**
	 * Retorna la funcion de callback o vacío
	 * @param strin $xfunc
	 * @return string string
	 */
	function getModuloFunction($xfunc)
	{
		foreach ($this->aFunctions as $i => $fn) {
			if (sonIguales($xfunc, $fn["function"]))
				return $fn["modulo"];
		}

		return "";
	}
}



function sc3AjaxInit()
{
	$ajax = new ScAjaxHelper();
	sc3SaveAjaxHelper($ajax);
}

/**
 * Recupera el Ajax Helper guardado en sesion
 * @return ScAjaxHelper
 */
function sc3GetAjaxHelper()
{
	global $AJAX_SESSION_KEY;
	if (!isset($_SESSION[$AJAX_SESSION_KEY])) {
		sc3AjaxInit();
	}

	$ajax = new ScAjaxHelper();
	$ajax = unserialize($_SESSION[$AJAX_SESSION_KEY]);
	return $ajax;
}


function sc3SaveAjaxHelper($xhelper)
{
	global $AJAX_SESSION_KEY;
	$_SESSION[$AJAX_SESSION_KEY] = serialize($xhelper);
}


/**
 * Arreglo básico de rta AJAX
 * @param string $xfn
 * @param int $xResult con el resultado a AJAX_RESULT, por defecto 1
 * @param array $xaParams con los parametros para copiar el GUID si está
 * @return array[]|number[]|string[]
 */
function getAjaxResponseArray($xfn = "", $xResult = 1, $xaParams = [])
{
	$hr = date("H:i");
	$fecha = date("d/m/Y");
	$aResult = [];
	$aResult["AJAX_RESULT"] = $xResult;
	$aResult["fn"] = $xfn;
	$aResult["error"] = "";
	$aResult["reload_hr"] = $hr;
	$aResult["reload_fecha"] = $fecha;
	$aResult["warning"] = "";
	$aResult["msg"] = "";
	$aResult["checksum"] = 0;
	$aResult["cant"] = 0;
	$aResult["rs"] = array();

	//devueve guid si está
	if (isset($xaParams["guid"]))
		$aResult["guid"] = $xaParams["guid"];

	return $aResult;
}

/**
 * Escapa utf y encodeURI() del javascript (cuando viene la info del browser).
 * Permite enviar códigos áéíóú con tranquilidad
 * @param string $xstr
 * @return mixed
 */
function escapeAjax($xstr)
{
	return escapeSql(urldecode($xstr));
}
