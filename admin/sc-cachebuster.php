<?php 

/**
 * Crea un archivo nuevo cada vez que se modifica un script y retorna su nombre
 * es BUSTER pero ya es tarde !
 * jun-2019
 * @author Marcos C
 * @param archivo destino $xfilename
 * @param carpeta de cache $xsubdir
 * @return string
 */
function sc3CacheButer($xfilename, $xsubdir = "tmpcache")
{
	if (!file_exists($xfilename))
		return "noexiste-$xfilename";
	
	//fecha del archivo, con segundos        
	$mod = date("Ym-dHis", filemtime($xfilename));
	
	$aFile = pathinfo($xfilename);
	
	$newFolder = $aFile["dirname"] . "/$xsubdir/";
	if (!file_exists($newFolder))
		mkdir($newFolder);
	
	$file2 = $newFolder . $aFile["filename"] . "-$mod." . $aFile["extension"];
	if (!file_exists($file2))
	{
		copy($xfilename, $file2);
	}
	
	return $file2;
}

?>