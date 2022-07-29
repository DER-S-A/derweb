<?php
require("funcionesSConsola.php");
$meses = 1;
?>
<!doctype html>
<html lang="es">

<head>
	<title>sc3</title>

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
							<td width="50" align="center"><?php echo (linkCerrar(1)); ?></td>
						</tr>
					</table>
				</td>
			</tr>


			<tr>
				<td class="td_etiqueta">Resultado: </td>
				<td class="td_dato">

					<div id="divops" style="width:600px; height:320px; overflow: scroll;overflow-x: hidden;">

						<?php
						if (enviado()) {
							$encriptar = RequestInt("encriptar");
							echo ("<br><br><br>");

							include("sc-ofuscar.php");
							include("sc-deploy.php");

							// Agrego carpetas al deploy
							$aCarpetas = [];
							$aCarpetas[] = "/";
							$aCarpetas[] = "/sp";
							$aCarpetas[] = "/controles";
							$aCarpetas[] = "/images";
							$aCarpetas[] = "/scripts";
							$aCarpetas[] = "/css";
							$aCarpetas[] = "/scripts/autosuggest";
							$aCarpetas[] = "/scripts/quill";
							$aCarpetas[] = "/scripts/chartjs";

							$aCarpetas[] = "/terceros";
							$aCarpetas[] = "/terceros/cezpdf";
							$aCarpetas[] = "/terceros/cezpdf/src";
							$aCarpetas[] = "/terceros/cezpdf/src/include";
							$aCarpetas[] = "/terceros/cezpdf/src/fonts";
							$aCarpetas[] = "/terceros/cezpdf/extensions";
							$aCarpetas[] = "/terceros/adminer";
							$aCarpetas[] = "/terceros/cal";
							$aCarpetas[] = "/terceros/cal/includes";
							$aCarpetas[] = "/terceros/ws-nusoap-0.9.5/lib";
							$aCarpetas[] = "/terceros/dropzone";

							$meses = RequestInt("meses");
							$dias = ($meses * 31) + 5;
							if ($dias < 15)
								$dias = 15;
							$dest = crearDeployFolder($dias);

							sc3Deploy($aCarpetas, $dias);

							ofuscar("sc-html.php", true, $encriptar, $dest);

							ofuscar("funcionesSConsola.php", true, $encriptar, $dest);
							ofuscar("sc-metadata.php", true, $encriptar, $dest);
							ofuscar("sc-navigation-stack.php", true, $encriptar, $dest);
							ofuscar("sc-cache.php", true, $encriptar, $dest);
							ofuscar("dbobjecti.php", true, $encriptar, $dest);
							ofuscar("sc-secuencias.php", true, $encriptar, $dest);
							ofuscar("sc-security.php", true, $encriptar, $dest);
							ofuscar("sc-showgraphic.php", true, $encriptar, $dest);
							ofuscar("sc-encrypt.php", true, $encriptar, $dest);
							ofuscar("sc-nroletras.php", true, $encriptar, $dest);
							//ofuscar("sc-phpids.php", true, $encriptar, $dest);

							ofuscar("sc-ajax-obtenervalor.php", true, $encriptar, $dest);
							ofuscar("sc-ajax-obtenerparametro.php", true, $encriptar, $dest);

							//        	ofuscar("sc-viewitem.php");
							ofuscar("sc-edititem.php", true, $encriptar, $dest);
							//			HAsta descubrir porque el footer queda mal
							//			ofuscar("sc-selitems.php");
							ofuscar("footer.php", true, $encriptar, $dest);

							ofuscar("sc-menu-fast.php", true, $encriptar, $dest);
							ofuscar("sc-adminfavoritos.php", true, $encriptar, $dest);
							ofuscar("sc-emailpdf.php", true, $encriptar, $dest);

							ofuscar("app-cja2.php", true, $encriptar, $dest);
							ofuscar("app-comp.php", true, $encriptar, $dest);
							ofuscar("app-cta.php", true, $encriptar, $dest);
							ofuscar("app-eco.php", true, $encriptar, $dest);
							ofuscar("app-ema.php", true, $encriptar, $dest);
							ofuscar("app-inmo.php", true, $encriptar, $dest);
							ofuscar("app-obr.php", true, $encriptar, $dest);
							ofuscar("app-kio.php", true, $encriptar, $dest);
							ofuscar("app-sto.php", true, $encriptar, $dest);
							ofuscar("app-tic.php", true, $encriptar, $dest);
							ofuscar("app-tec.php", true, $encriptar, $dest);
							ofuscar("app-gri.php", true, $encriptar, $dest);
							ofuscar("app-far.php", true, $encriptar, $dest);
							ofuscar("app-ven.php", true, $encriptar, $dest);
							ofuscar("app-log.php", true, $encriptar, $dest);
							ofuscar("app-srv.php", true, $encriptar, $dest);
							ofuscar("app-gal.php", true, $encriptar, $dest);
						}
						?>

					</div>

				</td>
			</tr>

			<tr>
				<td width="200" align="right" class="td_etiqueta">meses: </td>
				<td class="td_dato">
					<?php
					$txtcant = new HtmlInputText("meses", $meses);
					$txtcant->setTypeInt();
					echo ($txtcant->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td width="200" align="right" class="td_etiqueta">encriptar: </td>
				<td class="td_dato">
					<?php
					$cboEnc = new HtmlBoolean("encriptar", 0);
					echo ($cboEnc->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td align="right" class="td_etiqueta">&nbsp;</td>
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