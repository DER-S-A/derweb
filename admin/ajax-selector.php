<?php 
require("funcionesSConsola.php");



checkUsuarioLogueado();

$aquery = explode("|", Request("query"));
$query = $aquery[0];
$filtername = "";
if (sizeof($aquery) > 1)
	$filtername = $aquery[1];
$filterWhere = "";

$mfield = Request("mfield");
$mid = RequestInt("mid");
$id = RequestInt("id");

$rcontrol1 = requestOrSession("control1");
$extendedFilter = getSession($rcontrol1 . "-eqf");

header('Content-Type: text/html; charset="UTF-8"');

echo(translateCode($id, $query, $mfield, $mid, $extendedFilter, $filtername));
?>