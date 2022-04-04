<?php
include "sc-zipfile.php";

// copies files and non-empty directories
function rcopy($src, $dst, $xdias = 0, $xpattern = "", $xdeleteSource = FALSE)
{
	echo ("<hr><br>Copiando de <b>$src</b> a $dst:");

	if (is_dir($src)) {
		if (!file_exists("$dst"))
			mkdir($dst);

		$files = scandir($src);
		$hoy = getdate(time());

		foreach ($files as $file) {
			if (($file != "." && $file != "..") && (!is_dir($file))) {
				if (sonIguales($xpattern, "") || strContiene($file, $xpattern)) {
					$ftime = filemtime("$src/$file");
					clearstatcache();

					// 3 meses
					$cachetime = 24 * 60 * 60 * $xdias;
					if (($xdias == 0) || (time() - $cachetime < $ftime)) {
						echo ("<br> copiando $file..." . date("(d-m-Y H:i)", $ftime));
						if (file_exists("$src/$file") && !is_dir("$src/$file")) {
							copy("$src/$file", "$dst/$file");
							if ($xdeleteSource)
								unlink("$src/$file");
						}
					}
				}
			}
		}
	}
}



function sc3PackZipVersion($xdir, $xzip, $xzippath = "")
{
	echo ("<br>agregando carpeta $xdir...");

	$files = scandir($xdir);
	foreach ($files as $file) {
		if (!sonIguales($file, ".") && !sonIguales($file, "..") && !sonIguales($file, "version.zip")) {
			$f = "$xdir/$file";
			if (is_dir($f)) {
				$xzip->add_dir($file);
				sc3PackZipVersion($f, $xzip, $xzippath . "/" . $file);
			} else {
				$xzip->add_file(implode("", file($f)), $xzippath . "/" . str_replace("/", "", str_replace("./", "", $file)));
			}
		}
	}
}



function sc3Deploy($aDeployCarpetas, $xdias = 90)
{
	echo ("<hr><br>iniciando deploy de <b>$xdias</b> dias...");

	$dest = crearDeployFolder($xdias);
	for ($i =0; $i < sizeof($aDeployCarpetas); $i++) {
		if (sonIguales($aDeployCarpetas[$i], "/"))
			generarArchivosEnDeploy("." . $aDeployCarpetas[$i], $dest, $xdias);
		else
			generarArchivosEnDeploy("." . $aDeployCarpetas[$i], $dest . $aDeployCarpetas[$i], $xdias);
	}

	$fp = fopen("$dest/licencia.txt", 'w');
	fwrite($fp, "--" . md5(Sc3FechaUtils::formatFecha(getdate())) . "--");
	fclose($fp);

	$aborrar = $dest . "/vssver.scc";
	Sc3FileUtils::sc3BorrarArchivo($aborrar);

	$aborrar = $dest . "/sc-homero.php";
	Sc3FileUtils::sc3BorrarArchivo($aborrar);

	$aborrar = $dest . "/config.php";
	Sc3FileUtils::sc3BorrarArchivo($aborrar);

	$aborrar = $dest . "/sc-deploy.php";
	Sc3FileUtils::sc3BorrarArchivo($aborrar);

	$aborrar = $dest . "/sc-deployversion.php";
	Sc3FileUtils::sc3BorrarArchivo($aborrar);

	$aborrar = $dest . "/app-test.php";
	Sc3FileUtils::sc3BorrarArchivo($aborrar);


}


/**
 * crearDeployFolder
 * Permite crear la carpeta deploy
 * @param  int $xdias
 * @return void
 */
function crearDeployFolder($xdias = 90) {
	$hoy = getdate(time());

	$fecha = "";
	$fecha = $hoy["year"] . "-" . $hoy["mon"];
	$fecha .= "-" . $xdias;

	$dest = "../../deploy/update-$fecha";
	if (!file_exists("$dest"))
		mkdir($dest);
	return $dest;
}

/**
 * generarArchivosEnDeploy
 * Permite crear un subdirectorio
 * @param  string $destino
 * @return void
 */
function generarArchivosEnDeploy($origen, $destino, $xdias) {
	if (!file_exists("$destino"))
		mkdir($destino);
	rcopy($origen, $destino, $xdias);		
}
