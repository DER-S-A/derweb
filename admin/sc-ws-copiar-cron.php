<?php 
/*
Fecha: 16-may-2021
Autor: Marcos Casamayor
*/
include("funcionesSConsola.php");

//EN CIM $url = "https://www.sc3-app.com.ar/ws/LoginTicketRequest-cim.xml.cms";
$url = getParameter("ws-url-cms", "");
$destino = "./ws-afip/certificados/LoginTicketRequest.xml.cms";

if (esVacio($url))
{
	$cms = "Parametro ws-url-cms no definido";
}
else
{
	$cms = file_get_contents($url);
	echo("Por generar archivo $destino :" . substr($cms, 0, 35));
}

$handle = fopen($destino, "wa+");
fwrite($handle, $cms);
fclose($handle);
?>