<?php
// Pongo los encabezados por si me conecto desde javascript de otro servidor,
// me autorice la política CORS.
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

require("../../../funcionesSConsola.php");
require("../../../app/utils/model.inc.php");
require("../../../app/administracion/avisos-pagos/rendiciones-admin-model.php");

$parametros = file_get_contents("php://input");
$aParametros = json_decode($parametros, true);

$aResponse = [];
$objModel = new RendicionesAdminModel();
$aResponse = $objModel->actualizarMovimiento($aParametros);

echo json_encode($aResponse);