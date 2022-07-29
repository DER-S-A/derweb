<?php
include("funcionesSConsola.php");
checkUsuarioLogueadoRoot();

$tabla = Request("tabla");


function showArray($xarray)
{
	$aResult = [];
	foreach ($xarray as $clave => $valor) {
		$aResult[] = htmlBold($clave) . ": $valor";
	}
	return implode(", ", $aResult);
}

/**
 * Arma una tabla con los datos de la tabla
 * @param ScQueryInfo $xqinfo
 */
function mostrarTabla($xtabla, $xdetallada, $xfield = "")
{
	if (!$xdetallada) {
		$divs = new HtmlDivsContainer("tabla-minima");
		$url = new HtmlUrl("");
		$url->add("tabla", $xtabla);
		$ref = "";
		if (!esVacio($xfield))
			$ref = " ($xfield)";
		$divs->add(href(div(imgFa("fa-table") . $xtabla . $ref), $url->toUrl()));

		return $divs->toHtml();
	} else {

		$divs = new HtmlExpandingAreaDiv("tabla-db", "");
		$area1 = new HtmlDivDatos($xtabla);
		$area1->setStyle("tabla-db");
		$area1->setExpandible(true);

		$tabs = new HtmlTabs2();

		$tblDef = new HtmlDivsContainer("");
		$par = new HtmlEtiquetaValor("tabla", $xtabla);
		$tblDef->add($par->toHtml());

		$rsQuerys = getRs("select q.*, m.Item
						from sc_querys q
							left join sc_menuconsola m on (q.idmenu = m.iditemmenu)
						where q.table_ = '$xtabla'
						order by q.querydescription");

		while (!$rsQuerys->EOF()) {
			$descripcion = $rsQuerys->getValue("querydescription");
			$menu = $rsQuerys->getValue("Item");
			$idquery = $rsQuerys->getId();

			$url = new HtmlUrl("sc-viewitem.php?query=sc_querysall");
			$url->add("registrovalor", $idquery);
			$url->add("stackname", "sc3_config");

			$par = new HtmlEtiquetaValor("query", href("$menu / $descripcion", $url->toUrl(), "configurartabla"));
			$tblDef->add($par->toHtml());

			$rsQuerys->Next();
		}
		$rsQuerys->close();
		$tabs->agregarSolapa("Resúmen", "fa-cube", $tblDef->toHtml());

		$sql = "show create table $xtabla";
		$bd = new BDObject();
		$bd->execQuery($sql);
		$tabs->agregarSolapa("Base de datos", "fa-database", "<pre>" . $bd->getValue("1") . "</pre>");

		$area1->add("", $tabs->toHtml());
	}
	return $area1->toHtml();
}

?>
<!doctype html>
<html lang="es">

<head>
	<title>Navegar Base - por SC3</title>

	<?php include("include-head.php"); ?>

	<script language="javascript">

	</script>

	<style>
		.tablas-master {
			background-color: #336b87;
		}

		.tablas-actual {
			background-color: #2a3132;
		}

		.tablas-detalle {
			background-color: #90afc5;
		}

		.tabla-minima {
			font-size: 14px;
			border-radius: 5px;
			margin: 5px;
			background-color: #fff;
			padding: 10px;
			color: #607d8b;
		}

		.tabla-db {
			border-radius: 5px;
			margin: auto;
			margin-top: 10px;
			margin-bottom: 10px;

			background-color: #fff;
		}

		.titulo {
			font-size: 18px;
			font-variant: small-caps;
			padding: 10px;
			color: #fff;
			background-color: #616161;
			align-items: center;
		}
	</style>

</head>

<body onload="firstFocus()">

	<div class="titulo">Tablas Referenciadas</div>

	<div class="div-container-flex tablas-master">

		<?php
		$rsMasters = getRs("SELECT distinct
								TABLE_NAME,
								COLUMN_NAME,
								REFERENCED_TABLE_NAME,
								REFERENCED_COLUMN_NAME
							FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
							WHERE TABLE_SCHEMA = SCHEMA() AND 
								REFERENCED_TABLE_NAME IS NOT NULL and
								table_name = '$tabla'
							order BY REFERENCED_TABLE_NAME");

		while (!$rsMasters->EOF()) {
			$tablaMaster = $rsMasters->getValue("REFERENCED_TABLE_NAME");
			$field = $rsMasters->getValue("COLUMN_NAME");
			echo (mostrarTabla($tablaMaster, false, $field));
			$rsMasters->Next();
		}
		$rsMasters->close();
		?>

	</div>

	<div class="div-container-flex tablas-actual">

		<?php
		echo (mostrarTabla($tabla, true));
		?>

	</div>

	<div class="titulo">Tablas detalle</div>

	<div class="div-container-flex tablas-detalle">

		<?php
		$rsDet = getRs("SELECT 
							TABLE_NAME,
							COLUMN_NAME,
							REFERENCED_TABLE_NAME,
							REFERENCED_COLUMN_NAME
						FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
						WHERE TABLE_SCHEMA = SCHEMA() AND 
							TABLE_NAME IS NOT NULL and
							REFERENCED_TABLE_NAME = '$tabla'
						order BY TABLE_NAME");

		while (!$rsDet->EOF()) {
			$tablaDet = $rsDet->getValue("TABLE_NAME");
			$field = $rsDet->getValue("COLUMN_NAME");
			echo (mostrarTabla($tablaDet, false, $field));
			$rsDet->Next();
		}
		$rsDet->close();
		?>

	</div>

	<?php include("footer.php"); ?>

</body>

</html>