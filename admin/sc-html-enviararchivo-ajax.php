<?php
require("funcionesSConsola.php");

$path = Request('path');

if (esVacio($path)) {
	//agrupa los directorios por trimestre
	if (getParameterInt("sc3-images-subdir", "1")) {
		$path =  getImagesPath();
		$path2 = Sc3FechaUtils::formatFechaPath();
		$e = error_reporting(0);
		mkdir($path . $path2, 0777, true);
		$e = error_reporting($e);
		$path = $path2;
	}
}
$path = str_replace('"', '', $path);
$cerrar = RequestInt("cerrar");
$nombreDestino = Request("archivo_destino");


$arr_file_types = [
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
	'image/bmp',
	'image/x-bmp',
	'image/x-bitmap',
	'image/x-windows-bmp',
	'image/webp',
	'video/mp4',
	'image/svg+xml',
	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'application/octet-stream'
];

if (!esVacio($_FILES['file']['type']))
	if (!(in_array($_FILES['file']['type'], $arr_file_types))) {
		echo ("Tipo " . $_FILES['file']['type'] . " no permitido!");
		return;
	}

$resize = 1;

$file = 'f' . time() . '_' . strtolower(str_replace(" ", "", $_FILES['file']['name']));
if (!esVacio($nombreDestino))
	$file = basename($nombreDestino);
$archivo = $file;
$exito = move_uploaded_file($_FILES['file']['tmp_name'], 'ufiles/' . trim($path) . '/' . $file);
if ($exito) {

	$ruta = 'ufiles/' . trim($path) . '/' . $file;
	$archivo = trim($path . "/" . $file);
	if (file_exists('ufiles/' . trim($path) . '/' . $file) && (strContiene($ruta, "bmp") || strContiene($ruta, "webp"))) {
		$extension = pathinfo($ruta, PATHINFO_EXTENSION);
		$nombreImagen = basename($ruta, $extension);
		$rutaImagen = dirname($ruta);
		list($ancho, $alto) = getimagesize($ruta);
		$srcImagen = "";

		//creo una imagen vacia con la dimension original
		$dstImg = ImageCreateTrueColor(intval($ancho), intval($alto));
		if ($extension == "bmp")
			$srcImagen = imagecreatefrombmp($ruta);
		if ($extension == "webp")
			$srcImagen = imagecreatefromwebp($ruta);

		//copia el contenido de la imagen original (src) a la nueva imagen en blanco, creada anteriormente
		imagecopyresampled($dstImg, $srcImagen, 0, 0, 0, 0, intval($ancho), intval($alto), intval($ancho), intval($alto));

		//Nueva ruta temporal
		$nuevaRuta = $rutaImagen . "/" . str_replace(".", "", $nombreImagen);
		
		//guarda la nueva imagen en jpeg.
		imagejpeg($dstImg, $nuevaRuta. ".jpg");
		$archivo = str_replace("ufiles/", "", $nuevaRuta . ".jpg");

		//elimino el bmp original
		unlink($ruta);
	}
	$ruta = trim($path);
	$archResize = resizeImg($UPLOAD_PATH_SHORT, $file, $IMAGE_SIZE, $resize, $ruta);

	if ($cerrar == 0)
		echo ($archivo);
	else
		echo ("<!doctype html>
				<html>
					<body><script>window.close();</script>
					</body>
				</html>");
} else {
	echo ("Error al mover archivo a ufiles/$archivo");
}
