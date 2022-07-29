<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

$sec = new SecurityManager();

//variables del Request
$query = Request("query");
$aquery = explode("|", $query);
$rquery = $aquery[0];
$filtername = "";
if (count($aquery) > 1)
	$filtername = $aquery[1];
$filterId = 0;

$rcontrol1 = requestOrSession("control1");
$rcontrol2 = requestOrSession("control2");
$orderby = RequestSafe("orderby");

$extendedFilter = getSession($rcontrol1 . "-eqf");

$mfield = Request("mfield");
$mid = RequestInt("mid");
$rsearch = Request("search");
if (strcmp($rquery, "") == 0)
	echo ("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=queryname</b>");
$rtop = requestOrSession("top");
if ($rtop == "")
	$rtop = 20;

//analiza si viene de Info
$info = RequestInt("info");
$cerrar = "window.close();";
$cerrarId = "linkcerrarW";
$cantRecs = 100;
if ($info == 1) {
	$cerrar = "verUrl('sc-info.php');";
	$cerrarId = "botonCerrar";
	$cantRecs = 300;
}
$query_info = array();

$tc = getCache();
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else {
	$query_info = $tc->getQueryInfo($rquery);
	$qinfo = new ScQueryInfo($query_info, true);
	$tc->saveQueryObj($rquery, $qinfo);
}
saveCache($tc);

if (!sonIguales($filtername, "")) {
	$filterId = $qinfo->getFilterId($filtername);
}

?>
<!DOCTYPE html>
<html>

<head>

	<title>SC3 - buscador de datos</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<script>
		function buscar() {
			frm = document.getElementById('form1');
			palabra = document.getElementById('search');
			if (palabra.value == "")
				alert("Ingrese palabra a buscar.")
			else
				frm.submit();
		}
	</script>

	<?php include("include-head.php"); ?>

</head>
<?php
//la vista minima es el campo ID y el campo Combo, es lo que puede ver si no tiene permisos de ver nada
$vistaMinima = false;
if (!$sec->tienePermisoQuery($rquery))
	$vistaMinima = true;

//arma el  con el sql de la consulta
$sql = $qinfo->getQuerySql2("", $rsearch, $orderby, "", "", "", $mid, $mfield, $filterId, $extendedFilter, false, $vistaMinima);
$rsPpal = new BDObject();
$rsPpal->execQuery($sql);
$rows = $rsPpal->cant();
?>

