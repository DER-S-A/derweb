<?php
/**
 * Este script contiene todas las dependencias de clases que se
 * requieren para que el backend funcione.
 */

 // Incorporo las dependencias de admin para manejar la base de datos
 // de tal forma de tener unificada las clases en un solo lugar.
include_once("../../admin/config.php");
include_once("../../admin/funcionesSConsola.php");
include_once("../../admin/dbcommand.php");

include("includes/apis/api-controller.php");
include("includes/model.inc.php");

// Referencia al endpoint rubros
include("modulos/articulos/rubros-model.inc.php");
include("modulos/articulos/rubros-controller.php");

// Referencia al end point entidades.
include("modulos/entidades/entidades-model.inc.php");
include("modulos/entidades/entidades-controller.php");
include("modulos/articulos/marcas-model.inc.php");
include("modulos/articulos/marcas-controller.php");
include("modulos/articulos/subrubros-model.inc.php");
include("modulos/articulos/subrubros-controller.php");
include("modulos/clientes-potenciales/clientes-potenciales-model.inc.php");
include("modulos/clientes-potenciales/clientes-potenciales-controller.php");
?>