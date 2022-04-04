<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueadoRoot();

// copies files and non-empty directories
function sc3FtpDirTable($src, $xpattern = "")
{

	$table = new HtmlTable();
	$table->setWithAll();
	
	$aExts = array();
	$aExts["php"] = "images/php-16px.png";
	$aExts["png"] = "images/png.gif";
	$aExts["gif"] = "images/gif.gif";
	$aExts["jpg"] = "images/gif.gif";
	
	$cols = array("0" => array('align'=>'left', 'width'=>"40%"),
				"1" => array('align'=>'right'),
				"2" => array('align'=>'right'),
				"3" => array('align'=>'right'));
			
	$table->setColsStyle($cols);

	$fila = array("Nombre", "Tam", "Fecha", "Op");
	$table->setTitulos($fila);
	$table->setUseFilters();

	if (is_dir($src))
	{
		$files = scandir($src);

		foreach ($files as $file)
		{
			if ($file != ".")
			{
				$ftime = filemtime("$src/$file");
				$fecha = date ("d-m-Y H:i", $ftime);
				
				if (is_dir("$src/$file"))
				{
					//cambio de carpeta actual
					$url = new HtmlUrl("sc-listdir.php");
					if (sonIguales($file, ".."))
					{
						$url->add("path", dirname($src));
						$fecha = "";
					}
					else
						$url->add("path", "$src/$file");
					$fila = array(href(img("images/folder_open.png", "Carpeta") . " " . $file, $url->toUrl()) . " ", "", $fecha, "");
				}
				else
				{
					$size = FileSizeConvert(filesize("$src/$file"));
					clearstatcache();

					$path_info = pathinfo("$src/$file");
					$ext = "";
					if (isset($path_info['extension']))
						$ext = strtolower($path_info['extension']);
					
					$icon = img("images/file.gif", "Archivo $ext", "16");
					if (array_key_exists($ext, $aExts))
						$icon = img($aExts[$ext], "Archivo $ext", "16");

					$archivo = $icon . " " .  $file;
					if (!sonIguales($ext, "php"))
					{
						$url = new HtmlUrl("$src/$file");
						$archivo = href($archivo, $url->toUrl(), "archivo");
					}
					$fila = array($archivo, $size, $fecha, "");
				}
				$table->addFila($fila);
			}
		}
	}
	
	return $table->toHtml();
}

function FileSizeConvert($bytes)
{
	$bytes = floatval($bytes);
	$arBytes = array(
			0 => array(
					"UNIT" => "TB",
					"VALUE" => pow(1024, 4)
			),
			1 => array(
					"UNIT" => "<b>Gb</b>",
					"VALUE" => pow(1024, 3)
			),
			2 => array(
					"UNIT" => "<b>Mb</b>",
					"VALUE" => pow(1024, 2)
			),
			3 => array(
					"UNIT" => "Kb",
					"VALUE" => 1024
			),
			4 => array(
					"UNIT" => "b",
					"VALUE" => 1
			),
	);

	foreach($arBytes as $arItem)
	{
		if($bytes >= $arItem["VALUE"])
		{
			$result = $bytes / $arItem["VALUE"];
			$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
			break;
		}
	}
	return $result;
}

?>
<!doctype html>
<html lang="es">
<head>
<title>Navegador - SC3</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()" >

<table class="dlg">

	<tr>
		<td class="td_titulo">
			<?php 
			$dir = Request("path", "./");
			echo($dir);
			?>
		</td>
	</tr>
	
	<tr>
		<td>
			<?php
			echo(sc3FtpDirTable($dir));
			?>
		</td>
	</tr>
	
</table>

</body>
</html>