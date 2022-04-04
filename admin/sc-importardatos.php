<?php
include("funcionesSConsola.php");
checkUsuarioLogueadoRoot();

$query = Request("query");
$error = "";

$aBorrados = [];

$MAX = getParameterInt("sc-max-importar", 10000);


/**
 * Toma un CSV en el que coinciden las columnas con la tabla y los importa
 */
function scImportarArchivo($xarchivo, $xquery, $xseparador)
{
	global $MAX;

	$file1 = Sc3FileUtils::sc3SaveFile($xarchivo);
	$gestor = fopen($file1, "r");

	if ($gestor === FALSE) {
		return "Error al abrir archivo $file1";
	}

	$qinfo = getQueryObj($xquery);
	$tabla = $qinfo->getQueryTable();

	//campos existentes en la tabla, toma de la estructura
	$aCamposReales = getFieldsInArray($tabla, "");

	//ARMA arreglo de campos para el insert
	$aCampos = [];
	$aFieldsInfo = $qinfo->getFieldsDef();
	foreach ($aFieldsInfo as $i => $aFieldDef) {
		//Valida existencia en campos de la tabla
		$field = $aFieldDef["field_"];
		if (in_array($field, $aCamposReales))
			$aCampos[] = $field;
	}

	$cabeceraInsert = "insert into $tabla (" . implode(", ", $aCampos) . ")";

	$bd = new BDObject();
	$fila = 1;
	while (($aDatos = fgetcsv($gestor, 1000, $xseparador)) !== FALSE && ($fila <= $MAX)) {

		//ignora dos primeras filas de encabezado
		if ($fila > 1) {

			$aValues = [];
			foreach ($aDatos as $i => $valor) {
				if (esVacio($valor))
					$aValues[] = "null";
				else
					$aValues[] = comillasSql($valor);
			}

			$bd->execQuery($cabeceraInsert . " values (" . implode(", ", $aValues) . ")");
		}
		$fila++;
	}

	fclose($gestor);
	sc3UpdateTableChecksum($tabla);
	return $fila;
}

/**
 * Genera un CSV modelo de importación, con todas las columnas
 */
function generarArchivoModelo($xquery)
{
	global $aBorrados;
	//Contenido del archivo
	$aFilas = [];
	$aCabecera = [];

	$qinfo = getQueryObj($xquery);
	$tabla = $qinfo->getQueryTable();
	$idquery = $qinfo->getQueryId();
	$descrip = $qinfo->getQueryDescription();
	$filename = sc3CsvFilename("importar-" . $descrip, 0);

	$aFieldsInfo = $qinfo->getFieldsDef();

	//campos existentes en la tabla, toma de la estructura
	$aCamposReales = getFieldsInArray($tabla, "");

	$bd = new BDObject();
	$requiereFlushCache = false;
	$aBorrados = [];
	foreach ($aFieldsInfo as $i => $aFieldDef) {

		$field = $aFieldDef["field_"];
		if (in_array($field, $aCamposReales)) {

			$campo = $aFieldDef["show_name"];
			if ($aFieldDef["is_required"] == 1)
				$campo .= "*";
			$aCabecera[] = $campo;
		} else {
			$requiereFlushCache = true;
			$aBorrados[] = $field;
			$bd->execQuery("delete from sc_fields 
							where idquery = $idquery and 
								field_ = '$field'");
		}
	}

	$bd->close();

	//se borraron campos, flush de la caché de querys
	if ($requiereFlushCache) {
		$tc = getCache();
		$tc->flushCache();
		saveCache($tc);
	}

	sc3CsvSaveArray($filename, $aCabecera, $aFilas);

	$pathInfo =  pathinfo($filename);
	return "tmp/" . $pathInfo["filename"] . "." . $pathInfo["extension"];
}


if (enviado()) {
	$archivo = Request("archivo");
	$sep = Request("separador");

	//importa archivo
	$filas = scImportarArchivo($archivo, $query, $sep);
	setMensaje("Se han importado $filas datos.");
	goOn();
}

$archivoModelo = generarArchivoModelo($query);
?>
<!doctype html>
<html lang="es">

<head>
	<title>Importar Datos - por SC3</title>

	<?php include("include-head.php"); ?>
</head>

<body onload="firstFocus()">
	<form method="post" name="form1" id="form1">
		<?php
		$req = new FormValidator();
		?>
		<table class="dlg">
			<tr>
				<td colspan="2" align="center" class="td_titulo">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td align="center"><?php echo (getOpTitle(Request("opid"), "Importador de datos")); ?></td>
							<td width="50" align="center"> <?php echo (linkImprimir()); ?> </td>
							<td width="50" align="center"><?php echo (linkCerrar(0)); ?></td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Archivo de ejemplo: </td>
				<td class="td_dato">
					<a href="<?php echo ($archivoModelo); ?>">
						<i class="fa fa-file-excel-o fa-lg verde"></i> Archivo modelo (;)
					</a>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Separador: </td>
				<td class="td_dato">
					<?php
					$cboSep = new HtmlCombo("separador", ";");
					$cboSep->add(";", "punto y coma (;)");
					echo ($cboSep->toHtml());
					if (count($aBorrados) > 0)
						echo ("<br>Campos borrados: " . implode(", ", $aBorrados));
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Archivo: </td>
				<td class="td_dato">
					<?php
					$txtFile = new HtmlInputFile("archivo", "");
					$req->add("archivo", "archivo");

					echo ($txtFile->toHtml());
					?>
					<br>
					<small>(de a <?php echo ($MAX); ?> datos)</small>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">&nbsp;</td>
				<td class="td_dato">
					<?php
					$bok = new HtmlBotonOkCancel();
					echo ($bok->toHtml());
					?>
				</td>
			</tr>

		</table>

		<script language="JavaScript" type="text/javascript">
			<?php
			echo ($req->toScript());
			?>

			function submitForm() {
				if (validar()) {
					pleaseWait2();
					document.getElementById('form1').submit();
				}
			}
		</script>

	</form>

	<?php include("footer.php"); ?>

</body>

</html>