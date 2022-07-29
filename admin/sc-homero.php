<?php

function crearCarpeta($xsubdir)
{
	if (!is_dir("./$xsubdir")) {
		echo ("<br>por crear $xsubdir...");
		mkdir("./$xsubdir");
	}
}

crearCarpeta("tmp");
crearCarpeta("ufiles");
crearCarpeta("backups");
crearCarpeta("errores");
crearCarpeta("logs");
crearCarpeta("modulos");

//TODO: descompactar /images.zip /terceros/fontawasome.zip etc


require("funcionesSConsola.php");
session_destroy();

echo (getUserAgent() . "<br>");

$ip = Request("ip");

$p = "" . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'];
if (!esVacio($ip))
	$p = "$ip$ip";

echo ("p=$p<br>");
$p = md5($p);

$v = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SERVER_SOFTWARE'] . $BD_DATABASE;
echo ("v=$v<br>");
$v = md5($v);

$bd = new BDObject();
$sql = "delete from sc_parametros where nombre = '$p'";
$bd->execQuery2($sql);

$sql = "insert into sc_parametros (nombre, valor) values('$p', '$v')";
$bd->execQuery2($sql);



$p = "" . $_SERVER['SERVER_NAME'];
echo ("p=$p<br>");
$p = md5($p);

$v = $BD_DATABASE;
echo ("v=$v<br>");
$v = md5($v);

$bd = new BDObject();
$sql = "delete from sc_parametros where nombre = '$p'";
$bd->execQuery2($sql);

$sql = "insert into sc_parametros (nombre, valor) values('$p', '$v')";
$bd->execQuery2($sql);

$desinstalar = RequestInt("desinstalar");
if ($desinstalar == 1) {
	$sql = "delete sc_parametros where length(nombre) = 32 and length(valor) = 32";
	$bd->execQuery2($sql);
}

echo ("<br><br>EXITO ! ($p, $v)");

function s152()
{
	global $BD_DATABASE;
	$p = md5("" . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR']);
	$v = md5($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SERVER_SOFTWARE'] . $BD_DATABASE);
	if (!sonIguales(getParameter($p, ""), $v)) {
		gotoPage("./sc-error.php?code=lic");
		return false;
	}
	return true;
}

function s1521()
{
	global $BD_DATABASE;

	//version 1: valida muchos datos
	$p = md5("" . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR']);
	$v = md5($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SERVER_SOFTWARE'] . $BD_DATABASE);
	if (sonIguales(getParameter($p, ""), $v)) {
		return true;
	}

	//segundo test, server - bd
	$p = md5("" . $_SERVER['SERVER_NAME']);
	$v = md5($BD_DATABASE);
	if (sonIguales(getParameter($p, ""), $v)) {
		return true;
	} else {
		gotoPage("./sc-error.php?code=lic");
		return false;
	}

	return true;
}

if (s1521())
	echo ("<br>instalado !!!");
else
	echo ("<br>NO instalado !!!");

echo ("<br><br> PHP version: " . phpversion());
?>

<pre><?php print_r($_SERVER); ?></pre>

</body>

</html>