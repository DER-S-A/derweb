<?php

$anio = date("Y") - 1;
if (isset($_GET["patron"]))
	$anio = $_GET["patron"];


function borrarArchivos($dir, $xext, $xpatron)
{
	//valida no borrar carpeta vacía (otra vez)
	if (isset($dir) && $dir != "" && $dir != " ") {
		$path = realpath($dir);
		if ($path !== false && is_dir($path)) {

			$fechaActual = date("Y-m-d");
			echo ("<br>Borrando archivos en la carpeta $dir ($fechaActual , patron $xpatron)... ");

			$cant = 0;

			foreach (glob($dir . "*.$xext") as $v) {
				echo("<br>$v... ");
				//si cumple patron de búsqueda
				if (strpos($v, $xpatron) > 0) {
					$fechaArchivo = date("Y-m-d", filemtime($v));
					if ($fechaArchivo < $fechaActual) {
						echo(" por borrar");
						unlink($v);
						$cant++;
					}
				}
			}
			if ($cant > 0)
				echo (" borrados $cant archivos");
		} else
			echo ("<br>La carpeta $dir no existe!");
	}
}


borrarArchivos("facturas/0009/2021/01/", "pdf", $anio);
borrarArchivos("facturas/0009/2021/02/", "pdf", $anio);
borrarArchivos("facturas/0009/2021/03/", "pdf", $anio);
borrarArchivos("facturas/0009/2021/04/", "pdf", $anio);
borrarArchivos("facturas/0009/2021/05/", "pdf", $anio);



