<?php 
require("funcionesSConsola.php");

?>
<!doctype html>
<html lang="es">
<head>
<title>Subir archivos - por SC3</title>

<?php include("include-head.php"); ?>

<script language="JavaScript">

function close_window() 
{
    window.close();
}

console.clear();
prefijoc = '<?php echo(Request("prefix")) ?>'; 
path = '<?php echo(RequestInt("path")) ?>';
control = '<?php echo(Request("control")) ?>';
iframe =  '<?php echo(Request("iframe")) ?>';

function setParentPath()
{
	nombreArchivo = document.getElementById('file').value; 
	console.log("nombreArchivo: " + nombreArchivo);
	
	if (path != '')
		path = path + '/'; 
	prefijo = '';
	if (prefijoc != '' && prefijoc != '-')
	{
		prefijo = creator.parent.contenido.document.getElementById(prefijoc).value; 
	}
	nombreArchivo = nombreArchivo.substring(nombreArchivo.lastIndexOf('\\') + 1);
	nombreArchivo = path + '<?php echo(getPrefijoImg()); ?>' + '-' + prefijo + '-' + nombreArchivo;

	document.getElementById('archivo_destino').value = nombreArchivo;
	//modo tradicional: adjunto editando 
	if (creator.parent != null && creator.parent.contenido != null && 
									creator.parent.contenido.document.getElementById(control) != null)
		{
			creator.parent.contenido.document.getElementById(control).value = nombreArchivo;
			div1 = creator.parent.contenido.document.getElementById('previewImagen' + control);
			if (div1 != null)
				div1.innerHTML = nombreArchivo;
		}
	else
	{
		console.log("seteando (2) en : " + control);
		//modo debil: adjunto editando dentro de una solapa del view
		if (creator.document.getElementById(control) != null)
		{
			creator.document.getElementById(control).value = nombreArchivo;
			div1 = creator.document.getElementById('previewImagen' + control);
			if (div1 != null)
				div1.innerHTML = nombreArchivo;
		}
	}
		
	document.getElementById('prefix').value = prefijo;
	return false;
}

</script>

</head>
<body>

<!-- form name="attachments" method="POST" action="enviarArchivo.php" enctype="multipart/form-data" onSubmit="javascript:setParentPath()" -->
<form name="attachments" method="POST" action="sc-html-enviararchivo-ajax.php" enctype="multipart/form-data" onSubmit="javascript:setParentPath()">

<table class="dlg">
    <tr>
      <td class="td_titulo">Subir Archivo</td>
    </tr>
    <tr>
      <td class="td_dato">Las im&aacute;genes (jpg, gif, png) ser&aacute;n redimensionadas a un tama&ntilde;o est&aacute;ndar (Ancho: <?php echo($IMAGE_SIZE) ?> px). </td>
    </tr>
    
    <tr>
      <td class="td_dato">
		<input type="hidden" name="path" id="path" value="<?php echo(RequestInt("path")) ?>">
		<input type="hidden" name="upload" id="upload" value="true">
		<input type="hidden" name="prefix" id="prefix" value="">
		<input type="hidden" name="cerrar" id="cerrar" value="1">
		<input type="hidden" name="archivo_destino" id="archivo_destino" value="">
        <br />
        <input name="file" type="file"  id="file" width="60">
        <br>
        <br>
        <label>
			<select name="resize">
			<option value="1" selected="selected">Redimensionar al subir (recomendado)</option>
			</select>
        </label>
        <br />
        <br />
        <input name="Submit" type="submit" class="buscar" value="Subir">
        <br />
        <br />
      </td>
    </tr>
  </table>
</form>
</body>
</html>
