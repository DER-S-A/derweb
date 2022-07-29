<?php
include "funcionesSConsola.php";
checkUsuarioLogueadoRoot();

$server = $BD_SERVER;
$username = $BD_USER;
$db = $BD_DATABASE;
$pass = $BD_PASSWORD;

$revelarClave = getParameterInt("adminer-revelar-clave", 0);
if ($revelarClave == 0)
    $pass = "";

header("location: terceros/adminer/?server=$server&username=$username&db=$db&password=$pass");
