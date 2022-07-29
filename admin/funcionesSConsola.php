<?php

if (!session_id()) {
	session_start();
}

include("config.php");
include("dbobjecti.php");
include("sc-csv.php");

include("sc-html.php");
include("controles/sc-html-mensajes.php");
include("controles/sc-html-input-text.php");
include("controles/sc-html-table.php");
include("controles/sc-html-checkbox.php");
include("controles/sc-html-areas.php");
include("controles/sc-html-selector.php");
include("controles/sc-html-grid.php");
include("controles/sc-html-pdf.php");
include("controles/sc-html-tabs.php");
include("controles/sc-html-menu.php");
include("controles/sc-html-combo.php");
include("controles/sc-html-date.php");
include("controles/sc-html-boolean.php");
include("controles/sc-html-barcode.php");
include("controles/sc-html-botones.php");
include("controles/sc-html-texteditor.php");
include("controles/sc-html-color.php");
include("controles/sc-html-input-file.php");
include("controles/sc-html-graphic.php");
include("controles/sc-html-input-text-email.php");
include("controles/sc-html-divdatos.php");
include("controles/sc-html-divs.php");
include("controles/sc-html-buscador.php");

include("controles/sc-html-factura.php");
include("controles/sc-html-cbu.php");
include("controles/sc-html-cuit.php");

//conexion global, usada para realescape
$gDb = new BDObject();

//TODO: pasar a /core/
require("sc-fechautils.php");
include("sc-security.php");
include("debug-error.php");
include("sc-navigation-stack.php");
include("sc-cache.php");
include("sc-metadata.php");
include("sc-secuencias.php");
require("sc-pdfutils.php");
require("sc-fileutils.php");
require("sc-ajax.php");
require("sc-funciones-json.php");

if (!isset($INCLUDE_LIGHT) || $INCLUDE_LIGHT == 0) {
	include("sc-nroletras.php");
	include("sc-encrypt.php");
	include("sc-escritorio.php");
	include("sc-barcode3ro.php");
}

if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('America/Buenos_Aires');
}

require_once('terceros/cezpdf/src/Cezpdf.php');


$EMPTY_SELECTOR = "";
$VERSION = "1";
$RELEASE = "0";


if (esExcel()) {
	$xlsname = Request("xlsname");
	if (esVacio($xlsname))
		$xlsname = "datos";
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$xlsname.xls");
}


function esExcel()
{
	$format = Request("format");
	if (strcmp($format, "excel") == 0)
		return true;
	return false;
}


/**
 * Retorna si encuentra el parametro enviar=1
 */
function enviado()
{
	$enviar = RequestInt("enviar");
	if ($enviar == 1)
		return true;
	return false;
}

/**
 * Retorna la versión del sistema tomando la fecha de 
 * sc-updversion.php  o
 * sc-updversion-core.php
 * En formato vAA.MM (rd)
 */
function getVersion()
{
	$result = "v";
	$filename = "sc-updversion.php";
	if (file_exists($filename))
		$dia = (int) date("d", filemtime($filename));
	else {
		$filename = "sc-updversion-core.php";
		if (file_exists($filename))
			$dia = (int) date("d", filemtime($filename));
	}

	if ($dia >= 20)
		$dia = 3;
	elseif ($dia >= 10)
		$dia = 2;
	else
		$dia = 1;

	$result .= date("y.m", filemtime($filename)) . " (r$dia)";
	return $result;
}


/**
 * Toma un valor del $_GET o $_POST (gana en prioridad)
 * Si no se encuenta, retorna el valor $xdefault
 */
function Request($str, $xdefault = "")
{
	$strRet = "";
	$inGet = false;
	$inPost = false;

	global $_GET;
	global $_POST;

	if (is_array($_GET)) {
		$a = array_keys($_GET);
		if (is_array($a)) {
			if (in_array($str, $a)) {
				$strRet = $_GET[$str];
				$inGet = true;
			}
		}
	}

	//POST le gana a GET
	if (is_array($_POST)) {
		$a = array_keys($_POST);
		if (is_array($a)) {
			if (in_array($str, $a)) {
				$strRet = $_POST[$str];
				$inPost = true;
			}
		}
	}

	//$encriptaUrl = getParameterInt("sc3-encripta-url", 0);
	$encriptaUrl = 0;
	//intento descubrir si est� encriptado
	if (($encriptaUrl == 1) && esVacio($strRet)) {
		$a = array_keys($_GET);
		if (is_array($a)) {
			if (in_array("p", $a)) {
				$strE = $_GET["p"];
				if (!esVacio($strE)) {
					$enc = new Sc3Encriptador();
					$urlOrig = $enc->decryptUrl($strE);
					$avalores = explode2Niveles($urlOrig, "&", "=");
					if (isset($avalores[$str])) {
						$strRet = urldecode($avalores[$str]);
						$inGet = true;
					}
					$strRet = $avalores[$str];
				}
			}
		}
	}

	//no está presente en los parámetros, retorna el valor default
	if (esVacio($strRet) && !$inGet && !$inPost)
		return $xdefault;

	if (is_array($strRet))
		return $strRet;

	return trim($strRet);
}

/**
 * Retorna si está presente el parámetro en el GET o POST
 */
function inRequest($xParam)
{
	global $_GET;
	global $_POST;

	if (is_array($_GET)) {
		$a = array_keys($_GET);
		if (is_array($a)) {
			if (in_array($xParam, $a))
				return true;
		}
	}

	//POST le gana a GET
	if (is_array($_POST)) {
		$a = array_keys($_POST);
		if (is_array($a)) {
			if (in_array($xParam, $a))
				return true;
		}
	}

	return false;
}

/**
 * Retorna el valor del request y lo guarda en la sesion
 */
function requestOrSession($xstr)
{
	if (inRequest($xstr)) {
		setSession($xstr, Request($xstr));
		return Request($xstr);
	}

	return getSession($xstr);
}


function requestOrValue($xstr, $xvalue)
{
	$valor = Request($xstr);
	if (esVacio($valor))
		$valor = $xvalue;
	return $valor;
}

/*
* recupera en orden, el request, la session o el parametro
*/
function requestOrParameter($xrequest, $xparameter = "", $xdefault = "")
{
	if (sonIguales($xparameter, ""))
		$xparameter = $xrequest;

	$valorRequest = Request($xrequest);
	if (!sonIguales($valorRequest, "")) {
		saveParameter($xparameter, $valorRequest);
		return $valorRequest;
	}
	$valorParametro = getParameter($xparameter, $xdefault);
	return $valorParametro;
}


/**
 * Carga un arreglo con el request
 * @param string $xstr
 * @return array
 */
function RequestAll($xstr)
{
	$arr = array();
	$vars = "";
	if (isset($_GET[$xstr]))
		$vars = $_GET[$xstr];
	if (is_array($vars)) {
		foreach ($vars as $value) {
			array_push($arr, $value);
		}
	} else {
		if (isset($_POST[$xstr]))
			$vars = $_POST[$xstr];
		if (is_array($vars)) {
			foreach ($vars as $value) {
				array_push($arr, $value);
			}
		}
	}
	return $arr;
}


/**
 * Retorna un INT del request (previene sql-injection)
 * Da cero si no se puede convertir o es vacío
 */
function RequestInt($xstr, $xdefault = "0")
{
	return intval(Request($xstr, $xdefault));
}

/**
 * Retorna un INT del request (previene sql-injection)
 * Si el master coincide, toma mid
 */
function RequestIntMaster($xname, $xquery)
{
	$value = intval(Request($xname));
	if ($value == 0) {
		if (sonIguales(Request("mquery"), $xquery))
			$value = intval(Request("mid"));
	}
	return $value;
}

/**
 * Retorna un FLOAT del request (previene sql-injection)
 * Si no es válido o es vacio da cero
 */
function RequestFloat($xstr, $xdefault = "0")
{
	return floatval(Request($xstr, $xdefault));
}

/**
 * Retorna un STR del request (previene sql-injection)
 */
function RequestStr($xstr)
{
	$result = Request($xstr);
	$result = str_replace("'", "''", $result);
	return $result;
}


/**
 * Elimina comillas, espacios, guiones, puntos y comas
 */
function RequestSafe($xstr)
{
	$result = Request($xstr);
	$rep = array("'", " ", "-", ";", ",");
	$result = str_replace($rep, "", $result);
	return $result;
}

/**
 * Retorna la fecha en array
 * year, mon, mday, fecha_sql, fecha_legible, fecha_sql_2359, fecha_sql_0000, periodo_YYYYMM,
 * fecha_sql_ahora, 
 * es_hoy indica con 0|1
 */
