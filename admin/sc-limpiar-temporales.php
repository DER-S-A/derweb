<?php
/**
 * Este script contiene la operación para limpiar la cache del navegador.
 */
include "funcionesSConsola.php";
require("sc-updversion-utils.php");

// Limpia la cache.
Sc3FileUtils::borrarArchivos("tmp/");
Sc3FileUtils::borrarArchivos("tmpcache/");
Sc3FileUtils::borrarArchivos("scripts/tmpcache/");
Sc3FileUtils::borrarArchivos("css/tmpcache/");

?>