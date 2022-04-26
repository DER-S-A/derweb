<?php
/**
 * Este script contiene la actualización de versión del sistema
 * DERWEB.
 */

require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
include("der-updversion-clientes-potenciales.php");

// Clientes potenciales.
agregarOperCliPot_CambiarEstado();
agregarOperCliPot_AgregarNotas();
?>