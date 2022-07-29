<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$que = "";
$word = Request("word");
$words = explode(" ", $word);
if (sizeof($words) == 1)
	$cual = $words[0];
if (sizeof($words) == 2)
{
	$que = $words[0];
	$cual = $words[1];
}
if (sizeof($words) == 3)
{
	$que = $words[0];
	$cual = $words[1] . " " . $words[2];
}	

?>
<!doctype html>
<html lang="es">
<head>
<title>sc3 - buscar</title>

<?php include("include-head.php"); ?>

</head>

<body onload="javascript:document.getElementById('word').focus();">

<table class="dlg">

  <tr>
    <td height="40" align="center" valign="middle" class="td_dato">
      <form action="sc-buscar.php" method="get">
        <input type="text" name="word" id="word" size="40" maxlength="80" title="ej: gonzales, cliente gonzales, cheque 2398765, etc." value="<?php echo($word); ?>"/>
        <input type="submit" name="Submit" value="Buscar" />
      </form>
    </td>
  </tr>

  <tr>
    <td align="left" class="td_dato">Resultado de la b&uacute;squeda, usando que: <strong><?php echo($que); ?></strong> cu&aacute;l: <strong><?php echo($cual); ?></strong> </td>
  </tr>
  <?php
  		//recupera los querys que tiene acceso el usuario
		$sec = new SecurityManager();
		$rs = $sec->getRsQuerys("", $que, true);
		$total = 0;
		while (!$rs->EOF())
		{
			debug("sc-buscar.php: buscando '$cual' en ". $rs->getValue("queryname"));
			$qinfo = getQueryObj($rs->getValue("queryname"), false);
			$cant = $qinfo->cantResultados($cual);
			$total += $cant;
			if ($cant > 0)
			{
			?>
				<tr>
				  <td class="td_dato">
					<a href="sc-selitems.php?query=<?php echo($rs->getValue("queryname")); ?>&palabra=<?php echo($cual); ?>&todesktop=1">
							<img src="<?php echo($qinfo->getQueryIcon()); ?>" /> <?php echo($rs->getValue("querydescription")); ?>
					</a> 
					-  <b><?php echo($cant); ?></b> resultados encontrados
				  </td>
			  	</tr>
			<?php			
			}
			$rs->Next();
		}
	  ?>

  <tr>
    <td class="td_dato">
	<b><?php echo($total); ?></b> resultados encontrados
	</td>
  </tr>

</table>
<?php include("footer.php"); ?>
</body>
</html>