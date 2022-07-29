<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rcontrol1 = requestOrSession("control1");
$rcontrol2 = requestOrSession("control2");

$aquery = explode("|", Request("query"));
$rquery = $aquery[0];

$mfield = Request("mfield");
$mid = RequestInt("mid");
$rsearch = Request("search");

$gridSQL = Request("sql");

$cerrar = "window.close();";
$cerrarId = "linkcerrarW";

$cantRecs = 100;
$query_info = array();
$fk_cache = array();


$tc = getCache();
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else {
	$query_info = $tc->getQueryInfo($rquery);
	$qinfo = new ScQueryInfo($query_info, true);
	$tc->saveQueryObj($rquery, $qinfo);
}
saveCache($tc);
?>
<!DOCTYPE html>
<html>

<head>
	<title>Buscador de datos - por SC3</title>

	<script>
		function buscar() {
			frm = document.getElementById('form1');
			palabra = document.getElementById('search');
			if (palabra.value == "")
				alert("Ingrese palabra a buscar.")
			else {
				pleaseWait2();
				frm.submit();
			}
		}
	</script>

	<?php include("include-head.php"); ?>

</head>
<?php
$rsPpal = new BDObject();

//espacios por %
$rsearch2 = str_replace(" ", "%", $rsearch);

//quita acentos alli donde hay problemas
$filtraAcentos = getParameterInt("sc3-filtraacentos", 0);
if ($filtraAcentos == 1)
	$rsearch2 = str_replace(array('á', 'é', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), "%", $rsearch2);

$busquedaInt = 0;
if (is_numeric($rsearch2))
	$busquedaInt = $rsearch2;

//si es valor entero, reemplaza por valor entero o cero
$sql = str_replace(":FILTRO_INT", $busquedaInt, getSessionStr($gridSQL));
//reemplaza filtro por palabra buscada
$sql = str_replace(":FILTRO", $rsearch2, $sql);

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
			?>
			<a class="w3-button w3-text-white w3-right boton-fa-sup" id="<?php echo ($cerrarId); ?>" href="#" onClick="<?php echo ($cerrar); ?>">
				<i class="fa fa-times fa-lg"></i>
			</a>

		</header>
	</div>

	<form name="form1" id="form1" method="get" action="">

		<table class="dlg">
			<tr>
				<td>
					<input name="search" id="search" type="text" value="<?php echo ($rsearch); ?>" class="w3-margin-small w3-right input-buscar">
				</td>
				<td>
					<a href="javascript:buscar()" class="w3-button w3-win-phone-steel w3-margin-small w3-left">
						<i class='fa fa-search '></i> Buscar
					</a>
				</td>
			</tr>
		</table>

		<input type="hidden" name="query" value="<?php echo ("$rquery"); ?>">
		<input type="hidden" name="control1" value="<?php echo ($rcontrol1); ?>">
		<input type="hidden" name="control2" value="<?php echo ($rcontrol2); ?>">
		<input type="hidden" name="mfield" value="<?php echo ($mfield); ?>">
		<input type="hidden" name="mid" value="<?php echo ($mid); ?>">
		<input type="hidden" name="sql" value="<?php echo ($gridSQL); ?>">

	</form>


	<?php
	echo ("<table width=\"100%\" class=\"w3-table-all w3-hoverable\">\n");
	echo ("<thead><tr>");
	$i = 0;
	while ($i < $rsPpal->cantF()) {
		$fieldname = $rsPpal->getFieldName($i);
		$fieldname = str_replace("_", "<br>", $fieldname);

		$pos = strpos($fieldname, "_fk");
		if ($pos === FALSE) {
			echo ("<th align=\"center\" class=\"grid_header\"><b>");
			echo (ucfirst($fieldname));
			echo ("</b></th>");
		}
		$i++;
	}
	echo ("</tr></thead>");
	echo ("<tbody>");
	while ((!$rsPpal->EOF()) && ($cantRecs > 0)) {
		$valorKey = $rsPpal->getValue($qinfo->getKeyField());
		$valorDesc = $rsPpal->getValue($qinfo->getComboField());
		$valorDescJS = escapeJsValor($valorDesc);

		echo ("\n<tr class=\"tr_clickeable\"");
		echo (" onclick=\"javascript:selRec('" . $rcontrol1 . "', '" . $rcontrol2 . "', '" . $valorKey . "', '" . $valorDescJS . "');\" >");

		$i = 0;
		while ($i < $rsPpal->cantF()) {
			$fieldname = $rsPpal->getFieldName($i);
			$valorDesc = $rsPpal->getValue($qinfo->getComboField());


			$pos = strpos($fieldname, "_fk");
			if ($pos === FALSE) {
				$nombreCampo = $rsPpal->getFieldName($i);
				$tipoCampo = $rsPpal->getFieldType($i);
				$dataAlign = getDataAlign($nombreCampo, $tipoCampo, $qinfo->getFieldsRef());

				$class = "";
				if (sonIguales($dataAlign, "right"))
					$class .= " align-right";

				if (sonIguales($dataAlign, "left"))
					$class .= " align-left";

				if (sonIguales($dataAlign, "middle"))
					$class .= " align-middle";


				echo ("<td valign=\"top\" align=\"" . $dataAlign . "\" class=\"$class\">");
				$valorCampo = $rsPpal->getValue($i);

				if (esCampoFecha($tipoCampo) && ($i <= 2)) {
					$Day = getdate(toTimestamp($valorCampo));
					$valorCampo = Sc3FechaUtils::formatFecha($Day);
				}

				if ((strcmp($valorCampo, "") == 0) || (strcmp($valorCampo, "null") == 0))
					echo (espacio());
				else {
					if (esCampoStr($tipoCampo) || esCampoMemo($tipoCampo)) {
						if ($qinfo->isFileField($nombreCampo, $valorCampo))
							echo (sc3getImgSmall(getImagesPath(), $valorCampo, 60));
						else
							echo ($valorCampo);
					} else
				if (esCampoFecha($tipoCampo)) //tipo fecha
					{
						$Day = getdate(toTimestamp($valorCampo));
						echo (Sc3FechaUtils::formatFecha($Day, false, true));
					} else
				if (esCampoInt($tipoCampo)) //tipo INT, prueba si es una FK
					{
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
	}

	echo ("</tbody>");
	echo ("</table>\n");

	?>
	</td>
	</tr>
	</table>
	<!-- por sc3 (www.sc3.com.ar) -->
</body>

</html>