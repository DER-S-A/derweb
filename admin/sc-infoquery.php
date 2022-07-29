<?php
include("funcionesSConsola.php");

checkUsuarioLogueado();

$error = "";
$mid = RequestInt("mid");
$mquery = Request("mquery");
?>
<!doctype html>
<html lang="es">

<head>
	<title>Info Nautilus - por SC3</title>

	<?php include("include-head.php"); ?>

</head>

<body onload="firstFocus()">
	<form method="post" name="form1" id="form1">

		<table class="dlg">
			<tr>
				<td colspan="2" align="center" class="td_titulo">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td align="center"><?php echo (getOpTitle(Request("opid"))); ?></td>
							<td width="50" align="center"> <?php echo (linkImprimir()); ?> </td>
							<td width="50" align="center"><?php echo (linkCerrar(0)); ?></td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta"></td>
				<td class="td_dato">
					<textarea rows="32" cols="100">
		<?php
		$qinfo = getQueryObj($mquery);
		$rs = locateRecordId($qinfo->getQueryTable(), $mid, $qinfo->getKeyField());

		$asig = array();
		$campos = array();
		$valores = array();
		$abmForm = array();
		foreach ($rs->getRow() as $campo => $valor) {
			if (!is_numeric($campo)) {
				echo ("\r\n");
				echo ('$aValues[\'' . $campo . '\'] = \'' . $valor . "';");
				$campos[] = $campo;
				$valores[] = $valor;
				$asig[] = '$' . $campo . ' = $rs->getValue("' . $campo . '");';

				$abmForm[] = array(
					"campo" => $campo,
					"control" => '$txt' . ucfirst($campo) . ' = ' . "new HtmlInputText('$campo', " . '$' . "$campo);",
					"echo" => 'echo($txt' . ucfirst($campo) . '->toHtml());',
					"echover" => 'echo($' . $campo . ');'
				);
			}
		}

		echo ("\r\n\r\ninsert into " . $qinfo->getQueryTable() . "(" . implode(", ", $campos) . ")");
		echo ("\r\n  values('" . implode("', '", $valores) . "')");

		echo ("\r\n\r\n" . implode("\r\n", $asig));

		$sql = "show create table " . $qinfo->getQueryTable();
		$bd = new BDObject();
		$bd->execQuery($sql);
		echo ("\r\n\r\n" . $bd->getValue("1"));

		echo ("\r\n\r\n" . $qinfo->locateRecordSql2($mid));

		echo ("\r\n\r\n");
		foreach ($abmForm as $i => $grupo) {
			echo ("\r\n<tr>\r\n\t<td class=\"td_etiqueta\">" . $grupo["campo"] . ":</td>");
			echo ("\r\n\t<td class=\"td_dato\">\r\n\t\t" . '<?php' . "\r\n\t\t\t" . $grupo["control"] . "\r\n\t\t\t" . $grupo["echo"] . "\r\n\t\t" . '?>' . "\r\n\t</td>\r\n</tr>");
		}

		echo ("\r\n\r\n");
		foreach ($abmForm as $i => $grupo) {
			echo ("\r\n<tr>\r\n\t<td class=\"td_etiqueta\">" . $grupo["campo"] . ":</td>");
			echo ("\r\n\t<td class=\"td_dato\">\r\n\t\t" . '<?php' . "\r\n\t\t\t" . $grupo["echover"] . "\r\n\t\t" . '?>' . "\r\n\t</td>\r\n</tr>");
		}

		?>
		</textarea>
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


		</script>
	</form>
	<?php include("footer.php"); ?>
</body>

</html>