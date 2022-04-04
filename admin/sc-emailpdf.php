<?php
include("funcionesSConsola.php");
include("app-ema.php");
checkUsuarioLogueado();

$cuerpo = "";
$error = "";
$jscript = "";
$idtemplate = RequestInt("idtemplate");
if (enviado() && $idtemplate == 0) {
	$copiarme = RequestInt("copiarme");
	$fromEmail = getSession("email");
	$toEmail = Request("toemail");
	$tema = Request("tema");
	$cuerpo = Request("cuerpo");

	$cc = "";
	if ($copiarme == 1)
		$cc = getSession("email");
	$jscript = "";
	if (enviarEmail($fromEmail, $toEmail, $cc, $tema, $cuerpo, "", true))
		$jscript = "window.close();";
	else {
		$error = getMensaje();
	}
}

$email = Request("email");
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
	if (esVacio($email))
		$email = $rsComp->getValue("cliente_email");
	$idcuenta = $rsComp->getValueInt("idcuenta");
	$titulo = $SITIO . " - " . $rsComp->getValue("numero_completo");
	$nombre = $rsComp->getValue("cliente_nombre");
	$cuerpo = "<font face=\"Verdana\">Estimado " . $nombre . ",\nLe adjuntamos su comprobante. Atte.</font>";
}

if ($idtemplate != 0) {
	$rstemplate = locateRecordId("ema_templates", $idtemplate);
	$cuerpo = $rstemplate->getValue("cuerpo_html");
	$cuerpo = str_replace("@nombre@", $nombre, $cuerpo);

	if ($idcomprobante != 0 && $idcuenta != 0) {
		$botonMP = href(img(thisUrl() . '/mercadopago/pagar-mp.png', "Pagar"), thisUrl() . "/pagar-mp.php?idcuenta=$idcuenta&idcomprobante=$idcomprobante");
		$cuerpo = str_replace("[BOTON-MERCADO-PAGO]", $botonMP, $cuerpo);
	}
}

?>
<!doctype html>
<html lang="es">

<head>
	<title>Enviar email - por SC3</title>

	<?php include("include-head.php"); ?>

	<style>
		body {
			margin-bottom: 40px;
		}
	</style>
</head>

<body onload="document.getElementById('toemail').focus();">

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
							<td align="center" width="85%">Enviar por email </td>
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
				<td class="td_etiqueta">De: </td>
				<td class="td_dato">
					<?php
					global $SITIO;
					echo (getSession("email"));

					$cboCc = new HtmlBoolean2("copiarme", 0);
					echo ($cboCc->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">Para: <?php $hlp = new HtmlHelp("Separe destinatarios con coma ','");
												echo ($hlp->toHtml()); ?></td>
				<td class="td_dato">
					<?php
					$txtemail = new HtmlInputTextEmail("toemail", $email);
					$txtemail->setAutosuggest();
					echo ($txtemail->toHtml());
					$req->add("toemail", "Destinatario");
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
				<td class="td_etiqueta"> </td>
				<td class="td_dato">
					<?php
					$cboTemplate = new HtmlCombo("idtemplate", "");
					$cboTemplate->add("", " - aplicar plantilla - ");
					$rs = getRs("select id, nombre 
								from ema_templates 
								order by nombre");
					$cboTemplate->cargarRs($rs, "id", "nombre");
					$cboTemplate->onchangeSubmit();
					echo ($cboTemplate->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta"></td>
				<td class="td_dato">
					<?php
					$filename = basename($adjunto1);
					echo (img("images/email_attach.png", "Email") . " <b>$filename</b>");

					$hidFilename = new HtmlHidden("file1", $adjunto1);
					echo ($hidFilename->toHtml());

					if (!esVacio($adjunto2)) {
						$filename = basename($adjunto2);
						echo ("<br/>" . img("images/email_attach.png", "Email") . " <b>$filename</b>");
					}

					$hidajunto2 = new HtmlHidden("file2", $adjunto2);
					echo ($hidajunto2->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_dato" colspan="2">
					<?php
					$txtcuerpo = new HtmlRichText("cuerpo", $cuerpo);
					echo ($txtcuerpo->toHtml());
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