<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$fileName = Request("f1");
$archGuardar = Request("nombre");
$ext = Request("ext");
$rename = RequestInt("rename", 1);
if ($rename == 1)
    $archGuardar = strtolower(sinCaracteresEspecialesNiEspacios($archGuardar)) . "-" . date("Ymd-Hi") . ".csv";

//ver https://developer.mozilla.org/es/docs/Web/HTTP/Basics_of_HTTP/MIME_types

if (sonIguales($ext, "csv"))
    header('Content-Type: application/csv');

if (sonIguales($ext, "txt"))
    header('Content-Type: text/plain');

header('Content-Disposition: attachment; filename="' . $archGuardar . '";');

$file = file_get_contents($fileName);
echo($file);
?>