<body onLoad="javascript:document.getElementById('search').focus();sc3SelectAll('search');">

	<div class="">
		<header class="headerTitulo">
			<?php
			$icon = $qinfo->getQueryIcon();
			if ($icon == "")
				$icon = "images/table.png";
			echo (img($icon, ""));
			echo (" " . $qinfo->getQueryDescription());
			if (!sonIguales($filtername, ""))
				echo (" ($filtername)");
			?>
			<a class="w3-button w3-text-white w3-right boton-fa-sup" id="<?php echo ($cerrarId); ?>" href="#" onClick="<?php echo ($cerrar); ?>">
				<i class="fa fa-times fa-lg"></i>
			</a>

		</header>
	</div>

	<form name="form1" method="get" action="" id="form1">

		<table class="dlg">
			<tr>
				<td>
					<input name="search" id="search" type="text" value="<?php echo ($rsearch); ?>" class="w3-margin-small w3-right input-buscar">
				</td>
				<td>
					<a href="javascript:buscar()" class="w3-win-phone-steel w3-margin-small w3-right btn-flat">
						<i class='fa fa-search '></i> Buscar
					</a>
				</td>
			</tr>
		</table>

		<input type="hidden" name="query" value="<?php echo ("$rquery|$filtername"); ?>">
		<input type="hidden" name="control1" value="<?php echo ($rcontrol1); ?>">
		<input type="hidden" name="control2" value="<?php echo ($rcontrol2); ?>">
		<input type="hidden" name="info" value="<?php echo ($info); ?>">
		<input type="hidden" name="mfield" value="<?php echo ($mfield); ?>">
		<input type="hidden" name="mid" value="<?php echo ($mid); ?>">
	</form>

	<?php
	echo ("<table width=\"100%\" class=\"w3-table-all w3-hoverable\">\n");
	echo ("<thead><tr>");
	$i = 1;
	while ($i < $rsPpal->cantF()) {
		$fieldname = $rsPpal->getFieldName($i);

		$pos = strpos($fieldname, "_fk");
		if ($pos === FALSE) {
			$url = new HtmlUrl("sc-opencatalog.php");
			$url->add("query", $query);
			$url->add("orderby", $fieldname);
			$url->add("search", $rsearch);
			$url->add("control1", $rcontrol1);
			$url->add("control2", $rcontrol2);
			$url->add("mfield", $mfield);
			$url->add("mid", $mid);

			echo ("<th align=\"center\" class=\"grid_header\"><b>");
			echo (href($qinfo->getFieldCaption($fieldname), $url->toUrl()));
			echo ("</b></th>");
		}
		$i++;
	}
	echo ("</tr></thead>");
	echo ("<tbody>");
	while ((!$rsPpal->EOF()) && ($cantRecs > 0)) {
		$valorKey = $rsPpal->getValue($qinfo->getKeyField());
		$valorDesc = escapeJsValor($rsPpal->getValue($qinfo->getComboField()));
		$valorDescJS = escapeJsValor($valorDesc);

		echo ("\n<tr class=\"tr_clickeable\"");
		echo (" onclick=\"javascript:selRec('" . $rcontrol1 . "', '" . $rcontrol2 . "', '" . $valorKey . "', '" . $valorDescJS . "');\" >");

		$i = 1;
		while ($i < $rsPpal->cantF()) {
			$fieldname = $rsPpal->getFieldName($i);

			$pos = strpos($fieldname, "_fk");
			if ($pos === FALSE) {
				$valorDesc = '';
				$nombreCampo = $rsPpal->getFieldName($i);
				$tipoCampo = $rsPpal->getFieldType($i);
				$dataAlign = getDataAlign($nombreCampo, $tipoCampo, $qinfo->getFieldsRef());
				echo ("<td valign=\"top\" align=\"" . $dataAlign . "\">");
				$valorCampo = $rsPpal->getValue($i);

				//para armar click al apretar
				if ($i <= 2) {
					if (sonIguales($nombreCampo, $qinfo->getComboField())) {
						$valorDesc = escapeJsValor($valorCampo);

						if (esCampoFecha($tipoCampo)) {
							$Day = getdate(toTimestamp($valorCampo));
							$valorCampo = Sc3FechaUtils::formatFecha($Day);
							$valorDesc = $valorCampo;
						}
					}
				}

				if ((strcmp($valorCampo, "") == 0) || (strcmp($valorCampo, "null") == 0))
					echo (espacio());
				else {
					if ($qinfo->isFileField($nombreCampo))
						echo (sc3getImgSmall(getImagesPath(), $valorCampo, 50));
					else
				if (esCampoStr($tipoCampo) || esCampoMemo($tipoCampo)) {
						if (strpos($nombreCampo, "email") !== FALSE) {
							//$emailUrl = "<img src=\"images/enviar.gif\" alt=\"$valorCampo\" title=\"$valorCampo\" border=\"0\"/>";
							$emailUrl = imgFa("fa-envelope-o", "fa-2x", "gris", $valorCampo);
							$link = "mailto:" . $valorCampo;
							echo (href($emailUrl, $link));
						} else
							echo ($valorCampo);
					} else
				if (esCampoFecha($tipoCampo)) //tipo fecha
					{
						$Day = getdate(toTimestamp($valorCampo));
						echo (Sc3FechaUtils::formatFecha($Day, true, true));
					} else
				if (esCampoInt($tipoCampo)) //tipo INT, prueba si es una FK
					{
						//echo(getFKValue2($nombreCampo, $valorCampo, $qinfo->getFieldsRef(), $fk_cache, false));
						if (array_key_exists($nombreCampo, $qinfo->getFieldsRef()))
							echo ($rsPpal->getValue($nombreCampo . "_fk"));
						else
							echo ($valorCampo);
					} else
				if (esCampoBoleano($tipoCampo)) //tipo booleano
					{
						if ($valorCampo == 1)
							echo ("Si");
						else
							echo ("No");
					} else
				if (esCampoFloat($tipoCampo)) {
						echo (formatFloat($valorCampo));
					} else
				if (strcmp($nombreCampo, "Clave") == 0)
						echo ("********");
					else
						echo ($valorCampo);
				}
			}
			$i++;
			echo ("</td>");
		}

		echo ("</tr>");
		$rsPpal->Next();
		$cantRecs--;
	}
	echo ("</tbody>");
	echo ("</table>\n");

	if ($cantRecs == 0)
		echo ("Refine su busqueda.");

	if ($rows == 0 && $info == 0 && $qinfo->canInsert()) {
		$strInsert = "<br /><br /> Si no encuentra el dato y lo busco lo suficiente: <a accesskey=\"n\" title=\"Insertar nuevo dato\" href=\"";

		$url = new HtmlUrl("sc-edititem.php");
		$url->add("insert", "1");
		$url->add("modocatalog", "1");
		$url->add("query", $qinfo->getQueryName());

		$strInsert .= $url->toUrl() . "\">";
		$strInsert .= "<img src='./images/nuevo.png' border=0> Nuevo</a>";
		$strInsert .= "<br/><br/>";
		echo ($strInsert);
	}
	?>


	<!-- por sc3 (www.sc3.com.ar) -->
</body>

</html>