<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//variables del Request
$idquery = RequestInt("idquery");
$rsQ = locateRecordId("sc_querys", $idquery);
$rquery = $rsQ->getValue("queryname");

if (strcmp($rquery, "") == 0)
	echo("<h3>Falta parámetro: query</h3> Ej: sc-selitems.php<b>?query=propiedades_sin_borrar</b>");

$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);


$qinfo = new ScQueryInfo($query_info);
$qinfo->generateFieldsInfo(true);
goOn();
?>