function RequestFecha($xstr)
{
	$aResult = array("year" => 2010, "mon" => 01, "mday" => 01);
	$aHr = array("hr" => 0, "min" => 0);

	//analiza si viene el año separado del resto, viejo control de AAAA MM DD
	$anio = RequestInt($xstr . "_a");
	if ($anio != 0) {
		$aResult["year"] = $anio;
		$aResult["mon"] = RequestInt($xstr . "_m");
		$aResult["mday"] = RequestInt($xstr . "_d");

		$hr = RequestInt($xstr . "_h");
		$min = RequestInt($xstr . "_n");
		if ($hr + $min > 0) {
			$aHr["hr"] = $hr;
			$aHr["min"] = $min;
		}
	} else {
		//viene en formato 2020-04-19
		$fecha = Request($xstr);
		if (!esVacio($fecha)) {
			$aFecha = explode("-", $fecha);
			if (count($aFecha) == 3) {
				$aResult["year"] = $aFecha[0];
				$aResult["mon"] = $aFecha[1];
				$aResult["mday"] = $aFecha[2];
			}
		}
	}

	if ($aResult["mon"] < 10)
		$aResult["mon"] = "0" . (int) $aResult["mon"];

	if ($aResult["mday"] < 10)
		$aResult["mday"] = "0" . (int) $aResult["mday"];

	if ($aHr["hr"] < 10)
		$aHr["hr"] = "0" . (int) $aHr["hr"];

	if ($aHr["min"] < 10)
		$aHr["min"] = "0" . (int) $aHr["min"];

	//averigua si la fecha es hoy
	$diaHoy = date("d");
	$mesHoy = date("m");
	$anioHoy = date("Y");
	$esHoy = 0;
	if ($diaHoy == $aResult["mday"] && $mesHoy == $aResult["mon"] && $anioHoy == $aResult["year"])
		$esHoy = 1;

	$fechaSql = implode("-", $aResult) . " " . implode(":", $aHr);
	$fechaSql2359 = implode("-", $aResult) . " 23:59";
	$fechaSql0000 = implode("-", $aResult) . " 00:00";
	$fechaSqlNow = implode("-", $aResult) . " " . date("H:i");
	$fechaLegible = $aResult["mday"] . "/" . $aResult["mon"] . "/" . $aResult["year"];

	$aResult["es_hoy"] = $esHoy;
	$aResult["fecha_sql"] = $fechaSql;
	$aResult["fecha_legible"] = $fechaLegible;
	$aResult["fecha_sql_2359"] = $fechaSql2359;
	$aResult["fecha_sql_0000"] = $fechaSql0000;
	$aResult["fecha_sql_ahora"] = $fechaSqlNow;
	$aResult["periodo_YYYYMM"] = $aResult["year"] . $aResult["mon"];

	return $aResult;
}


/**
 * Recupera una variable de sesion, contempla que pueda estar compactada y en Base 64
 */
function getSession($str)
{
	$val = "";
	if (isset($_SESSION[$str])) {
		$val = $_SESSION[$str];
		if (startsWith($val, "--YXY--"))
			$val = descompactarB64(substr($val, 7));
	}
	return $val;
}

/**
 * Guarda una variable de session, opcionalmente la compacta y guarda en Base 64
 */
function setSession($str, $val, $xcompactar = 0)
{
	if ($xcompactar == 1) {
		$val = "--YXY--" . compactarB64($val);
	}
	$_SESSION[$str] = $val;
}


/**
 * Retorna el valor de una variable para un usuario
 * @param string $xvar
 */
function getVariableUsuario($xvar)
{
	return getSession("usuario_" . $xvar);
}

/**
 * Dado un texto, lo compacta y codifica en base 64
 * @param string $xtxt
 * @return string
 */
function compactarB64($xtxt)
{
	return base64_encode(gzcompress($xtxt));
}

/**
 * Dado un texto, lo descompacta
 * @param string $xtxt
 * @return mixed
 */
function descompactarB64($xtxt)
{
	return gzuncompress(base64_decode($xtxt));
}

/**
 * GUID unico para uso vario
 */
function GUID()
{
	return "G" . date("Ymd-his-") . sprintf('%04X-%04X', mt_rand(0, 65535), mt_rand(0, 65535));
}

function microtime_float()
{
	list($useg, $seg) = explode(" ", microtime());
	return ((float)$useg + (float)$seg);
}

/*
Elimina caracteres raros para el RTE
*/
function rteSafe($xtxt)
{
	//returns safe code for preloading in the RTE
	$tmpString = $xtxt;

	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);

	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
	//	$tmpString = str_replace("\"", "\"", $tmpString);

	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	return $tmpString;
}

/**
 * Escapa las comillas para evitar sql-injection
 * Si viene de Request mejor usar RequestStr()
 */
function escapeSql($xstr)
{
	$tmpString = $xstr;
	$tmpString = str_replace("'", "''", $tmpString);
	return $tmpString;
}

function comillasSql($xstr)
{
	return "'" . escapeSql($xstr) . "'";
}

function sinComillasSql($xstr)
{
	$tmpString = $xstr;
	$tmpString = str_replace("'", "", $tmpString);
	return $tmpString;
}

function escapeJsNombreVar($xstr)
{
	$tmpString = sinCaracteresEspeciales($xstr);
	$tmpString = str_replace("'", "", $tmpString);
	$tmpString = str_replace(" ", "", $tmpString);
	$tmpString = str_replace(".", "", $tmpString);
	return $tmpString;
}

function escapeJsValor($xstr)
{
	$tmpString = sinCaracteresEspeciales($xstr);
	$tmpString = str_replace("'", "", $tmpString);
	$tmpString = str_replace("\"", "", $tmpString);
	return $tmpString;
}


function sc3DefaultEncoding()
{
	return "UTF-8";
}

/**
 * Retorna path actual
 * @return mixed
 */
function path()
{
	$dir = dirname($_SERVER['SCRIPT_FILENAME']);
	$dir = str_replace("\\", "/", $dir);
	return $dir;
}


function getRemoteIp()
{
	return $_SERVER['REMOTE_ADDR'];
}

function getUserAgent()
{
	if (isMobileAgent())
		return "MOBILE: " . $_SERVER['HTTP_USER_AGENT'];

	return $_SERVER['HTTP_USER_AGENT'];
}

function isMobileAgent()
{

	$mobile_browser = 0;
	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|android|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		return TRUE;
	}

	if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		return TRUE;
	}

	if (RequestInt("mobile") == 1) {
		return TRUE;
	}

	if (isset($_SERVER['ALL_HTTP']) &&  strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
		return TRUE;
	}

	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
	$mobile_agents = array(
		'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
		'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
		'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
		'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
		'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
		'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
		'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
		'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
		'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
	);

	if (in_array($mobile_ua, $mobile_agents)) {
		$mobile_browser = 0;
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}

		$mobile_browser++;
	}

	if (isset($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
		$mobile_browser = 0;
	}


	if ($mobile_browser > 0)
		return TRUE;
	return FALSE;
}


/**
 * Retorna un ID (hash) unico de la pagina en funcion de su nombre
 * @return string
 */
function getPageId()
{
	return substr(md5($_SERVER['SCRIPT_NAME'] . Request("query")), 0, 5);
}

/**
 * Ubicacion actual, server/dominio y carpeta
 */
function thisUrl()
{
	$url = "";
	if (isset($_SERVER["SCRIPT_URI"]))
		$url = dirname($_SERVER["SCRIPT_URI"]);
	elseif (isset($_SERVER['SERVER_NAME']))
		$url = $_SERVER['SERVER_NAME'];
	return $url;
}


function getCacheName($xsitio)
{
	return substr(md5($xsitio), 0, 5);
}

/*
Retorna el str sin los acentos y ñ
*/
function sinCaracteresEspeciales($xstr)
{
	$sacar = array("(", ")", "á", "Á", "é", "í", "ó", "ú", "ñ", "Ñ", "°", chr(164), chr(165));
	$poner = array("",  "",  "a", "A", "e", "i", "o", "u", "n", "N", " ", "n", "n");

	$str = str_replace($sacar, $poner, $xstr);
	return $str;
}

/*
Retorna el array sin los acentos y ñ
*/
function sinCaracteresEspecialesArray($xaValores)
{
	$sacar = array("á", "Á", "é", "í", "ó", "Ú", "ñ", "Ñ", chr(164), chr(165));
	$poner = array("a", "A", "e", "i", "o", "u", "n", "N", "n", "n");

	$aValores = array();
	foreach ($xaValores as $i => $valor) {
		$aValores[$i] = "";
		if ($valor != null)
			$aValores[$i] = utf8_encode(str_replace($sacar, $poner, $valor));
	}

	return $aValores;
}


/**
 * reemplaza acentos y ñ por valores visibles en html
 * @param string $xstr
 */
function htmlVisible($xstr)
{
	$sacar = array("á", "é", "í", "ó", "ú", "ñ", chr(164), chr(165), "\r\n");
	$poner = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&ntilde;", "&ntilde;", "&ntilde;", "<br>");
	$str = str_replace($sacar, $poner, $xstr);
	return $str;
}


/**
 * No utilizar, devuelve el mismo texto, era para problemas de UTF y ISO...
 */
function pdfVisible($xstr, $xInTable = false)
{
	return $xstr;

	$sacar = array("á", "é", "í", "ó", "ú", "ñ", "Ñ", "\r\n");
	$poner = array("a", "e", "i", "o", "u", "n", "N", "<br>");
	$str = str_replace($sacar, $poner, $xstr);

	//resuelve codificacion
	$str = textToPdfEncoding($str, $xInTable);

	return $str;
}


/**
 * Si el texto exede el largo arma un texto nuevo con los primeros y ultimos caracteres
 * Ej: "hola mundo cruel mundo infame" =(11)=> "hola...fame"
 * @param string $xtext
 * @param int $xlargo
 */
function resumirTexto($xtext, $xlargo)
{
	if (strlen($xtext) <= $xlargo)
		return $xtext;

	return substr($xtext, 0, $xlargo / 2 - 3) . "... " . substr($xtext, strlen($xtext) - $xlargo / 2 + 1, $xlargo / 2);
}


/**
 * Convierte ISO a UTF-8
 */
function toUtf8($xtext)
{
	if ($xtext == null)
		$xtext = "";
	$result = $xtext;
	$code = mb_detect_encoding($xtext, "UTF-8, ISO-8859-1");
	if ($code != "UTF-8")
		$result = iconv($code, "UTF-8", $xtext);

	$isUTF8 = preg_match('//u', $result);
	if (!$isUTF8)
		return "-";

	return $result;
}


