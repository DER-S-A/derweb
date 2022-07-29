<?php 
include("funcionesSConsola.php");
checkUsuarioLogueado();

$error = "";

$nota = "";
$color = "";
$adjunto1 = "";
$adjunto2 = "";
$id = "";

//una pila con otro nombre indica que está en una solapa con su propia pila
$stackname = Request("stackname");

$insert = RequestInt("insert");
if (enviado())
{
	$borrar = RequestInt("borrar");
	
	if ($insert == 1 && ($borrar == 0))
	{
		$values = array();
		$values['usuario'] = getCurrentUserLogin();
		$sql = insertIntoTable("qscadjuntos", $values);
	}
	else
	{
		$id = RequestInt("id");
		$nota = Request("nota");
		$color = Request("color");
		$adjunto1 = Request("adjunto1");
		$adjunto2 = Request("adjunto2");
		$usuario = getCurrentUserLogin();
		
		if ($borrar == 0)
			$sql = "update sc_adjuntos 
					set color = '$color', nota = '$nota', adjunto1 = '$adjunto1', adjunto2 = '$adjunto2', usuario = '$usuario' 
					where id = $id";
		else 
			$sql = "delete from sc_adjuntos
					where id = $id";
	}
	
	$sent = new BDObject();
	$sent->execQuery($sql);
	
	goOn($stackname);
}
else
{
	$mid = RequestInt("mid");
	$mquery = Request("mquery");
	$qinfo = getQueryObj($mquery);
	$idquery = $qinfo->getQueryId();
	$rs = locateRecordWhere("sc_adjuntos", "iddato = $mid and idquery  = $idquery");
	if ($rs->EOF())
	{
		$insert = 1;
		$color = "#ffffff";
	}
	else
	{
		$id = $rs->getValue("id");
		$nota = $rs->getValue("nota");
		$color = $rs->getValue("color");
		$adjunto1 = $rs->getValue("adjunto1");
		$adjunto2 = $rs->getValue("adjunto2");
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Notas</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">
<form method="post" name="form1" id="form1">

  <?php
  $req = new FormValidator();
  ?>
  
  <table width="600" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center">Notas</td>
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
      <td width="200" align="right" class="td_etiqueta">Nota:</td>
      <td class="td_dato">
        <?php 
		$txt1 = new HtmlInputTextarea("nota", $nota);
		echo($txt1->toHtml()); 
	    ?>
      </td>
    </tr>
    
    <tr>
      <td align="right" class="td_etiqueta">Color:</td>
      <td class="td_dato">
        <?php 
		$color1 = new HtmlColor("color", $color);
		echo($color1->toHtml()); 
		?>
      </td>
    </tr>
    
     <tr>
      <td align="right" class="td_etiqueta">Adjunto 1:</td>
      <td class="td_dato">
        <?php
        $f1 = new HtmlInputFile("adjunto1", $adjunto1);
        //$f1->setShowImage(false);
        echo($f1->toHtml()); 
		?>
      </td>
    </tr>
    
    <tr>
      <td align="right" class="td_etiqueta">Adjunto 2:</td>
      <td class="td_dato">
        <?php
        $f2 = new HtmlInputFile("adjunto2", $adjunto2);
		//$f2->setShowImage(false);
        echo($f2->toHtml()); 
		?>
      </td>
    </tr>
    
    <tr>
      <td align="right" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
        <input name="enviar" type="hidden" id="enviar" value="1" />
        <input name="insert" type="hidden" id="insert" value="<?php echo($insert); ?>" />
        <input name="stackname" type="hidden"  value="<?php echo($stackname);  ?>" />
        <input name="id" type="hidden" id="id" value="<?php echo($id); ?>" />
        <input name="iddato" type="hidden" id="iddato" value="<?php echo($mid); ?>" />
        <input name="borrar" type="hidden" id="borrar" value="0" />
        <input name="idquery" type="hidden" id="idquery" value="<?php echo($idquery); ?>" />

        <input type="button" value="Aceptar [F8]" name="bsubmit" id="bsubmit" class="btn-success" accesskey="1"  onclick="javascript:submitForm()" />
        
        <input type="button" value="Cancelar" name="bcancelar" class="btn-warning" accesskey="0" onclick="javascript:document.location = 'hole.php?anterior=0&stackname=<?php echo($stackname); ?>'" />
        <input type="button" value="Borrar nota" name="bblanquear" class="btn-warning" onclick="javascript:blanquearNota()" />
       </td>
    </tr>
  </table>
  <script language="JavaScript" type="text/javascript">
	<!--
	<?
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		//make sure hidden and iframe values are in sync for all rtes before submitting form
		updateRTEs();
		if (validar())
			document.getElementById('form1').submit();
	}
	
	function blanquearNota() 
	{
		document.getElementById('nota').value = '';
		document.getElementById('color').value = '';
		document.getElementById('adjunto1').value = '';
		document.getElementById('adjunto2').value = '';
		document.getElementById('borrar').value = '1';
		
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
