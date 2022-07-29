<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueado();

$error = "";
if (enviado())
{
	//si no viene el dato pone ese, es cuando se invica desde el administrador
	$clavev  = Request("clavev", "ABC1");
	$claven  = Request("claven");
	$claven2 = Request("claven2");
	$idusuario = RequestInt("idusuario");
	if ($idusuario == getCurrentUser() && !sonIguales($clavev, "ABC1") && !esClaveCorrecta($clavev))
		$error = "La clave actual es incorrecta.";
	else	
	if (strcmp($claven, $claven2) != 0)
		$error = "Las claves ingresadas no son iguales.";
	else	
	if (esClaveTrivial($claven))
		$error = "La clave ingresada es trivial.";
	else
	{
		sc3cambiarClave($clavev, $claven, $claven2, $idusuario);
		goOn();
	}	
}

$idusuario = getCurrentUser();
$otroUsr = false;

$mquery = Request("mquery");
if (sonIguales($mquery, "sc_usuarios"))
{
	$idusuario = RequestInt("mid");
	$otroUsr = true;	
}
?>
<!DOCTYPE html>
<html>
<head>

<title>SC3 - cambio de clave</title>

<?php include("include-head.php"); ?>

</head>

<body onload="firstFocus()">

<form method="post" name="form1" id="form1">
  <?php
  $req = new FormValidator();
  ?>
  <table class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><img src="images/lock_edit.png" alt="Cambiar clave" />  Cambio de clave </td>
            <td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
            <td width="50" align="center"><?php echo(linkCerrar()); ?></td>
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
      <td colspan="2" align="left" class="td_dato">
        <p><strong>Importante</strong>: </p>
        <ul>
          <li>No utilice claves f&aacute;ciles o triviales (ej: 123, 1234, su fecha de cumplea&ntilde;os, etc)</li>
          <li>No divulgue su clave a terceros.</li>
          <li>No anote su clave en el escritorio de trabajo.   </li>
          <li>Recuerde que en las operaciones del sistema queda registrado el usuario que la realiz&oacute;. </li>
        </ul>
      </td>
    </tr>
    
    <tr>
      <td width="200" align="right" class="td_etiqueta">Usuario: </td>
      <td width="400" class="td_dato">
        <?php
        $selUsr = new HtmlSelector("idusuario", "sc_usuarios", $idusuario);
        $selUsr->setReadOnly(true); 
        echo($selUsr->toHtml());
		?>
      </td>
    </tr>
    
    <?php 
    if (!$otroUsr)
    {
    ?>
    <tr>
      <td width="200" class="td_etiqueta">Clave actual: </td>
      <td class="td_dato">
        <?php 
		$cclavev = new HtmlInputText("clavev", "");
		$cclavev->setTypePassword();
		echo($cclavev->toHtml()); 
		$req->add("clavev", "Clave actual");
		?>
      </td>
    </tr>
	<?php 
    }
	?>
    
    <tr>
      <td class="td_etiqueta">Nueva clave: </td>
      <td class="td_dato">
		<?php 
		$cclaven = new HtmlInputText("claven", "");
		$cclaven->setTypePassword();
		echo($cclaven->toHtml()); 
		$req->add("claven", "Nueva clave");
		?>*
	  </td>
    </tr>
    
    <tr>
      <td class="td_etiqueta">Nueva clave (confirmaci&oacute;n): </td>
      <td class="td_dato">
		<?php 
		$cclaven2 = new HtmlInputText("claven2", "");
		$cclaven2->setTypePassword();
		echo($cclaven2->toHtml()); 
		$req->add("claven2", "Nueva clave (confirmacion)");
		?>*
	  </td>
    </tr>
    
    <tr>
      <td class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
      	<?php 
		$bok = new HtmlBotonOkCancel();      	
      	echo($bok->toHtml());
      	?>
      </td>
    </tr>
  </table>
  
	<script language="JavaScript" type="text/javascript">
	<?php
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		if (validar())
		{
			pleaseWait2();
			document.getElementById('form1').submit();
		}

	}
	</script>

</form>
<?php include("footer.php"); ?>
</body>
</html>
