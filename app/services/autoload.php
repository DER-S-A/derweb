<?php
/**
 * Este script contiene todas las dependencias de clases que se
 * requieren para que el backend funcione.
 */

include("configuracion.php");
include("includes/db/DBConnection.class.php");
include("includes/db/DBDataFormat.class.php");
include("includes/db/DBCommand.class.php");
include("includes/apis/api-controller.php");
include("includes/model.inc.php");

// Referencia al endpoint rubros
include("modulos/articulos/rubros-model.inc.php");
include("modulos/articulos/rubros-controller.php");

// Referencia al end point entidades.
include("modulos/entidades/entidades-model.inc.php");
include("modulos/entidades/entidades-controller.php");

?>