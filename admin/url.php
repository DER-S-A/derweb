<?php
include_once('funcionesSConsola.php');

$fn = Request("fn", "");
$p = Request("p", "");
$k = Request("k");

if ($fn != "" && $fn == "getShort") {
	if ($p != "") {
		getShortUrl(base64_decode($p));
	} else {
		$aResult = getAjaxResponseArray("urls", 0);
		$aResult["error"] = "No se enviaron el parametro p con usuario, clave y url.";
		echo json_encode($aResult);
	}
} else {
	if ($k != "") {
		redireccionar($k);
	} else {
		$aResult = getAjaxResponseArray("urls", 0);
		$aResult["error"] = "No se envió ninguna key o función (fn, p, k).";
		echo json_encode($aResult);
	}
}

/**
 * Valida usuario, almacena URL en tabla y retorna ARRAY JSON
 * con url_corta en caso de éxito
 */
function getShortUrl($p)
{
	$maxRand = 999;
	$minRand = 100;
	$datos = json_decode($p, true);
	$url = $datos["url"];
	if (!startsWith($url, "http"))
		$url = "https://" . $url;
	$usuario = $datos["usuario"];
	$clave = md5($datos["clave"]);

	$result = array();
	$result = getUsuario($usuario, $clave);
	if (count($result) > 0) {

		//genera una KEY aleatoria de letras y nros y valida que no exista
		$key = strtoupper(chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . rand($minRand, $maxRand));
		while (!verificarKey($key)) {
			$key = strtoupper(chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . rand($minRand, $maxRand));
		}
		//Almacena URL con KEY
		insertarKey($key, $usuario, $url);
		$urlBase = $result[0]["url_base"];
		if (!startsWith($urlBase, "http"))
			$urlBase = "https://" . $urlBase;
		$urlCorta = $urlBase . "/url.php?k=" . $key;

		//Exito, retorna url_corta con resultado
		$aResult = getAjaxResponseArray("urls", 1);
		$aResult["url_corta"] = $urlCorta;
		echo json_encode($aResult);
	} else {
		$aResult = getAjaxResponseArray("urls", 0);
		$aResult["error"] = "No se pudo verificar el usuario y clave.";
		echo json_encode($aResult);
	}
}

function getUsuario($usuario, $clave)
{
	$bd = new BDObject();
	$SQL = "SELECT * 
			FROM url_usuarios 
			WHERE usuario = '$usuario' AND
				clave = '$clave'";
	$bd->execQuery($SQL);
	$rows = array();

	while (!$bd->EOF()) {
		$rows[] = $bd->getRow();
		$bd->Next();
	}
	$bd->close();
	return $rows;
}

/**
 * Verifica que la KEY dada no exista en la base
 */
function verificarKey($key)
{
	$bd = new BDObject();
	$SQL = "SELECT id 
			FROM url_urls 
			WHERE url_key = '$key'";

	$bd->execQuery($SQL);
	$rows = array();
	while (!$bd->EOF()) {
		$rows[] = $bd->getRow();
		$bd->Next();
	}
	$bd->close();
	if (count($rows) > 0)
		return false;
	return true;
}

/**
 * Inserta la traduccion en la base con fecha de hoy y entradas en cero
 */
function insertarKey($key, $usuario, $url)
{
	$bd = new BDObject();
	$SQL = "INSERT INTO url_urls (usuario, url, url_key, fecha, entradas) 
			VALUES	('$usuario', '$url', '$key', CURRENT_TIMESTAMP(), 0)";
	$bd->execInsert($SQL);
	$bd->close();
}


/**
 * Dado un key existente en la BASE, incrementa entradas + 1 y redirecciona a la dirección 
 * almacenada
 */
function redireccionar($k)
{
	$bd = new BDObject();

	$SQL = "SELECT * 
				FROM url_urls 
				WHERE url_key = '$k'";
	$bd->execQuery($SQL);

	$SQL = "UPDATE url_urls 
			SET entradas = entradas + 1
			WHERE url_key = '$k'";
	$bd->execQuery($SQL);
	$bd->close();
	$result = $bd->getValue("url");
	header("Location:" . $result);
}
