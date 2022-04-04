<?php
include("funcionesSConsola.php");
//include("sc-encrypt.php");

checkUsuarioLogueado();

$valor = Request("valor");
$error = "";
$modoEdit = RequestInt("modoedit");

$campo = Request("campo");
$mquery = Request("mquery");
$mid = RequestInt("mid");

$claveValida = false;
if (enviado())
{
	$clave = Request("claveusr");
	$claveValida = sc3IsValidPass($clave);

	if (!$claveValida)
		$error = "La clave no es correcta.";
	else 
	{
		$valorReal = Request("valorreal");
		//actualiza valor
		if (!esVacio($valorReal) && !esVacio($mquery) && !esVacio($mid) && !esVacio($campo))
		{
			$enc = new Sc3Encriptador();
			$valor = $enc->encrypt1($valorReal);
			
			$qinfo = getQueryObj($mquery);
			$sql = "update " . $qinfo->getQueryTable();
			$sql .= " set $campo = " . comillasSql($enc->encrypt1($valorReal));
			$sql .= " where " . $qinfo->getKeyField() . " = $mid";
			
			$bd = new BDObject();
			$bd->execQuery($sql);
		}
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>sc3</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">


<form method="post" name="form1" id="form1">
  <?php
  $req = new FormValidator();
  ?>
  <table width="95%" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center" width="85%"><img src="images/lock_edit.png" alt="Ver" /> Ver valor protegido </td>
			<td align="right" width="15%"> 
				<a id="linkcerrarW" href="#" onclick="window.close();">
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
      <td colspan="2" align="left" class="td_error">
      	<?php 
      	echo($error); 
      	?>
      </td>
    </tr>
    <?php
	}
	?>
	
    <tr>
      <td width="150" align="right" class="td_etiqueta">Clave de <?php echo(getCurrentUserLogin());?>:</td>
      <td class="td_dato">
        <?php 
		$txtClave = new HtmlInputText("claveusr", "");
		$txtClave->setTypePassword();
		$txtClave->setRequerido();
		echo($txtClave->toHtml());

		$req->add("claveusr", "clave");
        ?>
	  </td>
    </tr>

    <?php 
    if (enviado() && $claveValida)
    {
    ?>
    
    <tr>
      <td width="150" align="right" class="td_etiqueta">Valor: </td>
      <td class="td_dato">
		<?php
		
		$enc = new Sc3Encriptador();
		$valorReal = $enc->decrypt1($valor);
		
		if ($modoEdit)
		{
			$txtArea = new HtmlInputTextarea("valorreal", $valorReal);	
			$txtArea->setCols(45);		
			echo($txtArea->toHtml());
		}
		else 
			echo($valorReal);
		?>
      	
      	<br />
      </td>
    </tr>

    <?php 
    }
    ?>

    <tr>
      <td width="150" align="right" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="150" align="right" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
	      <?php 
	      $bok = new HtmlBotonOkCancel(true, false);
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
		//make sure hidden and iframe values are in sync for all rtes before submitting form
		updateRTEs();
		if (validar())
			document.getElementById('form1').submit();
	}
	
	//Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML, encHTML)
	initRTE("./imagesrte/", "./", "", false);
	</script>
</form>



</body>
</html>
