<?php
/**
 * Endpoint de la tabla avp_rendiciones
 */

include ("autoload.php");

// Pongo los encabezados por si me conecto desde javascript de otro servidor,
// me autorice la política CORS.

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

$objController = new Avp_rendicionesController();

// Recupero el nombre del método en base a la URL.
$strMethodName = $objController->getMethdoName();

// Verifico si el módulo y la clase se encuentra implementada. En caso de que no
// se encuentre devuelvo el error 404 not found.
if (!isset($strMethodName)) {
    header("HTTP/1.1 404 No encontrado");
    exit();
}

$objController->{$strMethodName}();
?>