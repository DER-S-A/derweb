<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$parametro = Request("parametro");
$valor = Request("valor");

echo(getParameter($parametro, $valor));
?>