<?php
include_once("funcionesSConsola.php");
include_once("app-wsp.php");
checkUsuarioLogueado();

$destino = "";
$tema = "";
$mensaje = "";
if (enviado()) {
	$destino = Request("destino");
	$tema = Request("tema");
	$mensaje = Request("mensaje");
	$adjunto = Request("adjuntos");
	$token = Request("token");
	$aAdjuntos = array();
	$aAdjuntos[] = $adjunto;

	//cero final es NO Masivo
	$params = prepararParametrosWsp($destino, $tema, $mensaje, $aAdjuntos, $token, false, 0);
	$result = callUrlWsp($params);

	$aResult = json_decode($result, true);
	$error = $aResult["error"];
	$msg = $aResult["msg"];

	if (!esVacio($error))
		setWarning($error . " " . $result);
	else
		setMensaje("$msg " . str_replace(",", ", ", $result));
	goOn();
}

?>
<!doctype html>
<html lang="es">

<head>
	<title>Enviar WhatsApp - por SC3</title>

	<?php include_once("include-head.php"); ?>

	<style>
		body {
			margin-bottom: 40px;
		}
	</style>
</head>

<body onload="document.getElementById('token').focus();">

	<form method="post" name="form1" id="form1" enctype='multiple/form-data'>
		<?php
		$req = new FormValidator();
		?>
		<table class="dlg">
			<tr>
				<td colspan="2" align="center" class="td_titulo">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td align="center" width="85%">Enviar por WhatsApp </td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">De: </td>
				<td class="td_dato">
					<?php
					$cboDe = new HtmlCombo("token", "");
					$rs = getRs("select token, nombre 
								from b2b_empresas 
								where token <> ''
								order by nombre");
					$cboDe->cargarRs($rs, "token", "nombre");
					echo ($cboDe->toHtml());
					$req->add("token", "De");
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Para: </td>
				<td class="td_dato">
					<?php
					$txtDestino = new HtmlInputText("destino", $destino);
					echo ($txtDestino->toHtml());
					$req->add("destino", "Destinatario");
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Tema: </td>
				<td class="td_dato">
					<?php
					$txtTema = new HtmlInputText("tema", $tema);

					echo ($txtTema->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Mensaje: </td>
				<td class="td_dato">
					<?php
					$txtMensaje = new HtmlInputTextarea("mensaje", $mensaje);
					$req->add("mensaje", "Mensaje");
					echo ($txtMensaje->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Archivos: </td>
				<td class="td_dato">
					<?php
					$adjuntos = new HtmlInputFile("adjuntos", "");
					echo ($adjuntos->toHtml());
					?>
				</td>
			</tr>

		</table>


		<div class="div-botones-inferior">
			<?php
			$bok = new HtmlBotonOkCancel(true, false);
			$bok->setLabel("Enviar");
			echo ($bok->toHtml());
			?>
		</div>

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
</body>

</html>