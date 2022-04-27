<?php
/**
 * Este script permite instalar módulos en el sistema.
 * Fecha: 27/12/2021
 */

include("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
include("modulos/generador_operaciones/sc-module-install.php");

?>
<html>
<head>
<title>sc3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="sc-gris.css" type="text/css">

</head>
<body>
<div class="td_titulo2">Instalador de módulos </div> 
<br>
<?php
    instalarGeneradorDeOperaciones();
?>