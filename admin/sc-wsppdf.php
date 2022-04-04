<?php
include("funcionesSConsola.php");
include_once("app-wsp.php");
checkUsuarioLogueado();

$mensaje = "";
$error = "";
$jscript = "";
$esTmp = false;

$celular = Request("celular");
$titulo = Request("titulo");
$fullFilename = Request("filename");

$mquery = Request("mquery");
$mid = RequestInt("mid");

$adjunto1 = "";
$adjunto2 = "";
$nombre = "";

if (!esVacio($mquery)) {
	//busca adjuntos al query que envió
	$qinfo = getQueryObj($mquery);
	$idquery = $qinfo->getQueryId();

	$rsExtra = locateRecordWhere("sc_adjuntos", "iddato = $mid and idquery  = $idquery");
	if (!$rsExtra->EOF()) {
		$adjunto1 = $rsExtra->getValue("adjunto1");
		$adjunto2 = $rsExtra->getValue("adjunto2");

		if (!esVacio($adjunto1))
			$adjunto1 = $UPLOAD_PATH_SHORT . "/" . $adjunto1;
		if (!esVacio($adjunto2))
			$adjunto2 = $UPLOAD_PATH_SHORT . "/" . $adjunto2;
	}
}

//fué enviado con un parámetro
if (esVacio($adjunto1))
	$adjunto1 = $fullFilename;

$idcuenta = 0;
if (sonIguales($mquery, "qcja2comprobantes")) {
	$idcomprobante = $mid;

	include("app-cja2.php");

	$rsComp = compTitularComprobante($idcomprobante);
	if (esVacio($celular))
		$celular = $rsComp->getValue("cliente_celular");
	$idcuenta = $rsComp->getValueInt("idcuenta");
	$titulo = $SITIO . " - " . $rsComp->getValue("numero_completo");
	$nombre = $rsComp->getValue("cliente_nombre");
	$mensaje = "Estimado " . $nombre . ",\nLe adjuntamos su comprobante.";
}


if (enviado()) {
	$copiarme = RequestInt("copiarme");
	$tocelular = Request("tocelular");
	$tema = Request("tema");
	$mensaje = Request("mensaje");
	$token = Request("token");

	$cc = "";
	if ($copiarme == 1)
		$cc = getSession("celular");


	$aAdjuntos = [];
	if (strpos($adjunto1, "tmp") != false) {
		$esTmp = true;
	}
	if (!esVacio($adjunto1)) {
		$aAdjuntos[] = $adjunto1;
	}
	if (!esVacio($adjunto2)) {
		$aAdjuntos[] = $adjunto2;
	}

	//el cero final indica que no es masivo y se inserta adelante en la cola
	$params = prepararParametrosWsp($tocelular, $tema, $mensaje, $aAdjuntos, $token, $esTmp, 0);
	$result = callUrlWsp($params);
	$aResult = json_decode($result, true);
	if (array_key_exists("error", $aResult) && $aResult["error"] != "") {
		$error = $aResult["error"];
	} else {
		$jscript = "window.close();";
	}
}

?>
<!doctype html>
<html lang="es">

<head>
	<title>Enviar WhatsApp - por SC3</title>

	<?php include("include-head.php"); ?>

	<style>
		body {
			margin-bottom: 40px;
		}
	</style>
</head>

<body onload="document.getElementById('tocelular').focus();">

	<script type="text/javascript">
		<?php
		echo ($jscript);
		?>
	</script>


	<form method="post" name="form1" id="form1">
		<?php
		$req = new FormValidator();
		?>
		<table class="dlg">
			<tr>
				<td colspan="2" align="center" class="td_titulo">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="85%">
								<i class="fa fa-whatsapp fa-2x"></i>
								Enviar por whatsapp
							</td>
							<td width="15%">
								<a id="linkcerrarW" href="#" onclick="window.close();">
									<img src="images/close.gif" border="0" title="Cerrar [Esc]" alt="Cerrar [Esc]" />
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<?php
			if ($error != "") {
			?>
				<tr>
					<td colspan="2" class="td_error">
						<?php
						echo ($error);
						?>
					</td>
				</tr>
			<?php
			}
			?>

			<tr>
				<td class="td_etiqueta">Para: </td>
				<td class="td_dato">
					<?php
					$txtDestino = new HtmlInputText("tocelular", $celular);
					$txtDestino->setSize(30);
					echo ($txtDestino->toHtml());
					$req->add("tocelular", "Destinatario");

					$hlp = new HtmlHelp("Separe destinatarios con coma ','");
					echo ($hlp->toHtml()); 
					?>
				</td>

			</tr>

			<tr>
				<td class="td_etiqueta">Tema: </td>
				<td class="td_dato">
					<?php
					$txttema = new HtmlInputText("tema", $titulo);
					$txttema->setClass("ancho100");

					echo ($txttema->toHtml());
					?>
				</td>
			</tr>
			<tr>
				<td class="td_etiqueta"></td>
				<td class="td_dato">
					<?php
					$filename = basename($adjunto1);
					echo (img("images/manage_attachments.png", "celular") . " <b>$filename</b>");

					$hidFilename = new HtmlHidden("file1", $adjunto1);
					echo ($hidFilename->toHtml());

					if (!esVacio($adjunto2)) {
						$filename = basename($adjunto2);
						echo ("<br/>" . img("images/manage_attachments.png", "celular") . " <b>$filename</b>");
					}

					$hidajunto2 = new HtmlHidden("file2", $adjunto2);
					echo ($hidajunto2->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Mensaje: </td>
			</tr>
			<tr>
				<td class="td_dato" colspan="2">
					<?php
					$txtmensaje = new HtmlInputTextarea("mensaje", $mensaje);
					$req->add("mensaje", "Mensaje");
					echo ($txtmensaje->toHtml());
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
				updateRTEs();
				if (validar()) {
					pleaseWait2();
					document.getElementById('form1').submit();
				}
			}
		</script>
	</form>
</body>

</html>