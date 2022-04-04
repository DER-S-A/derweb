<?php
require("funcionesSConsola.php"); 
require("app-ema.php"); 

$rcode = Request("code");
$rmsg = Request("msg");
$lastError = getSession("sql-last-error");

$error = $rmsg;
if (strcmp($rcode, "1062") == 0)
{
	//Duplicate entry \'Movimiento anulado'\ for key 2
	$error = "Los datos se encuentran duplicados en el sistema. ";
	
	$amsg = explode("'", $lastError);
	$error .= "<br>Ya existe el valor: <b>" . $amsg[1] . "</b>";
}

//codigo=1452: Cannot add or update a child row: a foreign key constraint fails (`pinamarweb/pro_ocupacion`, CONSTRAINT `FK_pro_ocupacion_3` FOREIGN KEY (`idpropiedad`) REFERENCES `pro_propiedades` (`id`)) 
if (strcmp($rcode, "1452") == 0)
{
	$error = "Los datos seleccionados son incorrectos, no existen en el sistema. ";
	$amsg = explode("REFERENCES", $lastError);
	$quitar = array("`", "(", ")", "id", "_");
	$error .= "<br><b>(" . str_replace($quitar, " ", $amsg[1]) . ")</b>";
}

//codigo=1048: Column 'fecha_contacto' cannot be null
if (strcmp($rcode, "1048") == 0)
{
	$error = "No ha ingresado todos los datos solicitados. ";
	$amsg = explode("'", $lastError);
	$error .= "<br><b>(" . str_replace("_", " ", $amsg[1]) . ")</b>";
}

if (strcmp($rcode, "1451") == 0)
	$error = "El dato no se puede borrar porque esta siendo usado en el sistema. ";
if (strcmp($rcode, "2003") == 0)
	$error = "Error al conectarse con servidor de base de datos. ";
if (strcmp($rcode, "1146") == 0)
	$error = "Error, no existe esta tabla. ";
	
//codigo=1292:  Incorrect datetime value: '2010-44-55 12:42' for column 'fecha_contacto' at row 1	
if (strcmp($rcode, "1292") == 0)
{
	$error = "Error, el formado de fecha es incorrecto. ";
	$amsg = explode("for column", $lastError);
	$amsg = explode("'", $amsg[1]);
	$error .= "<br><b>(" . str_replace("_", " ", $amsg[1]) . ")</b>";
}

//1054: Unknown column 'op.aprobada' in 'field list'
if (strcmp($rcode, "1054") == 0)
{
	$error = "Error, no existe la columna.";
	$amsg = explode("'", $lastError);
	$error .= "<br><b>(" . $amsg[1] . ")</b>";
}

//codigo=1406: Data too long for column 'observaciones'
if (strcmp($rcode, "1406") == 0)
{
	$error = "Error, el texto ingresado es demasiado largo. ";
	$amsg = explode("for column", $lastError);
	$amsg = explode("'", $amsg[1]);
	$error .= "<br><b>(" . str_replace("_", " ", $amsg[1]) . ")</b>";
}

// codigo=1364: Field 'nro_factura' doesn't have a default value
if (strcmp($rcode, "1364") == 0)
{
	$error = "Error, falta un valor por defecto. ";
	$amsg = explode("'", $lastError);
	$error .= "<br><b>(" . str_replace("_", " ", $amsg[1]) . ")</b>";
}

if (strcmp($rcode, "lic") == 0)
	$error = "Error de licencia, falta archivo licencia.txt o es incorrecto. ";

if (strcmp($rcode, "ids") == 0)
{
	sleep(6);
	$error = "El servidor esta temporalmente caido. <br />Pruebe borrar archivos temporales y cookies de su explorador con CTROL + SHIFT + DEL (SUPR) y vuelva a acceder.";
}	
?>
<!doctype html>
<html lang="es">
<head>
<title>Error del sistema</title>

<?php include("include-head.php"); ?>

</head>

<body>

<p><br>
</p>
<table class="dlg">
  <tr>
    <td colspan="2" align="center" valign="top" class="td_titulo">Error en el sistema</td>
  </tr>
  <tr>
    <td width="150" align="center" valign="top">
		
		<i class="fa fa-bomb fa-fw fa-3x w3-text-red"> </i>

	</td>
    <td class="td_dato">
	     <p>Ha ocurrido un error en el sistema (codigo <?php echo($rcode) ?>).</p>
	    <p class="td_error"><?php echo($error) ?></p>
		
      <p>Causas frecuentes: </p>
      <ul>
        <li>No ingres&oacute; todos los <strong>datos solicitados</strong>.</li>
        <li>Los datos se encuentran <strong>repetidos</strong>. </li>
        <li>Intent&oacute; <strong>borrar</strong> un dato que est&aacute; siendo usado. </li>
      <?php 
      if (esRoot())
      {
      	echo("<li>" . href(img("images/index.gif", "Error") .  " Error", logFile(), "errores") . "</li>");
      }
      ?>
      </ul>
      
      <br>
    </td>
  </tr>
  
  <tr>
    <td>
    </td>
    
    <td>
      	
      	<a href="javascript:history.go(-1);" class="btn btn-warning" name="bcancelar">
	    	 	<i class='fa fa-undo fa-lg'></i> Cancelar  
        </a>
      	    
    </td>
  </tr>
</table>
<br>
<br>

<?php 

//comentado, genera SPAM y demoras !?

/*
//envia archivo de error
error_reporting(0);
$txt = file_get_contents(logFile());
if (strlen($txt) > 1200)
	$txt = substr($txt, - 1200);

enviarEmail(getCurrentUserEmail(), "marcos.casamayor@gmail.com", "", "SC3-ERROR: " . $SITIO, $txt, "", false, true);	
*/

?>

<?php include("footer.php"); ?>

</body>
</html>