<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueado();

$error = "";

$rquery = Request("query");

//busca la definicion y el obj en la cache ----------------------------
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else
{
	$qinfo = new ScQueryInfo($query_info);
	$tc->saveQueryObj($rquery, $qinfo);	
}	
saveCache($tc);
//------fin recuperar qinfo ------------------------------------------


$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("sc3FiltroAsociado", "sc-reportar-ajax.php");
sc3SaveAjaxHelper($ajaxH);

$sqlCampos = "select field_, 
					case when ifnull(grupo, '') = '' then show_name 
					else concat(upper(grupo), ' - ', show_name) end as campo
				from sc_fields
				where visible = 1 and 
					ifnull(password_field, 0) = 0 and
					idquery = " . $qinfo->getQueryId() . 
				" order by grupo, campo";

$rsFields = getRs($sqlCampos);

$cboCampos = new HtmlCombo("listacampos", "");
$cboCampos->setClass("oculto");
$cboCampos->addSeleccione();
$cboCampos->cargarRs($rsFields, "field_", "campo");
$rsFields->close();

?>
<!doctype html>
<html lang="es">
<head>
<title>Reportes - por SC3</title>

<?php include("include-head.php"); ?>

<script type="text/javascript" src="<?php echo(sc3CacheButer("scripts/sc-reportar.js")); ?>"></script>

<script language="javascript">

var queryname = '<?php echo($rquery);?>';

</script>

</head>
<body onload="firstFocus()">
<form method="post" name="form1" id="form1" action="sc-reportar-grid.php" target="resultado">
<?php
$req = new FormValidator();
?>

<header class="w3-container headerTitulo">
	<?php 
	$icon = $qinfo->getQueryIcon();
	echo(img($icon, ""));
	echo(" Consultar " . $qinfo->getQueryDescription());

	echo($cboCampos->toHtml());

	$hidQuery = new HtmlHidden("query", $rquery);
	echo($hidQuery->toHtml());

	$hidEnviar = new HtmlHidden("enviar", 1);
	echo($hidEnviar->toHtml());
	?>
</header>

<?php

$tab = new HtmlTabs2();
$tab->setId("tabreporte");

$camposMuestra = "";
//lista de grupos posibles
$aGrupos = getGruposArray($qinfo->getFieldsDef());
$camposGrilla = $qinfo->getQueryFields();

$sql = $qinfo->buildSelectLeftJoin(true);
$sql .= " limit 0";
$rsPpal = getRs($sql);

$cantGrupos = 0;
//recorre los grupos de datos
foreach ($aGrupos as $grupoActual)
{
	$area1 = new HtmlDivDatos($grupoActual);
	$area1->setExpandible(true);
	$i = 0;

	//recorre todos los campos y solo muestra los del grupo = $grupoActual
	while ($i < $rsPpal->cantF())
	{
		$nombreCampo = ($rsPpal->getFieldName($i));
		$fieldGroup = $qinfo->getFieldGrupo($nombreCampo);
		$visible = $qinfo->getFieldVisible($nombreCampo);
			
		$pos = strpos($nombreCampo, "_fk");
		if ($pos === FALSE && ($visible == 1))
		{
			//analiza si el campo esta en el grupo que estoy mostrando
			if (sonIguales($grupoActual, $fieldGroup)
					|| (sonIguales("", $fieldGroup) && sonIguales("Datos", $grupoActual)))
			{
				$etiqueta = htmlVisible($qinfo->getFieldCaption($nombreCampo));

				$valor = 0;
				// si está en el listado general lo tilda
				if (strContiene($camposGrilla, $nombreCampo))
					$valor = 1;

				$bMuestra = new HtmlBoolean2("muestra_$nombreCampo", $valor);
				$area1->add($etiqueta . ":", $bMuestra->toHtml());
			}
		}
		$i++;
	}
	
	if ($cantGrupos > 1)
		$area1->setExpandida(false);
	$camposMuestra .= $area1->toHtml();

	$cantGrupos++;
}	
$rsPpal->close();

$tab->agregarSolapa("Campos a mostrar", "fa-table", $camposMuestra);


$tab->agregarSolapa("Filtros", "fa-filter", '<div class="barra-buscador" id="barra-buscador">
											<a href="javascript:busquedaAvanzada()" class="w3-margin-small btn-flat btn-action" title="Búsqueda detallada">
												<i class="fa fa-search-plus fa-lg"></i> Agregar filtro
											</a>

											<a href="javascript:buscar()" class="w3-margin-small w3-right btn-flat btn-success" title="">
												<i class="fa fa-search fa-lg"></i> Buscar
											</a>
											</div>');


$tab->agregarSolapa("Resultado", "fa-print", '<iframe src="sc-reportar-grid.php?query=' . $rquery . '" name="resultado" frameborder="0" width="100%" height="500"></iframe>');

echo($tab->toHtml());
?>



	<script type="text/javascript">  

	<?php
	echo($req->toScript());
	?>
	
	function buscar() 
	{
		if (validar())
		{
			document.getElementById('form1').submit();
			openSolapa(null, 'tabreporte', '2');
		}
	}
	
	</script>
	
</form>
<?php include("footer.php"); ?>
</body>
</html>