/**
 * Convierte el texto a la codificacion del PDF
 * @param string $xtext
 * @return string
 */
function textToPdfEncoding($xtext, $xInTable = false)
{
	$texto = $xtext;

	//cambiado en constructor de Cpdf para que no se cambie a windows-1251
	//PDF usa:     public $targetEncoding = 'ISO-8859-1';

	//echo("<br>" . substr($texto, 0, 10) . ": " . mb_detect_encoding($texto, 'UTF-8, ISO-8859-1'));

	$quitar = array("<br>", "&nbsp;", "<span style=\"font-weight: bold;\">", "</span>", "<span style=\"font-weight: bold; text-decoration: underline;\">", "<div style=\"text-align: right;\">", "</div>", "<style type=\"text/css\">", "body ", "{", "background:", "#FFF;", "}", "</style>", "<span style=\"font-style: italic;\">");
	$poner = array("\n", " ", "<b>", "</b>", "", "", "\n", "", "", "", "", "", "", "", "", "");
	$texto = str_replace($quitar, $poner, $texto);

	//TODO: invocar toUtf8() e ignorar el resto
	//return toUtf8($texto);

	/*
	if ($xInTable)
	*/
	//parece funcionar en PHP5 y 7 (aleluya hermanos !)
	return utf8_encode($texto);



	//detecta si es iso o utf (en esta creo)
	if (sonIguales(mb_detect_encoding($texto, 'UTF-8, ISO-8859-1'), "UTF-8"))
		$texto = mb_convert_encoding($texto, "ISO-8859-1", "UTF-8");

	return $texto;
}


function sinCaracteresEspecialesNiEspacios($xstr)
{
	$arep = array(" ", "--", "(", ")", ":", "[", "]", ".", "/", chr(164), chr(165));
	$ret = sinCaracteresEspeciales(str_replace($arep, "-", $xstr));
	return $ret;
}

/**
 * Igual al wordwrap pero respeta \n ya existentes
 * @return array
 **/
function linewrap($string, $width, $break = '\n', $cut = false)
{
	$array = explode("\n", $string);
	$result = "";
	foreach ($array as $key => $val) {
		$result .= wordwrap($val, $width, $break, $cut);
		$result .= '\n';
	}
	return explode('\n', $result);
}


