<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$fileName = Request("f1");
$filecsv = Request("csv");
$filecsv = strtolower(sinCaracteresEspecialesNiEspacios($filecsv)) . "-" . date("Ymd-Hi") . ".csv";

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="' . $filecsv . '";');

$file = file_get_contents($fileName);
echo($file);
?>