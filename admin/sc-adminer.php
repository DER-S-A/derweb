<?php
include "funcionesSConsola.php";
checkUsuarioLogueadoRoot();

$server = $BD_SERVER;
$username = $BD_USER;
$db = $BD_DATABASE;
$pass = $BD_PASSWORD;

header("location: terceros/adminer/?server=$server&username=$username&db=$db&password=$pass");
?>