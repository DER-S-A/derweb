<?php
require("funcionesSConsola.php");

$path = Request('path');
$path = str_replace('"', '', $path);
$cerrar = RequestInt("cerrar");
$nombreDestino = Request("archivo_destino");

$arr_file_types = array( 
					'application/msword',
					'application/sql',
					'application/txt',
					'application/pdf',
					'application/vnd.ms-excel',
					'application/json',
					'text/plain',
					'text/csv', 
					'image/gif',
					'image/jpeg',
					'image/jpg',
					'image/png',
					'image/svg+xml',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'application/octet-stream'
					);

if (!esVacio($_FILES['file']['type']))
	if (!(in_array($_FILES['file']['type'], $arr_file_types))) 
	{
		echo("Tipo " . $_FILES['file']['type'] . " no permitido!");
		return;
	}

$resize = 1;

$file = 'f' . time() . '_' . strtolower(str_replace(" ", "", $_FILES['file']['name']));
if (!esVacio($nombreDestino))
	$file = basename($nombreDestino);

move_uploaded_file($_FILES['file']['tmp_name'], 'ufiles/' . trim($path) . '/' . $file);
$archivo = trim($path . "/" . $file); 
$ruta = trim($path);
$archResize = resizeImg($UPLOAD_PATH_SHORT, $file, $IMAGE_SIZE, $resize, $ruta);

if ($cerrar == 0)
	echo($archivo);
else
	echo("<!doctype html>
			<html>
				<body><script>window.close();</script>
				</body>
			</html>");
?>