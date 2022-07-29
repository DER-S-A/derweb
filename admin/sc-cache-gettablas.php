<?php

include('config.php');

$conn = mysqli_connect($BD_SERVER, $BD_USER, $BD_PASSWORD, $BD_DATABASE);

if ($conn->connect_error) 
{
    die("Conexion fallida: " . $conn->connect_error);
}

$sql = "select queryname, querydescription 
        from sc_querys 
        where es_cacheable = 1";

$result = mysqli_query($conn, $sql);

$rawdata = array();
while($row = mysqli_fetch_assoc($result))
    $rawdata[] = $row;
echo json_encode($rawdata);
?>