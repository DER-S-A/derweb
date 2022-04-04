<?php
include("funcionesSConsola.php");
checkUsuarioLogueado();

$error = "";
$mid = RequestInt("mid");
$mquery = Request("mquery");

if (enviado()) {

	$zip = Request("zip_file");


	setMensaje($zip);
	goOn();
}
?>
<!doctype html>
<html lang="es">

<head>
	<title>Subir fotos - por SC3</title>

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
							<td align="center"><?php echo (getOpTitle(Request("opid"))); ?></td>
							<td width="50" align="center"> <?php echo (linkImprimir()); ?> </td>
							<td width="50" align="center"><?php echo (linkCerrar(0)); ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="td_etiqueta">Propiedad:</td>
				<td class="td_dato">
					<?php
					$selprops = new HtmlSelector("idpropiedad", "qpropropiedades", "");
					$selprops->setValue($mid);
					$selprops->setReadOnly(true);
					echo ($selprops->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">ZIP modulo:</td>
				<td class="td_dato">
					<?php
					$f1 = new HtmlInputFile("zip_file", "");
					echo ($f1->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">&nbsp;</td>
				<td class="td_dato">
					<?php
					$bOkCancel = new HtmlBotonOkCancel();
					echo ($bOkCancel->toHtml());
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