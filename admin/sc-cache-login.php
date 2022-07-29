<?php


function login($userPass)
{
	include('config.php');
    $datos = array();
    $datos = explode('|', $userPass);
    $conexion = mysqli_connect($BD_SERVER, $BD_USER, $BD_PASSWORD, $BD_DATABASE);
    $sql = 'SELECT login, clave FROM sc_usuarios';
    $sql .= ' where login = "'. $datos[0] .'" AND clave = "'.$datos[1].'"';
    if (!$result = mysqli_query($conexion, $sql)) die ('Error en la sentencia SQL.' . $sql);
	
    while($row = mysqli_fetch_assoc($result)){
        $rawdata[] = $row;
    }
    return json_encode($rawdata);
}

$userPass = $_GET['userPass'];

echo login($userPass);


?>
