<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = RequestSafe("query");

$query_info = Array();
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);

$f1 = Request("f1");
$field1 = RequestSafe("field1");
$field2 = RequestSafe("field2");

$sql = "";
if (enviado())
{
	$sql = $qinfo->buildLeftJoinForGroupBy(RequestSafe("field1"), RequestSafe("field2"), $f1);	
}
?>
<!doctype html>
<html lang="es">
<head>
<title>SC3 - analisis de <?php echo($qinfo->getQueryDescription()); ?></title>

<?php include("include-head.php"); ?>

<script language="javascript">

	function buscar()
	{
		f = document.getElementById('form1');
		f.submit();
	}


</script>

<style type="text/css">

.div-controles-listado
{
	padding: 5px;
	background-color: #9c88ff;
}

</style>

</head>
<body>

<form name="form1" id="form1" method="get" target="grafico" action="sc-groupitems-grafico.php">

<div class="td_titulo">
	<?php 
		$icon = $qinfo->getQueryIcon();
		if ($icon == "")
			$icon = "images/question.gif";
		echo(img($icon, ""));
		echo(" " . $qinfo->getQueryDescription());
	?>: an&aacute;lisis avanzado

</div>

<div class="div-controles-listado">
	<?php 

	$aDiv = array();
	$p1 = new HtmlEtiquetaValor("Agrupar por", $qinfo->getComboCamposAgrupar("field1", "-ninguno-", RequestSafe("field1")));
	$aDiv[] = $p1->toHtml();
	$p1 = new HtmlEtiquetaValor("Luego por", $qinfo->getComboCamposAgrupar("field2", "-ninguno-", RequestSafe("field2")));
	$aDiv[] = $p1->toHtml();

	$p1 = new HtmlEtiquetaValor("Calcular", $qinfo->getComboCamposAgrupar2("f1", $f1));
	$aDiv[] = $p1->toHtml();

	$cgtype = new HtmlGraphicType("gtype");
	$p1 = new HtmlEtiquetaValor("Grafico", $cgtype->toHtml()); 
	$aDiv[] = $p1->toHtml();

	echo(implode(" ", $aDiv));
	?>
</div>

<div class="div-botones">
	<input name="Submit2" type="button" class="buscar" onclick="javascript:buscar()"  accesskey="b" value="Analizar" />
	<input type="hidden" name="query" value="<?php echo($rquery) ?>" />
	<input type="hidden" name="enviar" value="1" />
</div>

<iframe src="sc-groupitems-grafico.php" name="grafico" style="width: 100%; height: 500px" frameborder="0"></iframe>

<?php include("footer.php"); ?>

</body>
</html>