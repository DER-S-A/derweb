<?php

/**
 * Clase con métodos estáticos de archivos y carpetas
 */
class Sc3FileUtils {

	/**
	 * Funcion para borrar archivos, recibe como parametro la carpeta 
	 * Sólo borra archivos del día anterior, no de hoy
	 */
	public static function borrarArchivos($dir, $imprimir = true)
	{
		//valida no borrar carpeta vacía (otra vez)
		if (isset($dir) && $dir != "" && $dir != " ") {
			$path = realpath($dir);
			if ($path !== false && is_dir($path)) {
				if ($imprimir)
					echo ("<br>Borrando archivos en la carpeta $dir ... ");
				$fechaActual = date("Y-m-d");
				$cant = 0;
				foreach (glob($dir . '*.*') as $v) {
					$fechaArchivo = date("Y-m-d", filemtime($v));
					if ($fechaArchivo < $fechaActual) {
						unlink($v);
						$cant++;
					}
				}
				if ($cant > 0 && $imprimir)
					echo (" borrados $cant archivos");
			} else if ($imprimir) {
				echo ("<br>La carpeta $dir no existe!");
			}
		} else {
			echo ("<br>Direccion ingresada invalida!");
		}
	}


	/**
	 * Mueve una carpeta usando el rename. Si ya existe el destino no hace nada
	 */
	public static function moverCarpeta($xorig, $xdest, $xnuevoNombre = "")
	{
		if (file_exists($xorig))
		{
			if (!file_exists("./$xdest"))
				mkdir("./$xdest");

			$destino = "$xdest/$xorig";
			if (!esVacio($xnuevoNombre))
				$destino = "$xdest/$xnuevoNombre";

			echo("<br>moviendo de <b>$xorig</b> a $destino");
			if (!file_exists($destino))
				rename($xorig, $destino);
			else
				Sc3FileUtils::rrmdir($xorig);
		}
	}


	/**
	 * Borra carpeta recursivamente
	 */
	public static function rrmdir($dir) {

		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") 
						Sc3FileUtils::rrmdir($dir . "/" . $object); 
					else 
						unlink($dir . "/" . $object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	} 


	/**
	 * Copia el archivo a un lugar permanente
	 * @param string $xfile
	 * @return string
	 */
	public static function sc3SaveFile($xfile)
	{
		global $UPLOAD_PATH_SHORT;
		
		$docName = basename($xfile);
		$path =  getImagesPath();
		//agrupa los directorios por trimestre
		if (getParameterInt("sc3-images-subdir", "1"))
		{
			$path .= Sc3FechaUtils::formatFechaPath();
			$docName = getImagesPath() . Sc3FechaUtils::formatFechaPath() . "/" . $docName;
		}

		copy($UPLOAD_PATH_SHORT . "/" . $xfile, $docName);
		$docName = str_replace(getImagesPath(), "", $docName);
		return $UPLOAD_PATH_SHORT . "/" .$docName;
	}

	/**
	 * Borra y archivo y lo dice
	 */
	public static function sc3BorrarArchivo($xaborrar, $ximprime = true)
	{
		if (file_exists($xaborrar)) 
		{
			if ($ximprime)
				echo("<br>Borrando <b>$xaborrar</b>");
			unlink($xaborrar);
		}
	}

}
