<? require("funcionesSConsola.php"); ?>
<?
checkUsuarioLogueado();

$rquery = RequestSafe("query");

$query_info = Array();
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);

if (enviado())
{
	$campos = implode(", ", RequestAll("fields2"));
	echo($campos);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>sc3 - configurador de <? echo($qinfo->getQueryDescription()); ?></title>

<? include("include-head.php"); ?>

<script language="javascript">

	function buscar()
	{
		f=document.getElementById('form1');
		f.submit();
	}

</script>

<link rel="stylesheet" href="sc.css" type="text/css">
</head>
<body>

<form name="form1" id="form1" method="get">
<table width="60%" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
  <tr>
    <td colspan="3" align="center" class="td_titulo">
         <? 
		$icon = $qinfo->getQueryIcon();
		echo(img($icon, ""));
		echo(" " . $qinfo->getQueryDescription());
	?>: configurador</td>
  </tr>

  <tr>
    <td colspan="3" align="left" bgcolor="#F8F8FA" class="td_dato">
		Seleccione los campos que desea ver en el listado      
    </td>
  </tr>

  <tr>
    <td colspan="3" align="center" class="td_titulo2">Columnas a mostrar en el listado</td>
  </tr>

  <tr>
    <td width="40%" align="center" class="td_etiqueta"> 
	<? 
		$sfields = new HtmlCombo("fields1", "");
		$sfields->setMultiple();
		$allFields = $qinfo->getListaCamposCompleta();
		
		$sfields->cargarArray($allFields);
		
		echo($sfields->toHtml());
	?> 
	</td>
    <td width="20%" align="center" class="td_dato">
    
			<input type="button" value="--&gt;"
				 onclick="moveOptions(document.getElementById('fields1'), document.getElementById('fields2'));" /><br />
			<input type="button" value="&lt;--"
				 onclick="moveOptions(document.getElementById('fields2'), document.getElementById('fields1'));" />

    </td>
    <td width="40%" align="center" class="td_dato">
      <?
      	$sfields2 = new HtmlCombo("fields2", "");
		$sfields2->setMultiple();
		
		echo($sfields2->toHtml());
      ?>
    </td>
  </tr>

 <tr>
    <td colspan="3" align="center" class="td_titulo2">Ordenar por </td>
    </tr>
  <tr>
    <td align="left" class="td_etiqueta" colspan="3"> 
	<? 
		echo(getComboCamposArray($qinfo, "orderby", "-cualquiera-", RequestSafe("orderby"))); 
	?> 
	</td>
 </tr>	

   <tr>
    <td align="center" valign="middle" class="td_etiqueta"> </td>
    <td align="left" colspan="2" class="td_dato">
      <input name="Submit2" type="button" class="buscar" onclick="javascript:buscar()"  accesskey="b" value="Buscar">
      <input type="hidden" name="query" value="<? echo($rquery) ?>">
      <input type="hidden" name="enviar" value="1">
    </td>
  </tr>
</table>
</form>
<? include("footer.php"); ?>
</body>
</html>
