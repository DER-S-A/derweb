<?php 
include("funcionesSConsola.php");

checkUsuarioLogueado();
$pattern = Request("pattern");
$pattern2 = str_replace("-", " ", $pattern);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>sc3 - historico de reportes</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">
  <table width="95%" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
            <td width="50" align="center">
              	<a id="linkcerrarW" href="#" onClick="window.close();">
					<img src="images/close.gif" border="0" title="Cerrar [Esc]"  alt="Cerrar [Esc]"/> 
				</a> 
  			</td>
          </tr>
        </table>
      </td>
    </tr>
    <?php
	if ($error != "")
	{
	?>
    <tr>
      <td colspan="2" align="left" class="td_error"><?php echo($error); ?></td>
    </tr>
    <?php
	}
	?>
    <tr>
      <td width="150" align="right" valign="top" class="td_etiqueta">Historico de <?php echo(substr($pattern2, 0, strlen($pattern2) - 1)); ?></td>
      <td class="td_dato">
        <?php 
		  //getting all files of desired extension from the dir using explode
		
		$desired_extension = 'pdf'; //extension we're looking for
		$dirname = "tmp/";
		$dir = opendir($dirname);
		$pattern .= getCurrentUser() . "-";
		if (strlen($pattern) > 10)
		{
			while(false != ($file = readdir($dir)))
			{
				if(($file != ".") and ($file != ".."))
				{
					$fileChunks = explode(".", $file);
					$pos = strpos($fileChunks[0], $pattern);
					if($fileChunks[1] == $desired_extension && ($pos === 0)) //interested in second chunk only
					{      
						$filename = substr($file, 0, strlen($pattern) - 3);
						$file2 = $dirname . "" . $file;
						echo(href(img("images/pdf.jpg", "Archivo pdf") . " " . $filename . " (" . date("d/m/Y H:i", filemtime($file2)), $dirname . $file, "_blank") . ")<br>");
					}
				}
			}
		}
		closedir($dir);
		?>
      </td>
    </tr>
  </table>
<?php include("footer.php"); ?>
</body>
</html>
