<?php
$INCLUDE_LIGHT = 1;
 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$aquery = explode("|", Request("query"));
$query = $aquery[0];
$filtername = $aquery[1];
$filterWhere = "";

$desc = str_replace("'", "-", Request("desc"));

$mfield = Request("mfield");
$mid = RequestInt("mid");

echo(translateDesc($desc, $query, $mfield, $mid));