function doLogin($xUser, $xPass)
{
	if (strcmp($xUser, "") == 0)
		return 0;
	else {
		//Valida psw y setea variables de entorno
		$rsUsuarios = new BDObject();
		$rs = new BDObject();
		$rsUsuarios->execQuery("select * from sc_usuarios where (habilitado > 0) and login like '" . $xUser . "' and clave like md5('" . $xPass . "')");
		$idUser = $rsUsuarios->getValue("id");
		$diaHoy = date("N");
		$hora = date("G");
		if ($rsUsuarios->EOF()) {
			setSession("sc3_logueado", false);
			sleep(2);
			return 0;
		} else {
			$rs->execQuery("SELECT * FROM sc_perfiles p 
									INNER JOIN sc_perfiles_horarios h on (p.id = h.idperfil) 
									INNER JOIN sc_usuarios_perfiles u on (h.idperfil = u.idperfil) 
							WHERE u.idusuario =" . $idUser . " ");
			if (!$rs->EOF()) {
				$rsFecha = new BDObject();

				$rsFecha->execQuery("SELECT p.id, p.hr_fin
									FROM sc_perfiles_horarios as p 
										INNER JOIN sc_perfiles sp ON (p.idperfil = sp.id)
										INNER JOIN sc_usuarios_perfiles up ON (sp.id = up.idperfil)
									WHERE p.dia = $diaHoy and 
										p.hr_inicio <= $hora and 
										p.hr_fin > $hora and 
										up.idusuario = $idUser");

				if ($rsFecha->EOF()) {
					setSession("sc3_logueado", false);
					logOp("LOGIN ERR", "", 0, "Fuera de Horario, IP: " . getRemoteIp() . ", AGENT: " . getUserAgent());
					sleep(1);
					return 0;
				} else {
					$horafinal = $rsFecha->getValue("hr_fin");
					setSession("sc3_hora_final", $horafinal);
				}
				$rsFecha->close();
			}
			setSession("sc3_logueado", true);
			setSession("idusuario-logueado", (int) $rsUsuarios->getValue("id"));
			setSession("login", $xUser);
			setSession("sc3_clave", $xPass);
			setSession("email", $rsUsuarios->getValue("email"));
			setSession("es_root", $rsUsuarios->getValue("esRoot"));
			setSession("idlocalidad", $rsUsuarios->getValueInt("idlocalidad"));
			setSession("idcontacto", $rsUsuarios->getValueInt("idcontacto"));
			setSession("usuario_punto_venta", $rsUsuarios->getValue("punto_venta"));
			setSession("usuario_idlista", $rsUsuarios->getValue("idlista"));

			logOp("LOGIN", "", 0, "IP: " . getRemoteIp() . ", AGENT: " . getUserAgent());

			//borra registros de login de mas de 2 años, borra todo de mas de 5 años
			$rsUsuarios->execQuery("delete from sc_logs 
									where codigo_operacion = 'login' and 
										datediff(current_timestamp(), fecha) > (365*2)");
			$rsUsuarios->execQuery("delete from sc_logs 
									where datediff(current_timestamp(), fecha) > (365*5)");
			return 1;
		}
	}
}

function getCurrentUserLogin()
{
	return getSession("login");
}

function getCurrentUserEmail()
{
	return getSession("email");
}

function esRoot()
{
	return (int) getSession("es_root");
}


/*
Cambia la clave del usuario
*/
function sc3cambiarClave($xclavev, $xclaven, $xclaven2, $xidusuario = 0)
{
	debug("sc3cambiarClave($xclavev, $xclaven, $xclaven2)");

	$sql = "update sc_usuarios";
	$sql .= "   set clave = md5('" . $xclaven . "')";
	$sql .= " where id = $xidusuario";
	//está cambiando clave propia
	if ($xidusuario == getCurrentUser())
		$sql .= "   and clave = md5('" . $xclavev . "')";

	$rs = new BDObject();
	$rs->execQuery($sql);
}


function sc3IsValidPass($xclave)
{
	$login = getCurrentUserLogin();

	$sql = "select *
			from sc_usuarios
			where login = '$login' and habilitado = 1 and 
				clave = md5('$xclave')";

	$rs = new BDObject();
	$rs->execQuery($sql);

	if ($rs->EOF())
		return false;
	return true;
}

function sc3cambiarClave2($xlogin, $xclave)
{
	debug("sc3cambiarClave2($xlogin)");

	$sql = "update sc_usuarios
			set clave = md5('" . $xclave . "')
			where login = '$xlogin'";

	$rs = new BDObject();
	$rs->execQuery($sql);
}

/**
 * Actualiza hr de ultima actividad y retorna arreglo de usuarios activos
 */
function sc3UsuariosActivos()
{
	$sql = "update sc_usuarios
			set ultima_actividad = CURRENT_TIMESTAMP()
			where id = " . getCurrentUser();

	$rs = new BDObject();
	$rs->execQuery($sql);

	$sql = "select login
			from sc_usuarios
			where ultima_actividad is not null and 
				date_add(ultima_actividad, interval 2 MINUTE) > CURRENT_TIMESTAMP()
			order by login";
	$rs->execQuery($sql);

	$aUsr = $rs->getAsArray("");

	$aUsr[] = array('login' => 0);
	if (!esVacio(getSession("sc3_hora_final"))) {
		if (date("G") >= getSession("sc3_hora_final")) {
			header('Location: sc-loginerror.php');
		}
	}

	return $aUsr;
}


/*
* Genera una clave con los 3 primeros del login mas 3 nros al azar
*/
function sc3GenerarClave($xlogin)
{
	debug("sc3GenerarClave($xlogin)");

	$clave = substr($xlogin, 1, 3) . getClave(3);
	return $clave;
}

function esClaveCorrecta($xclavev)
{
	debug("esClaveCorrecta($xclavev)");
	$sql = "select * from sc_usuarios";
	$sql .= " where clave = md5('$xclavev')";
	$sql .= " and id = " . getCurrentUser();
	$rs = new BDObject();
	$rs->execQuery($sql);
	if ($rs->EOF())
		return false;
	return true;
}

/*
indica si la clave es trivial
*/
function esClaveTrivial($xclave)
{
	$triviales = array("123", "1234", "12345", "abc", "abcd", "987", "9876", "123456", "1234567", "321", "4321", "54321", "654321");
	if (strlen($xclave) < 4)
		return true;
	if (in_array($xclave, $triviales))
		return true;
	return false;
}


function getRsUsuariosSistema()
{
	$sql = "select id, nombre, login, email from sc_usuarios
			where  (habilitado > 0)
			order by nombre";

	$rs = new BDObject();
	$rs->execQuery($sql);
	return $rs;
}


function getCurrentUser()
{
	return getSession("idusuario-logueado");
}

function setCurrentUser($xidusuario)
{
	return setSession("idusuario-logueado", $xidusuario);
}

function getCurrentUserLocalidad()
{
	return getSession("idlocalidad");
}

function usuarioLogueado()
{
	if (getSession("sc3_logueado"))
		return 1;
	else
		return 0;
}


function checkUsuarioLogueado()
{
	if (!usuarioLogueado()) {
		header("Location:./sc-loginerror.php");
		exit;
	}
	s15();
}

function checkUsuarioLogueadoRoot()
{
	checkUsuarioLogueado();
	if (!esRoot()) {
		header("Location:./sc-loginerror.php");
		exit;
	}
}


function setMensaje($xmsg, $xacumular = false)
{
	$msg = getSession("SC3-MENSAJE");
	if ($xacumular && !esVacio($msg))
		$xmsg .= "<br>" . $msg;
	setSession("SC3-MENSAJE", $xmsg);
}

function getMensaje()
{
	$msg = getSession("SC3-MENSAJE");
	setMensaje("");
	return $msg;
}

function setWarning($xmsg, $xacumular = false)
{
	$msg = getSession("SC3-WARN");
	if ($xacumular && !esVacio($msg))
		$xmsg .= "<br>" . $msg;
	setSession("SC3-WARN", $xmsg);
}

function getWarning()
{
	$msg = getSession("SC3-WARN");
	setWarning("");
	return $msg;
}


function goOn($xstackname = "")
{
	if (esVacio($xstackname))
		$xstackname = Request("stackname");
	header("Location:./hole.php?stackname=$xstackname");
	exit;
}


function goOnAnterior($xstackname = "")
{
	if (esVacio($xstackname))
		$xstackname = Request("stackname");
	header("Location:./hole.php?anterior=1&stackname=$xstackname");
	exit;
}

/**
 * Va al hole pero con la indicacion de ejecutar una accion
 */
function goOn2($xopid, $xmid, $xstackname = "")
{
	if (esVacio($xstackname))
		$xstackname = Request("stackname");
	header("Location:./hole.php?opid=$xopid&mid=$xmid&stackname=$xstackname");
	exit;
}

/**
 * Va al hole pero tambien muestra un archivo
 */
function goOnShowFile($xfile, $xstackname = "")
{
	if (esVacio($xstackname))
		$xstackname = Request("stackname");
	header("Location:./hole.php?file=$xfile&stackname=$xstackname");
	exit;
}

function goToPage($xpage)
{
	header("Location:./$xpage");
	exit;
}


function goToPage2($xpage)
{
	header("Location:$xpage");
	exit;
}


function goToPageView($xquery, $xid, $xstackname = "")
{
	if (esVacio($xstackname))
		$xstackname = Request("stackname");

	goToPage("sc-viewitem.php?stackname=$xstackname&query=$xquery&registrovalor=$xid");
}


function esNumero($xpalabra)
{
	return (is_numeric($xpalabra) == TRUE);
}

/*
Retorna si tiene formato de fecha dd/mm/aaaa
*/
function esFecha($xpalabra)
{
	$a = explode("/", $xpalabra);
	if (count($a) != 3)
		return false;

	return true;
}



function condicionSql($xfilterField, $xcondicion, $xfilterValue, $xalias = "")
{
	debug("condicionSql($xfilterField, $xcondicion, $xfilterValue, $xalias)");
	if ($xfilterField == "" || $xcondicion == "" || $xfilterValue == "")
		return "";

	//analiza si hay alias para el campo
	if (sonIguales($xalias, ""))
		$result = $xfilterField;
	else
		$result = $xalias . "." . $xfilterField;

	if (esNumero($xfilterValue)) {
		if ($xcondicion == "CON" || $xcondicion == "IGU")
			$result .= " = ";
		if ($xcondicion == "MAY")
			$result .= " > ";
		if ($xcondicion == "MEN")
			$result .= " < ";
		if ($xcondicion == "DIF")
			$result .= " <> ";
		$result .= $xfilterValue;
	} else {
		if (esFecha($xfilterValue))
			$xfilterValue = Sc3FechaUtils::strToFecha($xfilterValue);

		if ($xcondicion == "CON")
			$result .= " like '%" . $xfilterValue . "%'";
		if ($xcondicion == "IGU")
			$result .= " = '" . $xfilterValue . "'";
		if ($xcondicion == "COM")
			$result .= " like '" . $xfilterValue . "%'";
		if ($xcondicion == "MAY")
			$result .= " > '" . $xfilterValue . "'";
		if ($xcondicion == "MEN")
			$result .= " < '" . $xfilterValue . "'";
		if ($xcondicion == "DIF")
			$result .= " <> '" . $xfilterValue . "'";
	}
	return $result;
}

/*
Dado un nombre de tabla, retorna un RS con todos sus registros
*/
function getRsTabla($xtabla, $xfields = "*", $xorder = "")
{
	debug("getRsTabla($xtabla)");
	$sql = "select " . $xfields . " from " . $xtabla;
	if (!sonIguales($xorder, ""))
		$sql .= " order by " . $xorder;
	$rs = new BDObject();
	$rs->execQuery($sql);
	return $rs;
}


/**
 * Dado un sql, retorna un RS con todos sus registros 
 * @param string $xsql
 * @return BDObject
 */
function getRs($xsql, $xsoloAsoc = false)
{
	$rs = new BDObject();
	$rs->execQuery($xsql, false, $xsoloAsoc);
	return $rs;
}


/*
Dado un query Info, arma un sql con la consulta
*/
function locateRecord($xquery_info, $xvalue)
{
	return locateRecordId($xquery_info["table_"], $xvalue, $xquery_info["keyfield_"]);
}

/*
Ubica el registro que lo invoca en una operacion
*/
function locateRecordMaster()
{
	debug("locateRecordMaster()");

	$query = Request("mquery");
	$id = RequestInt("mid");
	$qinfo = getQueryObj($query);
	return locateRecordId($qinfo->getQueryTable(), $id);
}

/*
Retorna el valor de un registro en las tabals, id y campo dados
*/
function sc3getRecordValueInt($xtable, $xid, $xfield)
{
	debug("sc3getRecordValueInt($xtable, $xid, $xfield)");
	$rsrubro = locateRecordId($xtable, $xid);
	return (int) $rsrubro->getValue($xfield);
}


function getArrayGenPersonas()
{
	$values = array();
	$values["fecha_alta"] = "CURRENT_TIMESTAMP()";
	$values["es_cliente"] = 0;
	$values["es_alumno"] = 0;
	$values["es_empleado"] = 0;
	$values["es_proveedor"] = 0;
	$values["es_propietario"] = 0;
	$values["es_juridica"] = 0;
	return $values;
}


/**
 * Enter description here...
 * @param string $xtable
 * @param int $xid
 * @param string $xidfield
 * @return BDObject
 */
function locateRecordId($xtable, $xid, $xidfield = "id")
{
	debug("locateRecordId($xtable, $xid)");
	return locateRecordWhere($xtable, $xidfield . " = " . $xid);
}


/*
Retorna el icono y el nombre de la operacion
*/
function getOpTitle($xidoperacion, $xtitle = "", $xicono = "")
{
	if (sonIguales($xidoperacion, "")) {
		$titulo = $xtitle;
		$icon = $xicono;
		$ayuda = "";
	} else {
		$rs = locateRecordId("sc_operaciones", $xidoperacion);

		$icon = $rs->getValue("icon");
		if ($icon == "")
			$icon = "images/question.gif";
		$ayuda = $rs->getValue("ayuda");
		$titulo = htmlVisible($rs->getValue("nombre"));
		$rs->close();
	}
	$title = "";
	if (!esExcel())
		$title .= img($icon, $ayuda);
	$title .= " " . $titulo;

	if (esVacio(trim($titulo)))
		$title = Request("op_titulo");
	return $title;
}

/**
 * Abrevia un nombre de columna, ej: Retencion_IIBB -> Rete_IIBB
 * @param string $xnombreCol
 */
function sc3NombreColAbreviado($xnombreCol)
{
	$xnombreCol = str_replace(" ", "_", $xnombreCol);
	$aCols = explode("_", $xnombreCol);
	if (count($aCols) == 1)
		return $xnombreCol;

	$ares = array();
	$i = 0;
	while ($i < count($aCols)) {
		$ares[] = substr($aCols[$i], 0, 4);
		$i++;
	}

	return implode("_", $ares);
}


/*
Retorna el icono y el nombre de la operacion
*/
function getOpTitle2($xidoperacion)
{
	$rs = locateRecordId("sc_operaciones", $xidoperacion);
	$titulo = $rs->getValue("nombre");
	return $titulo;
}

/*
Ubica el maximo de una tabla
*/
function findMaxId($xtable, $xidfield = "id")
{
	debug(" findMaxId($xtable)");
	$sql = "select max($xidfield) as maxid from " . $xtable;
	$rs = new BDObject();
	$rs->execQuery($sql);
	return $rs->getValue("maxid");
}

/*
Retorna el id de la operacion que tiene el url dado
*/
function findOperacionId($xurl)
{
	debug("findOperacion($xurl)");
	$sql = "select id from sc_operaciones where url like '$xurl'";
	$rs = new BDObject();
	$rs->execQuery($sql);
	return (int) $rs->getValue("id");
}

/**
 * Ubica un registro con una condicion, retorna RS
 * @param string $xtable
 * @param string $xwhere
 * @return BDObject
 */
function locateRecordWhere($xtable, $xwhere, $xSoloAsoc = false, $xorderby = "")
{
	debug(" locateRecordWhere($xtable, $xwhere)");

	$sql = "select * 
			from $xtable
			where $xwhere";

	if (!esVacio($xorderby))
		$sql .= " order by $xorderby";

	$rs = new BDObject();
	$rs->execQuery($sql, false, $xSoloAsoc);
	return $rs;
}


function getDataAlign($xnombreCampo, $xtipoCampo, $xfields_ref = "", $xvalor = "", $xcolsStyles = "", $index = -1)
{
	if (is_array($xcolsStyles)) {
		if (isset($xcolsStyles[$index]["align"]))
			return $xcolsStyles[$index]["align"];
	}

	//el campo forma parte de un FK
	if (is_array($xfields_ref) && isset($xfields_ref[$xnombreCampo]) && is_array($xfields_ref[$xnombreCampo]))
		return "left";

	if (
		esCampoFecha($xtipoCampo) || esCampoInt($xtipoCampo) || esCampoPorcentaje($xvalor)
		|| esCampoFloat($xtipoCampo) || esCampoConMoneda($xvalor)
	)
		return "right";

	if (esCampoColor($xvalor) || esCampoBoleano($xtipoCampo) || startsWith($xvalor, "pdf:"))
		return "middle";

	return "left";
}

/*
Retorna un arreglo con los campos de la tabla dada. Esquiva el xskip
*/
function getFieldsInArray($xtable, $xskip, $xcant = 100)
{
	debug("getFieldsInArray($xtable, $xskip, $xcant)");
	$str = "select * 
			from $xtable 
			limit 0";

	$rs = new BDObject();
	$rs->execQuery($str);
	$i = 0;
	$fields = array();
	while ($i < $rs->cantF() && ($i < $xcant)) {
		if (strcmp($xskip, $rs->getFieldName($i)) != 0)
			array_push($fields, $rs->getFieldName($i));
		$i++;
	}

	$rs->close();
	return $fields;
}


function pageSize()
{
	$page = requestOrSession("top");
	if ($page == "")
		return 20;
	return $page;
}

//Genera claves alfanumericas.
function randomValue($num)
{
	switch ($num) {
		case "1":
			$rand_value = "a";
			break;
		case "2":
			$rand_value = "b";
			break;
		case "3":
			$rand_value = "c";
			break;
		case "4":
			$rand_value = "d";
			break;
		case "5":
			$rand_value = "e";
			break;
		case "6":
			$rand_value = "f";
			break;
		case "7":
			$rand_value = "g";
			break;
		case "8":
			$rand_value = "h";
			break;
		case "9":
			$rand_value = "i";
			break;
		case "10":
			$rand_value = "j";
			break;
		case "11":
			$rand_value = "k";
			break;
		case "12":
			$rand_value = "k";
			break;
		case "13":
			$rand_value = "m";
			break;
		case "14":
			$rand_value = "n";
			break;
		case "15":
			$rand_value = "p";
			break;
		case "16":
			$rand_value = "p";
			break;
		case "17":
			$rand_value = "q";
			break;
		case "18":
			$rand_value = "r";
			break;
		case "19":
			$rand_value = "s";
			break;
		case "20":
			$rand_value = "t";
			break;
		case "21":
			$rand_value = "u";
			break;
		case "22":
			$rand_value = "v";
			break;
		case "23":
			$rand_value = "w";
			break;
		case "24":
			$rand_value = "x";
			break;
		case "25":
			$rand_value = "y";
			break;
		case "26":
			$rand_value = "z";
			break;
		case "27":
			$rand_value = "2";
			break;
		case "28":
			$rand_value = "5";
			break;
		case "29":
			$rand_value = "2";
			break;
		case "30":
			$rand_value = "3";
			break;
		case "31":
			$rand_value = "4";
			break;
		case "32":
			$rand_value = "5";
			break;
		case "33":
			$rand_value = "6";
			break;
		case "34":
			$rand_value = "7";
			break;
		case "35":
			$rand_value = "8";
			break;
		case "36":
			$rand_value = "9";
			break;
	}
	return $rand_value;
}

//Generador de Claves alfanumericas
function getClave($valor = 20)
{
	$num = '4';
	randomValue($num);
	if ($valor > 0) {
		$rand_id = "";
		for ($i = 1; $i <= $valor; $i++) {
			mt_srand((float)microtime() * 1000000);
			$num = mt_rand(1, 36);
			$rand_id .= randomValue($num);
		}
	}
	return $rand_id;
}


/*
Dado un str cualquiera, lo guarda en la sesion y retorna un key unico
Arma un hash por el string dado y lo guarda. De esta forma, intenta reutilizar los keys
*/
function saveSessionStr($xstr, $xprefix = "_sql", $xforceUnique = false)
{
	$astr = explode(" ", $xstr);
	$hash = count($astr);

	if ($xforceUnique)
		$key = $xprefix . getClave(4) . $hash;
	else
		$key = $xprefix . $hash;

	$_SESSION[$key] = $xstr;
	return $key;
}

/*
Dado un key unico, recupera de la session su valor
*/
function getSessionStr($xkey)
{
	return getSession($xkey);
}

/**
 * Retorna si dos textos son iguales
 */
function sonIguales($xs1, $xs2)
{
	if (is_array($xs1))
		return false;

	if ($xs1 == null) {
		$xs1 = "";
	}
	if (strcmp($xs1, $xs2) == 0)
		return true;
	return false;
}

function esVacio($xstr)
{
	return sonIguales($xstr, "");
}

/**
 * Retorna null si es una cadena vacia, �til para mandar un entero a la base de datos
 * @param string $xstr
 * @return string
 */
function ifemptyNull($xstr)
{
	if (esVacio($xstr))
		return "null";

	return $xstr;
}

/**
 * Retorna si se encuentra el string buscar en total
 *
 * @param string $xtotal
 * @param string $xbuscar
 * @return boolean
 */
function strContiene($xtotal, $xbuscar)
{
	if (esVacio($xbuscar))
		return false;
	if (strpos($xtotal, $xbuscar) === false)
		return false;
	return true;
}

/**
 * Comienza con !
 * @param string $string el total
 */
function startsWith($string, $char)
{
	$string = trim($string);
	$length = strlen($char);
	return (substr($string, 0, $length) === $char);
}

function endsWith($string, $char)
{
	$string = trim($string);
	$length = strlen($char);
	$start =  $length * -1;
	return (substr($string, $start, $length) === $char);
}


function getImagesPath()
{
	global $UPLOAD_PATH_SHORT;
	return $UPLOAD_PATH_SHORT . "/";
}


function s15()
{
	global $BD_DATABASE;
	$p = md5("" . $_SERVER['SERVER_NAME']);
	$v = md5($BD_DATABASE);
	if (!sonIguales(getParameter($p, ""), $v)) {
		gotoPage("./sc-error.php?code=lic");
		return false;
	}
	return true;
}

/**
 * Recupera el parametro o retorna el valor default
 * @param $xnombre string
 * @param $xvalor string valor por defecto
 */
function getParameter($xnombre, $xvalor)
{
	if (!isset($_SESSION[$xnombre])) {
		$retVal = $xvalor;
		$rsParams = new BDObject();
		$rsParams->execQuery("select valor 
							from sc_parametros 
							where nombre like '" . $xnombre . "'");
		if (!$rsParams->EOF())
			$retVal = $rsParams->getValue("valor");
		else {
			$rsParams->execQuery("insert into sc_parametros(nombre, valor) 
								values('" . $xnombre . "', '" . $xvalor . "')");
		}
		$rsParams->close();
		setSession($xnombre, $retVal);
	}
	return getSession($xnombre);
}

/**
 * Guarda el parametro si es necesario
 */
function saveParameter($xnombre, $xvalor)
{
	$result = getParameter($xnombre, $xvalor);
	if (!sonIguales($xvalor, $result)) {
		$rsParams = new BDObject();
		$rsParams->execQuery("update sc_parametros 
							set valor = '$xvalor' 
							where nombre like '$xnombre'");
		setSession($xnombre, $xvalor);
		$result = $xvalor;
		$rsParams->close();
	}
	return $result;
}

/*
Retorna el valor del parametro convertido a INT, si es invalido retorna un CERO
*/
function getParameterInt($xnombre, $xvalor)
{
	return intval(getParameter($xnombre, $xvalor));
}


//Intenta resolver la clave extranjera
function getFKValueWithURL($xidquery, $xcampo, $xvalor)
{
	debug("getFKValueWithURL($xidquery, $xcampo, $xvalor)");
	$rsRefInfo = new BDObject();
	$rsRefInfo->execQuery("SELECT sc_referencias.*, sc_querys.* FROM sc_referencias LEFT JOIN sc_querys ON sc_referencias.idquery = sc_querys.idquery where sc_referencias.idquerymaster=" . $xidquery . " and campo_ like '" . $xcampo . "'");

	$retVal = $xvalor;
	if (!$rsRefInfo->EOF()) {
		$rsFKInfo = new BDObject();
		$rsFKInfo->execQuery("select " .  $rsRefInfo->getValue("combofield_") . " from " .  $rsRefInfo->getValue("table_") . " where " .  $rsRefInfo->getValue("keyfield_") . "=" . $xvalor);
		if ($rsFKInfo->EOF())
			$retVal = $xvalor . " (FKE)";
		else {
			$retVal = $xvalor . "-" . $rsFKInfo->getValue($rsRefInfo->getValue("combofield_"));
		}
	}
	return $retVal;
}

/**
 * REtorna el nombre del preview del mapa según el lat/lon dado
 * @param decimal $xlat
 * @param decimal $xlon
 * @param string $xqueryName
 * @param int $xid
 */
function sc3NombreMapaGooglePoint($xlat, $xlon, $xqueryName, $xid)
{
	$latlon = $xlat . "," . $xlon;
	$latlon2 = str_replace("-", "m", $latlon);
	$latlon2 = str_replace(",", "-", $latlon2);
	$latlon2 = str_replace(".", "p", $latlon2);

	$nombreMapa = getImagesPath() . "mapa-" . $xqueryName . "-$xid-" . $latlon2 . ".png";
	return $nombreMapa;
}

/**
 * Retorna nombre de mapa del punto dadoo. Lo crea y copia de google en carpeta de archivos
 * @param decimal $xlat
 * @param decimal $xlon
 * @param string $xqueryName
 * @param int $xid
 */
function sc3MapaGooglePoint($xlat, $xlon, $xqueryName, $xid)
{
	if ($xlat == 0 || $xlon == 0 || $xid == 0)
		return "";

	$nombreMapa = sc3NombreMapaGooglePoint($xlat, $xlon, $xqueryName, $xid);
	if (!file_exists($nombreMapa)) {
		$latlon = $xlat . "," . $xlon;
		$mapSize = getParameter("sc3-map-preview-size", "300x300");
		$mapZoom = getParameter("sc3-map-preview-zoom", "16");

		$img_url = "http://maps.googleapis.com/maps/api/staticmap?center=" . $latlon . "&zoom=$mapZoom&size=$mapSize&sensor=true";
		$img_url .= "&markers=color:red%7Ccolor:red%7Clabel:P%7C" . $latlon;
		$errorR = error_reporting(0);
		copy($img_url, $nombreMapa);
		error_reporting($errorR);
	}

	if (!file_exists($nombreMapa))
		return "";
	return $nombreMapa;
}

/*
Retrna un campo para la edicion del punto lat/long
*/
function googlePointField($xnombrecampo, $xvalor, $xfield_def, $xid = 0, $xqueryName = "", $xlongitud = 0)
{
	$input = new HtmlInputText($xnombrecampo, $xvalor);
	// 8 decimales es inaf !
	$input->setTypeFloat(8);
	$input->dontShowCalculator();

	$res = "";

	//busca imagen ya grabada del punto en cuestion
	if ($xid != 0 && $xlongitud != 0) {
		$nombreMapa = sc3MapaGooglePoint($xvalor, $xlongitud, $xqueryName, $xid);
		if (!esVacio($nombreMapa)) {
			$sizeImg = getParameter("sc3-map-img-size", "100");
			$res = href(img($nombreMapa, $nombreMapa, $sizeImg), $nombreMapa, "mapas") . "<br>";
		}
	}

	$gmapsSelector = getParameter("gmap-selector", "http://localhost/gmaps/gmap-selector.php");
	$url = "javascript:openWindowGmaps('" . $gmapsSelector . "?idlat=" . $xnombrecampo . "&idlng=longitud', 'gmaps')";
	$href = href(img("images/mundo.gif", "Ubicar punto geografico"), $url);

	$result = $res;
	$result .= "\n<table width=\"100%\"><tr>";
	$result .= "<td width=\"40%\">";
	$result .= $input->toHtml();
	$result .= "</td>";
	$result .= "<td width=\"60%\" align=\"left\">";
	$result .= $href;
	$result .= "</td>";
	$result .= "</tr></table>";
	return $result;
}

/*
Retorna un combo con la info dada, pero si es autogesti�n aplica filtro de idusuario
*/
function getComboWithInfo($xCampoID, $xTabla, $xId, $xCampoVisible, $xwhere, $xwhereeval, $xselected, $xautogestion, $xis_required)
{
	debug("getComboWithInfo($xCampoID, $xTabla, $xId, $xCampoVisible, $xwhere, $xwhereeval, $xselected, $xautogestion, $xis_required)");
	$str = "select " . $xId . ", " . $xCampoVisible . " from " . $xTabla;
	if ((strcmp($xwhere, "") != 0) || (strcmp($xwhereeval, "") != 0)) {
		$str .= " where " . $xwhere;
		if (strcmp($xwhereeval, "") != 0)
			$str .= " " . eval($xwhereeval);
	}
	$str .= " order by " . $xCampoVisible;
	$rsPpal0 = new BDObject();
	$rsPpal0->execQuery($str);
	if ($xis_required == "1")
		$retVal = getComboResultSet($rsPpal0, $xCampoID, $xId, $xCampoVisible, $xselected);
	else
		$retVal = getComboResultSetWithNnull($rsPpal0, $xCampoID, $xId, $xCampoVisible, $xselected);
	return $retVal;
}


/*
Arma un prfijo para que no choquen los nombres de las im�genes.
Utiliza la fecha.
*/
function getPrefijoImg()
{
	$dia = getdate(time());
	$prefijoImg = "f" . $dia["year"] . "" . $dia["mon"] . "-" . $dia["mday"];
	return $prefijoImg;
}


if (!function_exists('array_intersect_key')) {
	function array_intersect_key($isec, $keys)
	{
		$res = array();
		foreach (array_keys($isec) as $key) {
			if (isset($keys[$key])) {
				$res[$key] = $isec[$key];
			}
		}
		return $res;
	}
}


function getRsLocalidades()
{
	$sql = "select l.id, l.nombre, concat(p.nombre, ' - ', l.nombre) as nombre2
			from bp_localidades l
				left join bp_provincias p on (l.idprovincia = p.id)
			order by l.nombre
			limit 1000";

	$rs = new BDObject();
	$rs->execQuery($sql);
	return $rs;
}


function completarCerosIzq($xstr, $xcant)
{
	return str_pad($xstr, $xcant, "0", STR_PAD_LEFT);
}

function completarCerosDer($xstr, $xcant)
{
	return str_pad($xstr, $xcant, "0", STR_PAD_RIGHT);
}




/**
 * Busca si la fecha está en bp_feriados
 * @param datetime $xfecha
 */
function esFeriado($xfecha)
{
	$sql = "select * 
			from bp_feriados
			where fecha_feriado = '$xfecha'";
	$rs = new BDObject();
	$rs->execQuery($sql);
	return !$rs->EOF();
}


/**
 * Arma un Fav .ico y lo guarda en /ico
 * @param string $xicon Path a png/jpg
 * @param bool $xasumeExists Cuando es TRUE no verifica existencia
 * @return string
 */
function favIconBuild($xicon, $xasumeExists = false)
{
	$favicon = "";
	if (!esVacio($xicon)) {
		if (!file_exists("./ico/"))
			mkdir("./ico");

		$path_parts = pathinfo($xicon);
		$favicon = "./ico/" . $path_parts['filename'] . ".ico";
		if (!$xasumeExists) {
			if (!file_exists($favicon)) {
				require_once "sc-ico.php";
				$ico_lib = new PHP_ICO($xicon, array(array(16, 16)));
				$ico_lib->save_ico($favicon);
			}
		}

		$favicon = substr($favicon, 2);
	}
	return $favicon;
}


/**
 * Retorna si tiene la forma "$ 3.34"
 *
 * @param string $xvalor
 * @return boolean
 */
function esCampoConMoneda($xvalor)
{
	//contempla que tenga la moneda
	//$ 24,482,210.00
	if (strlen($xvalor) > 16)
		return false;

	$valores = explode(" ", $xvalor);
	if ((count($valores) == 2) && strContiene($valores[0], "$"))
		return true;
	return false;
}

/**
 * Retorna si tiene la forma "#123456"
 *
 * @param string $xvalor
 * @return boolean
 */
function esCampoColor($xvalor)
{
	//contempla que tenga # al inicio
	if (strContiene($xvalor, "#") && (strlen($xvalor) == 7))
		return true;
	return false;
}


/**
 * Retorna si tiene la forma "17.3%"
 *
 * @param string $xvalor
 * @return boolean
 */
function esCampoPorcentaje($xvalor)
{
	//contempla que tenga el signo %
	$valores = explode("%", $xvalor);
	if ((count($valores) == 2) && esVacio($valores[1]) && (strlen($xvalor) <= 6))
		return true;
	return false;
}

/**
 * De un valor "$ 45.34" obtiene el valor float (para operar)
 *
 * @param string $xvalor
 * @return float
 */
function splitValorConMoneda($xvalor)
{
	if (esVacio($xvalor))
		return  0.00;

	if (esCampoConMoneda($xvalor)) {
		$separadorMiles = getParameter("sc3-separador-miles", ",");
		$valores = explode(" ", $xvalor);
		return str_replace($separadorMiles, "", $valores[1]) * 1.00;
	} else
		return $xvalor;
}

/**
 * Retorna un float con dos decimales
 * @param float $xvalue
 */
function formatFloat($xvalue, $xdecimales = 2, $xsepMilesVacio = 0, $xaPdf = 0)
{
	if (strcmp($xvalue, "") == 0)
		return "";
	$result = "";

	$moneda = "";
	//contempla que tenga la moneda
	$valores = explode(" ", $xvalue);
	if (count($valores) > 1) {
		$moneda = $valores[0] . " ";
		$valorFloat = $valores[1];
	} else
		$valorFloat = $valores[0];

	//parametros generales o toma los del usuario
	$separadorMiles = getParameter("sc3-separador-miles", ",");
	$separadorDecimales = getParameter("sc3-separador-decimales", ".");

	$separadorMiles = getParameter("sc3-separador-miles-" . getCurrentUser(), $separadorMiles);
	$separadorDecimales = getParameter("sc3-separador-decimales-" . getCurrentUser(), $separadorDecimales);
	$notacionArgentina = getParameter("sc3-notacion-argentina-pdf", 0);

	if ($xsepMilesVacio == 1) {
		$separadorMiles = "";
	}

	if (($xaPdf == 1) && ($notacionArgentina == 1)) {
		$separadorMiles = ".";
		$separadorDecimales = ",";
	}

	if (esExcel())
		$result = number_format($valorFloat, $xdecimales, ',', '');
	else {
		if (is_numeric($valorFloat))
			$result = number_format($valorFloat, $xdecimales, $separadorDecimales, $separadorMiles);
		else
			return $xvalue;
	}

	if (sonIguales($result, "-0" . $separadorDecimales . "00"))
		$result = "0" . $separadorDecimales . "00";

	return $moneda . $result;
}

/**
 * Formatea un valor decimal sin decimales, EJ: 100.67 (ancho 10) => 0000010067
 * @param float $xvalor
 * @param int $xancho
 * @return string
 */
function formatFloatArchivo($xvalor, $xancho, $xSepDecimal = "", $xDecimales = 2, $xcompletarCon = "0")
{
	$ret = "";
	$mult = 100;
	if ($xDecimales == 3)
		$mult = 1000;
	if ($xDecimales == 0)
		$mult = 1;

	if (esVacio($xSepDecimal)) {
		if ($xvalor < 0)
			$ret = "-" . str_pad("" . abs($xvalor * $mult), $xancho - 1, $xcompletarCon, STR_PAD_LEFT);
		else
			$ret = str_pad("" . ($xvalor * $mult), $xancho, $xcompletarCon, STR_PAD_LEFT);
	} else {
		$valor = str_pad("" . ($xvalor * 100), $xancho, $xcompletarCon, STR_PAD_LEFT);
		$ret = substr($valor, 1, $xancho - 3) . $xSepDecimal . substr($valor, $xancho - 2, 2);
	}

	return $ret;
}

/**
 * Un texto va al archio con left y convertido
 * Trunca y llena de espacios a derecha
 */
function formatStringArchivo($xvalor, $xlargo, $xcompletarCon = " ")
{
	$xvalor = iconv("UTF-8", "WINDOWS-1252", sinCaracteresEspeciales($xvalor));
	return str_pad(substr($xvalor, 0, $xlargo), $xlargo, $xcompletarCon);
}


function saveArrayAsFile($xfilename, $xarray)
{
	$fp = fopen($xfilename, 'w');

	foreach ($xarray as $i => $valor) {
		fwrite($fp, $valor . "\r\n");
	}

	fclose($fp);
}


/**
 * Redondeo para precios, arriba de 0.3 redondea para arriba
 * @param float $number
 * @param float $redondeo
 * @return float
 */
function roundTo($number, $redondeo)
{
	if ($redondeo == 0)
		return $number;
	$dif = $redondeo / 3;
	return round(($number + $dif) / $redondeo, 0) * $redondeo;
}

/**
 * Aplica redondeo matemático estandar
 * @param float $number
 * @param float $redondeo
 * @return float
 */
function roundToStandar($number, $redondeo)
{
	if ($redondeo == 0)
		return $number;
	return round($number / $redondeo, 0) * $redondeo;
}


function valorConMonedaEnTabla($xmoneda, $xvalor)
{
	if (sonIguales($xmoneda, ""))
		return $xvalor;

	return $xmoneda . $xvalor;

	//TODO: esta tabla le pone bordes !
	$result = "<table cellspacing=\"0\" border=\"0\"><tr>";
	$result .= "<td align=\"right\">";
	$result .= trim($xmoneda);
	$result .= "</td>";
	$result .= "<td align=\"right\">";
	$result .= $xvalor;
	$result .= "</td>";
	$result .= "</tr></table>";

	return $result;
}

/*
Formatea un float pero lo pone rojo si es negativo
*/
function formatFloatRed($xvalue, $xdecimales = 2)
{
	if (sonIguales($xvalue, ""))
		return espacio();

	$moneda = "";
	//contempla que tenga la moneda
	$valores = explode(" ", $xvalue);
	if (count($valores) > 1) {
		$moneda = $valores[0] . " ";
		$valorFloat = $valores[1];
	} else
		$valorFloat = $valores[0];

	if ($valorFloat < 0)
		$res = "<font color=\"red\">" . valorConMonedaEnTabla($moneda, formatFloat($valorFloat, $xdecimales)) . "</font>";
	else
		$res = valorConMonedaEnTabla($moneda, formatFloat($valorFloat, $xdecimales));
	return $res;
}

/*
Retorna: $ 300
*/
function sayMoney($xmoneda, $xmonto)
{
	return $xmoneda . " " . formatFloat($xmonto);
}

/**
 * Retorna 
 * sayMoneyWords("pesos", 456.78) -> pesos cuatrocientos cincuenta y seis con setenta y ocho centavos
 **/
function sayMoneyWords($xmoneda, $xmonto)
{
	$str = $xmoneda . " ";

	$xmonto = (float) $xmonto + 0.009;
	$Numero = intval($xmonto);
	$Decimales = $xmonto - intval($xmonto);
	$Decimales = $Decimales * 100;
	$Decimales = intval($Decimales);
	$str .= NumerosALetras($Numero);
	if ($Decimales > 0) {
		$str .= " con ";
		$str .= NumerosALetras($Decimales);
		$str .= " centavos";
	}

	return $str;
}


/**
 * Crea una version reducida de la imagen dada
 * resizeImg("img", "theFileName.jpg", 100);
 */
function resizeImg($imageDirectory, $imageName, $thumbWidth, $xresize = 1, $xpath = "", $xVerbose = false)
{
	$imageExt = strtolower($imageName);
	$pos = strpos($imageExt, ".jpg");

	if (!sonIguales($xpath, ""))
		$xpath = "/" . $xpath;

	$format = "";
	if ($pos > 0 || (strpos($imageExt, ".jpeg") > 0)) {
		$format = "JPG";
		$srcImg = imagecreatefromjpeg("$imageDirectory$xpath/$imageName");
	} else {
		$pos = strpos($imageExt, ".gif");
		if ($pos > 0) {
			$format = "GIF";
			$srcImg = imagecreatefromgif("$imageDirectory$xpath/$imageName");
		} else {
			$pos = strpos($imageExt, ".png");
			if ($pos > 0) {
				$format = "PNG";
				$srcImg = imagecreatefrompng("$imageDirectory$xpath/$imageName");
			} else {
				$pos = strpos($imageExt, ".exe");
				if ($pos > 0) {
					$format = "EXE";
					unlink("$imageDirectory$xpath/$imageName");
					return FALSE;
				}
			}
		}
	}

	$oldName = $imageName;

	//Analiza si es un archivo que no es imagen
	if ($format == "") {
		if ($xVerbose)
			echo ("por renombrar $imageDirectory$xpath/$oldName por $imageDirectory$xpath/$imageName<br />");
		//		rename("$imageDirectory$xpath/$oldName", "$imageDirectory$xpath/$imageName");
	} else {
		$origWidth = imagesx($srcImg);
		$origHeight = imagesy($srcImg);
		//analiza si es necesario el resize
		if (($origWidth < $thumbWidth) || ($xresize < 1)) {
			//deja mismo ancho, para que no la redimensione
			$thumbWidth = $origWidth;
			$thumbHeight = $origHeight;
			rename("$imageDirectory$xpath/$oldName", "$imageDirectory$xpath/$imageName");
		} else {
			//caso final, es formato conocido, lo redimensiona
			$ratio = $origWidth / $thumbWidth;
			$thumbHeight = intval($origHeight / $ratio);

			$thumbImg = ImageCreateTrueColor($thumbWidth, $thumbHeight);
			if ($thumbImg === FALSE) {
				echo ("ERROR al crear archivo con ImageCreateTrueColor(), GD requerido !!<br>");
				return FALSE;
			}

			$filedCopied = imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, imagesx($thumbImg), imagesy($thumbImg), $origWidth, $origHeight);
			if ($filedCopied === FALSE) {
				echo ("ERROR copiando archivo nuevo (imagecopyresampled())...<br>");
				return FALSE;
			}

			if ($xVerbose)
				echo ("por crear $imageDirectory$xpath/$imageName...<br>");
			if ($format == "JPG")
				$filedCreated = imagejpeg($thumbImg, "$imageDirectory$xpath/$imageName");
			else if ($format == "GIF")
				$filedCreated = imagegif($thumbImg, "$imageDirectory$xpath/$imageName");
			else if ($format == "PNG")
				$filedCreated = imagepng($thumbImg, "$imageDirectory$xpath/$imageName");
			else
				echo ("formato desconocido: $format<br>");

			if ($filedCreated === FALSE) {
				echo ("ERROR creando archivo de destino $format...<br>");
				return FALSE;
			}

			if ($xVerbose) {
				echo ("Grabada con el nombre $imageDirectory<b>$xpath/$imageName</b><br>");
				echo ("por borrar $imageDirectory$xpath/$oldName<br>");
			}

			if (!sonIguales($imageName, $oldName))
				unlink("$imageDirectory$xpath/$oldName");
		}
	}
	return TRUE;
}


/**
 * Crea una version reducida (si no existe ya) de la imagen dada
 * Y la guarda con el mismo nombre + "-small"
 * @return string Retorna el nombre del archivo
 */
function sc3getImgSmall($imageDirectory, $imageName, $thumbWidth = 80, $xcompact = false)
{
	$postfix = "-x80";
	$imageExt = strtolower($imageName);
	$fileParts = explode(".", $imageExt);

	//si no usa thumbs, retorna la misma IMG 
	if (getParameterInt("usar-thumbs", "1") && (count($fileParts) > 1))
		$fileSmall = "$imageDirectory" . $fileParts[0] . $postfix . "." . $fileParts[1];
	else
		$fileSmall = $imageDirectory . $imageName;

	//analiza si existe el archivo (evita warnings)	
	if (!file_exists("$imageDirectory/$imageName")) {
		if (strContiene($imageName, "youtube")) {
			$texto = substr($imageName, -11);
			return linkImgFa($imageName, "fa-youtube", $texto, "fa-2x verde", "yotu", "");
		}

		return "<img src=\"images/nofoto.jpg\" border=\"0\" alt=\"Archivo $imageName no encontrado\"  title=\"Archivo $imageName no encontrado\" width=\"$thumbWidth\">";
	}

	$pos = strpos($imageExt, ".svg");
	if ($pos > 0) {
		return "<img src=\"$imageDirectory/$imageName\" border=\"0\" title=\"$imageName\" width=\"$thumbWidth\">";
	}
	//analiza si ya fue creada la version x80	
	if (!file_exists($fileSmall)) {
		$pos = strpos($imageExt, ".jpg");
		$pos2 = strpos($imageExt, ".jpeg");
		$format = "";
		if (($pos + $pos2) > 0) {
			$format = "JPG";
			$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
		} else {
			$pos = strpos($imageExt, ".gif");
			if ($pos > 0) {
				echo ("Imagen .GIF<br>");
				$srcImg = imagecreatefromgif("$imageDirectory/$imageName");
			} else {
				$pos = strpos($imageExt, ".png");
				if ($pos > 0) {
					$format = "PNG";
					$srcImg = imagecreatefrompng("$imageDirectory/$imageName");
				} elseif (($pos = strpos($imageExt, ".bmp")) > 0) {
					$format = "BMP";
					$srcImg = imagecreatefrombmp("$imageDirectory/$imageName");
				} else {
					$file = new HtmlInputFile("", $imageName);
					$file->setWidth($thumbWidth);
					$file->setReadOnly(true);
					if ($xcompact)
						$file->setCompact();
					return $file->toHtml();
				}
			}
		}


		$origWidth = imagesx($srcImg);
		$origHeight = imagesy($srcImg);

		//analiza si es necesario el resize
		if ($origWidth < $thumbWidth) {
			//deja mismo ancho, para que no la redimensione
			$thumbWidth = $origWidth;
			$thumbHeight = $origHeight;
			copy("$imageDirectory/$imageName", $fileSmall);
		} else {
			//caso final, es formato conocido, lo redimensiona
			$ratio = $origWidth / $thumbWidth;
			$thumbHeight = intval($origHeight / $ratio);

			$thumbImg = ImageCreateTrueColor($thumbWidth, $thumbHeight);
			imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, imagesx($thumbImg), imagesy($thumbImg), $origWidth, $origHeight);

			if ($format == "JPG")
				imagejpeg($thumbImg, $fileSmall);
			if ($format == "GIF")
				imagegif($thumbImg, $fileSmall);
			if ($format == "PNG")
				imagepng($thumbImg, $fileSmall);
			if ($format == "BMP") {
				$fileSmall = str_replace(".bmp", ".jpeg", $fileSmall);
				imagejpeg($thumbImg, $fileSmall);
			}
		}
	}

	return "<img src=\"" . $fileSmall . "\" border=\"0\" alt=\"" . $fileSmall . "\" width=\"$thumbWidth\">";
}


/**
 * Retorna el RS con la persona=empresa
 * Opcional el parametro de idempresa
 * @return BDObject
 **/
function getRsEmpresa($xidempresa = 0)
{
	if ($xidempresa == 0) {
		$strSql = "select p.*, 
						iva.descripcion as condicion_iva,
						iva.codigo as codigo_iva,
						loc.nombre as localidad,
						prov.nombre as provincia

					from gen_personas p
						left join gen_tipos_iva iva on (p.id_tipo_iva = iva.id)
						left join bp_localidades loc on (p.idlocalidad = loc.id) 
						left join bp_provincias prov on (loc.idprovincia = prov.id) ";

		$strSql .= " where p.id= ";
		$strSql .= getParameter("idpersonaempresa", "1");
	} else {
		$strSql = "select emp.*,
						iva.descripcion as condicion_iva,
						iva.codigo as codigo_iva,
						loc.nombre as localidad,
						prov.nombre as provincia

					from cja2_empresas emp
						left join gen_tipos_iva iva on (emp.id_tipo_iva = iva.id)
						left join bp_localidades loc on (emp.idlocalidad = loc.id) 
						left join bp_provincias prov on (loc.idprovincia = prov.id) ";

		$strSql .= " where emp.id = $xidempresa";
	}

	$rs = new BDObject();
	$rs->execQuery($strSql);
	return $rs;
}


/**
 * Retorna la empresa de la tabla cja2_empresas
 * @param int $xidempresa
 * @return BDObject
 */
function getRsEmpresaId($xidempresa)
{
	$strSql = "select e.*,
					iva.descripcion as condicion_iva,
					loc.nombre as localidad,
					prov.nombre as provincia
			
				from cja2_empresas e
					left join gen_tipos_iva iva on (e.id_tipo_iva = iva.id)
					left join bp_localidades loc on (e.idlocalidad = loc.id)
					left join bp_provincias prov on (loc.idprovincia = prov.id) 

				where e.id = $xidempresa";

	$rs = new BDObject();
	$rs->execQuery($strSql);
	return $rs;
}


/*
Retorna el RS con los datos completos de la persona
*/
function getRsDatosPersona($xid)
{
	$strSql = "select p.*, 
					iva.descripcion as condicion_iva,
					loc.nombre as localidad,
					prov.nombre as provincia
			
				from gen_personas p
					left join gen_tipos_iva iva on (p.id_tipo_iva = iva.id)
					left join bp_localidades loc on (p.idlocalidad = loc.id) 
					left join bp_provincias prov on (loc.idprovincia = prov.id)";

	$strSql .= " where p.id= $xid";

	$rs = new BDObject();
	$rs->execQuery($strSql);
	return $rs;
}




function logOp($xcodigo_operacion, $xobjeto_operado, $xid_operado, $xdescripcion)
{
	debug("logOp($xcodigo_operacion, $xobjeto_operado, $xid_operado, $xdescripcion)");

	$idusuario = getCurrentUser();
	if (esVacio($idusuario))
		$idusuario = 1;
	$sql = "insert into sc_logs(idusuario, fecha, codigo_operacion, objeto_operado, id_operado, descripcion)";

	$sql .= " values(";
	$sql .= $idusuario . ", CURRENT_TIMESTAMP(), '" . $xcodigo_operacion . "', '" . $xobjeto_operado . "', " . $xid_operado . ", '" . $xdescripcion . "')";
	$rs = new BDObject();
	$rs->execQuery($sql);
	$rs->close();
}

/**
 * Actualiza el checksum de la tabla (+1)
 * Si viene * se actualizan todas las tablas (utilizado luego de actualizar version)
 */
function sc3UpdateTableChecksum($xtabla, $xbd = null)
{
	$sql = "update sc_querys 
			set table_checksum = ifnull(table_checksum, 0) + 1
			where (table_ = '$xtabla' or '$xtabla' = '*')";

	if ($xbd == null)
		$xbd = new BDObject();
	$xbd->execQuery($sql);
}


function botonToolbar($xoperacion, $xmquery, $xmid, $record)
{
	debug("botonToolbar($xmquery, $xmid)");

	$url = new HtmlUrl($xoperacion["url"]);
	$url->add("mquery", $xmquery);
	$url->add("mid", $xmid);
	$url->add("opid", $xoperacion["id"]);

	$target = $xoperacion["target"];
	if (strcmp($target, "") != 0)
		$target = " target=\"" . $target . "\" ";
	$result = "\n<td width=\"50\" align=\"center\" class=\"td_toolbar\">";
	$condicion = $xoperacion["condicion"];
	if (strcmp($condicion, "") == 0)
		$condicion = true;
	else
		eval($condicion);
	if ($condicion) {
		$result .= "<a href=\"" . $url->toUrl();
		$result .= "\"";
		$result .= $target;
		$result .= ">";
	}
	$result .= img($xoperacion["icon"], $xoperacion["ayuda"]);
	$result .= "<br />";
	$result .= $xoperacion["nombre"];
	if ($condicion)
		$result .= "</a>";
	$result .= "</td>";
	return $result;
}

/*
reemplaza +/= para evitar problemas en urls
*/
function base64_encode_safe($input)
{
	return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_decode_safe($input)
{
	return base64_decode(strtr($input, '-_,', '+/='));
}


/**
 * Carga los modulos app-xxx.php que existan en el dir actual, ej: app-eco.php, app-obr.app
 */
function sc3LoadModules()
{
	debug("sc3LoadModules()");

	foreach (glob("./app-*.php") as $filename) {
		if ((strlen($filename) < 15) && !sonIguales($filename, "./app-cja.php") && !sonIguales($filename, "./app-test.php")) {
			//echo("<br>analizando $filename... ");
			include_once($filename);
		}
	}
}


function implode_array($glue, $separator, $array)
{
	if (!is_array($array)) return $array;
	$string = array();
	foreach ($array as $key => $val) {
		if (is_array($val))
			$val = implode(',', $val);
		$string[] = "{$key}{$glue}{$val}";
	}
	return implode($separator, $string);
}


/**
 * Desarma string: 
	query=qagenda&fstack=1&todesktop=1
en el arreglo:
	Array
	(
		[query] => qagenda
		[fstack] => 1
		[todesktop] => 1
	)
 **/
function explode2Niveles($xstr, $xdelimitador1, $xdelimitador2)
{
	$aresult = array();

	$explode1 = explode($xdelimitador1, $xstr);
	foreach ($explode1 as $str) {
		$explode2 = explode($xdelimitador2, $str);
		$aresult[trim($explode2[0])] = $explode2[1];
	}

	return $aresult;
}



function utf8ize($mixed)
{
	if (is_array($mixed)) {
		foreach ($mixed as $key => $value) {
			$mixed[$key] = utf8ize($value);
		}
	} elseif (is_string($mixed)) {
		return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
	}
	return $mixed;
}
