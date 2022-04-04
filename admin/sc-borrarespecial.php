<?php 
include("funcionesSConsola.php");

checkUsuarioLogueado();

$error = "";
$mid = RequestInt("mid");
$mquery = Request("mquery");
$qinfo = getQueryObj($mquery);

if (enviado())
{
	$sec = new SecurityManager();
	$rs = $sec->getRsOperacionesRelacionadasParaBorrar($qinfo->getQueryId());

	$aSql = array();
	
	while (!$rs->EOF())
	{
		$count = "";
	
		$detQuery = $rs->getValue("queryname");
		//metadata: recupera de la cache el obj del query info
		$qinfoDetalle = getQueryObj($detQuery);
		$sql2 = $qinfoDetalle->getRecordCount($rs->getValue("mfield"), $mid);
	
		$rsCount = new BDObject();
		$rsCount->execQuery($sql2);
		$count = $rsCount->getValue("cant");
	
		if ($count > 0)
		{
			$aSql[] = "delete from " . $rs->getValue("tabla") . 
						" where " . $rs->getValue("mfield") . " = $mid";
		}
		$rs->Next();
	}

	$sql = "delete from " . $qinfo->getQueryTable() . 
			" where " . $qinfo->getKeyField() . " = " . $mid;

	$bd =  new BDObject();
	$bd->beginT();
	$bd->execQuerysInArray($aSql, array());
	$bd->execQuery($sql);
	$bd->commitT();
	
	setMensaje("El articulo se ha borrado con exito.");
	goOnAnterior();
}

?>
<!doctype html>
<html lang="es">
<head>
<title>Borrado especial</title>

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
            <td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
            <td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
            <td width="50" align="center"><?php echo(linkCerrar(0)); ?></td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td class="td_etiqueta">A borrar:</td>
      <td class="td_dato">
        <?php 
        $sec = new SecurityManager();
        
        $rs = $sec->getRsOperacionesRelacionadasParaBorrar($qinfo->getQueryId());
        
        $i = 0;
        while (!$rs->EOF())
        {
        	$res = "";
        	
        	$count = "";

        	$detQuery = $rs->getValue("queryname");
       		//metadata: recupera de la cache el obj del query info
       		$qinfoDetalle = getQueryObj($detQuery);
       		$sql2 = $qinfoDetalle->getRecordCount($rs->getValue("mfield"), $mid);
        
       		$rsCount = new BDObject();
       		$rsCount->execQuery($sql2);
       		$count = $rsCount->getValue("cant");
       		$count2 = " (" . $count . ")";

       		if ($count > 0)
       		{
	        	$icon = $rs->getValue("icon");
	        	if (esVacio($rs->getValue("icon")))
	        		$icon = "images/table.png";
	        
	        	$res .= img($icon, "");
	        	$res .= " " . $rs->getValue("querydescription") . $count2;
	
	        	echo($res . "<br/>");
       		}
       		        	
        	$rs->Next();
        	$i++;
        }
        
        
        ?>
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