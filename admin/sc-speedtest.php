<?php 
include("funcionesSConsola.php");

$error = "";

$pdf = new HtmlPdf("Clientes", "a4", true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Test de velocidad</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">

  <table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" class="dlg">
    <tr>
      <td colspan="3" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><? echo(getOpTitle(Request("opid"))); ?></td>
            <td width="40" align="center"> <? echo(linkImprimir()); ?> </td>
            <td width="40" align="right"><? echo(linkCerrar(0)); ?></td>
          </tr>
        </table>
      </td>
    </tr>	

    <tr>
      <td colspan="2" class="td_dato">
		<?php
		$sql = "select id, nombre, telefono, celular, direccion, email
		 		from gen_personas 
				order by nombre 
				limit 30";
		$bd = new BDObject();
		$bd->execQuery($sql);
		
//		echo($bd->getAffectedRows());
		
		$grid = new HtmlGrid($bd);
		$grid->setTitle("Clientes y contactos");
		$grid->setWithAll();
		echo($grid->toHtml());
		
		$pdf->addGrid($grid);
		?>
      </td>
    </tr>

	<tr>
		<td align="right" class="td_etiqueta" width="200">resultado:</td>
		<td class="td_dato" colspan="2">
		<? 
		echo($pdf->toLink($mid)); 
		?>
		</td>
	</tr>

	<tr>
      <td align="right" class="td_etiqueta" width="200">&nbsp;</td>
      <td class="td_dato">
	      <?php 
	      $bok = new HtmlBotonOkCancel();
	      echo($bok->toHtml());
	      ?>
      </td>
    </tr>
  </table>
  
<? include("footer.php"); ?>

</body>
</html>