<?php 
include("funcionesSConsola.php");
checkUsuarioLogueado();

$error = "";

$nota = "";
$color = "";

$insert = RequestInt("insert");
if (enviado())
{
		$nota = Request("nota");
		$titulo = Request("titulo");
		$usuario = getCurrentUserLogin();
		
		$rsuser = locateRecordWhere("cal_accounts", "user = '" . $usuario . "'");
		$userid = $rsuser->getValue("id");
		$fechaStamp = Request("fecha_a") . "-" . Request("fecha_m") . "-" . Request("fecha_d") . " " . Request("fecha_h") . ":" . Request("fecha_n");
		$sql = "insert into cal_events(username, eventtype, user_id, stamp, duration, subject, description)";
		$sql .= " values ('" . $usuario . "', 0, $userid ,'" . $fechaStamp . "', '" . $fechaStamp . "', '" . $titulo . "', '" . $nota . "')";
	
		$sent = new BDObject();
		$sent->execQuery($sql);
	
		goOn();
}
else
{
	$mid = RequestInt("mid");
	$mquery = Request("mquery");
	$qinfo = getQueryObj($mquery);
	$idquery = $qinfo->getQueryId();
	$rs = locateRecordId($qinfo->getQueryTable(), $mid);
	$nota = $rs->getValue($qinfo->getComboField());
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Agendar</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">
<form method="post" name="form1" id="form1">
  <?
  $req = new FormValidator();
  ?>
  <table width="600" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center">
            	<img src="images/scagenda.png"	border="0" /> Agendar
            </td>
            <td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
            <td width="50" align="center"><?php echo(linkCerrar(0)); ?></td>
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
      <td width="200" align="right" class="td_etiqueta">Fecha</td>
      <td class="td_dato">
        <?php 
		$fecha1 = new HtmlDate("fecha");
		echo($fecha1->toHtml()); 
	    ?>
      </td>
    </tr>

 	<tr>
      <td width="200" align="right" class="td_etiqueta">Titulo</td>
      <td class="td_dato">
        <?php 
		$txtTitulo = new HtmlInputText("titulo", "");
		$txtTitulo->setPlaceholder("titulo...");
		echo($txtTitulo->toHtml()); 
	    ?>
      </td>
    </tr>
    
    <tr>
      <td width="200" align="right" class="td_etiqueta">Agendar</td>
      <td class="td_dato">
        <?php 
		$txt1 = new HtmlInputTextarea("nota", $nota);
		echo($txt1->toHtml()); 
	    ?>
      </td>
    </tr>
    
    <tr>
      <td align="right" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
        <input name="id" type="hidden" id="id" value="<?php echo($id); ?>" />
        <input name="iddato" type="hidden" id="iddato" value="<?php echo($mid); ?>" />
        <input name="idquery" type="hidden" id="idquery" value="<?php echo($idquery); ?>" />
			
			<?php 
			$bok = new HtmlBotonOkCancel();
			echo($bok->toHtml());
			?>
        
      </td>
    </tr>
  </table>
  <script language="JavaScript" type="text/javascript">
	<!--
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
	//-->
	</script>
</form>
<?php include("footer.php"); ?>
</body>
</html>
