<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = RequestSafe("query");

$query_info = Array();
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);

if (enviado())
{
	$result = $qinfo->buildSelectLeftJoin();	
	$wherePart = "";
	$i = 1;
	while ($i <= 3)
	{
		if (!sonIguales(RequestSafe("field$i"), ""))
			$wherePart = addWhere($wherePart, $qinfo->buildSearch(RequestSafe("field$i"), Request("palabra$i"), RequestSafe("cond$i"), "t1"));
		$i++;	
	}
	
	//agrega los filtros construidos (y quizas la palabra WHERE)
	$result = addFilter($result, $wherePart);

	//TODO: guardar filtro !

	//guarda el sql en sessi�n y recupera key 
	$savedSql = saveSessionStr($result);

	$url = new HtmlUrl("sc-selitems.php");
	$url->add("query", $rquery);
	$url->add("sql", $savedSql);
	$url->add("orderby", RequestSafe("orderby"));
	goToPage($url->toUrl());
}
?>
<!doctype html>
<html lang="es">
<head>
<title>sc3 - buscador de <?php echo($qinfo->getQueryDescription()); ?></title>

<?php include("include-head.php"); ?>

<script language="javascript">

function buscar()
{
	f=document.getElementById('form1');
	palabra = document.getElementById('palabra');
	f.submit();
}

</script>

</head>

<body onload="javascript:document.getElementById('palabra').focus();">

<form name="form1" id="form1" method="get">
<table width="60%" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
  <tr>
    <td colspan="3" align="center" class="td_titulo">
	            <?php 
				$icon = $qinfo->getQueryIcon();
				if ($icon == "")
					$icon = "images/question.gif";
				echo(img($icon, ""));
				echo(" " . $qinfo->getQueryDescription());
			?>: buscador avanzado	</td>
  </tr>
  <tr>
    <td colspan="3" align="left" bgcolor="#F8F8FA" class="td_dato">
	</td>
  </tr>

  <tr>
    <td colspan="3" align="left" bgcolor="#F8F8FA" class="td_dato">
      Para buscar:<br /> 
	  <ul>
        <li>
          Seleccione el campo a filtrar, luego la condici&oacute;n y finalmente el valor. Tiene hasta 3 campos para completar. </li>
        <li>Ingrese las fechas en formato DD/MM/AAAA (dia/mes/a&ntilde;o), ej: 02/04/2017. </li>
	  </ul>
    </td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="td_titulo2">Filtrar por </td>
  </tr>

  <?php
$i = 1;
while ($i <= 3)
{
?>
  <tr>
    <td align="center" class="td_etiqueta"> 
	<?php 
		echo(getComboCamposArray($qinfo, "field$i", "-cualquiera-", RequestSafe("field$i"))); 
	?> 
	</td>
    <td align="center" class="td_dato">
      <?php
				$c = new HtmlCombo("cond$i", RequestSafe("cond$i"));
				$c->add("CON", "contiene");
				$c->add("IGU", "igual");
				$c->add("DIF", "diferente");
				$c->add("MAY", "mayor");
				$c->add("MEN", "menor");
				$c->add("COM", "comienza con");
				echo($c->toHtml());
				?>
    </td>
    <td align="center" class="td_dato">
      <?php
				$input = new HtmlInputText("palabra$i", Request("palabra$i"));
				echo($input->toHtml());
			?>
    </td>
  </tr>

  <?php
	$i++;
 }
 ?>
  <tr>
    <td colspan="3" align="center" class="td_titulo2">Ordenar por </td>
    </tr>
  <tr>
    <td align="left" class="td_etiqueta" colspan="3"> 
	<?php 
		echo(getComboCamposArray($qinfo, "orderby", "-cualquiera-", RequestSafe("orderby"))); 
	?> 
	</td>
</tr>	
   <tr>
    <td align="center" valign="middle" class="td_etiqueta"> </td>
    <td align="left" colspan="2" class="td_dato">
      <input name="Submit2" type="button" class="buscar" onclick="javascript:buscar()"  accesskey="b" value="Buscar" />
      <input type="hidden" name="query" value="<?php echo($rquery) ?>" />
      <input type="hidden" name="enviar" value="1" />
    </td>
  </tr>
</table>
</form>
<?php include("footer.php"); ?>
</body>
</html>
