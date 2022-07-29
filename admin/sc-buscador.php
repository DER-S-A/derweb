<? require("funcionesSConsola.php"); ?>
<? checkUsuarioLogueado() ?>
<?


?>
<!doctype html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>sc3 - buscador</title>
<link href="sc.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form method="get" name="form1" id="form1" action="sc-selitems.php">
  <table width="150" border="0" align="center" cellpadding="1" cellspacing="1" class="dlg">
    <tr>
      <td class="td_etiqueta"><span class="td_dato">
        <?
		$sec = new SecurityManager();
		$rs = $sec->getRsQuerys();
		$combo = new HtmlCombo("query", "");
		$combo->valueFromRequest();
		while (!$rs->EOF())
		{
			$icon = $rs->getValue("icon");
			if (sonIguales($icon, ""))
				$icon = "images/table.png";
			$combo->add($rs->getValue("queryname"), $rs->getValue("querydescription"), $icon); 
			$rs->Next();
		}
		  
		echo($combo->toHtml());  
	  ?>
      </span></td>
    </tr>
    <tr>
      <td class="td_etiqueta"><span class="td_dato">
        <?
	  	$input = new HtmlInputText("palabra", "");
		$input->valueFromRequest();
		$input->setSize(20);
		echo($input->toHtml());
	  ?>
      </span></td>
    </tr>
    <tr>
      <td class="td_etiqueta" align="center">
        <input type="submit" value="Buscar" name="bbuscar" class="buscar" accesskey="1" />
        
        <input type="hidden" name="fstack" value="1" /></td>
    </tr>
  </table>
</form>
<? include("footer.php"); ?>
</body>
</html>