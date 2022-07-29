<?php 
include("funcionesSConsola.php");
include("app-ema.php");
checkUsuarioLogueado();

$usuario = getParameter("sms_sender-usuario", "	mcasamayor");
$clave = getParameter("sms_sender-clave", "11111");
$urlSender = getParameter("sms_sender-url", "http://www.smsfacil.com.ar/sms/sender2.php");
$lac = getParameter("sc3-sms-lac", "2284");

$urlCortosApi = getParameter("sc3-acortador-url", "http://sc3.com.ar/u/url.php");
$urlCortosUsr = getParameter("sc3-acortador-usuario", "sc3");
$urlCortosClave = getParameter("sc3-acortador-clave", "12341234");


$cuerpo = "";
$error = "";
$jscript = "";
$idtemplate = RequestInt("idtemplate");
if (enviado() && $idtemplate == 0)
{
	$cuerpo = $SITIO . " \r\n" . Request("cuerpo");

	$jscript = "";

	/*
		HTTP Get URL Ejemplo:
		http://www.smsfacil.com.ar/sms/sender2.php?cid=usuariox&vc=XXXX&n=1133332222&msg=hello world

		Variables Requeridas:
			cid = nombre de usuario (provista por SMSFácil)
			vc = clave de acceso (provista por SMSFácil)
			n = n�mero de celular que va a recibir el mensaje.
			msg = mensaje sms a enviar.
			Variables Opcionales:
			o day = especificar d�a de envío del sms.
			o time = especificar hora de envío de sms.
			o r = c�digo de referencia del mensaje.
	*/
	$cel = Request("celular");

	//traduce el celular según como está escrito
	$cel = emaValidarCelular($cel);
	if (!esVacio($cel))
	{			
		// Invoca acortador de URLS -------------------------------------------------
		$datos = [];
		$datos["url"] = thisUrl() . Request("file1");
		$datos["usuario"] = $urlCortosUsr;
		$datos["clave"] = $urlCortosClave;
		$params = base64_encode(json_encode($datos));
	
		$url = new HtmlUrl($urlCortosApi);
		$url->add("fn", "getShort");
		$url->add("p", $params);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url->toUrl());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/41.0');
		$header = array("User-Agent: Mozilla/5.0");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		$aResponse = json_decode($response, true);

		//Analiza error
		if ($aResponse['AJAX_RESULT'] != 1)
			print_r($aResponse);

		$urlCorta = $aResponse["url_corta"];
		curl_close($ch);
	
		//FIN acortador de URLS -----------------------------------------------------------

		//agrega URL corta al mensaje y firma
		$cuerpo .= " " . $urlCorta . " \r\n\r\nUsamos Sistemas SC3";

		$url = new HtmlUrl($urlSender);
		$url->add("cid", $usuario);
		$url->add("vc", $clave);
		$url->add("n", $cel);
		$url->add("msg", $cuerpo);
		
		$rta = file_get_contents($url->toUrl());

		//code:1707# - no more credits left 
		if (startsWith($rta, "code:1707#"))
			$error = "Falta de credito en SMS Facil, usuario $usuario";
		else	
			//code:1709# - message added with delay 
			if (startsWith($rta, "code:1709#"))
				$error = "";
			else
				$error = $rta;

		//guarda en tabla ema_envios
		emaLogEnvio("SMS", $cel, $idtemplate, 0, 0, "", $cuerpo);
	}

	if (esVacio($error))
		$jscript = "window.close();";

}

$fullFilename = Request("filename");

$mquery = Request("mquery");
$mid = RequestInt("mid");

$adjunto1 = "";
$adjunto2 = "";
$nombre = "";

if (!esVacio($mquery))
{
	//busca adjuntos al query que envió
	$qinfo = getQueryObj($mquery);
	$idquery = $qinfo->getQueryId();
	
	$rsExtra = locateRecordWhere("sc_adjuntos", "iddato = $mid and idquery  = $idquery");
	if (!$rsExtra->EOF())
	{
		$adjunto1 = $rsExtra->getValue("adjunto1");
		$adjunto2 = $rsExtra->getValue("adjunto2");
		
		if (!esVacio($adjunto1))
			$adjunto1 = str_replace(".", "", $UPLOAD_PATH_SHORT) . "/" . $adjunto1;
		if (!esVacio($adjunto2))
			$adjunto2 = str_replace(".", "", $UPLOAD_PATH_SHORT) . "/" . $adjunto2;
	}
}

//fué enviado con un parámetro
if (esVacio($adjunto1))
	$adjunto1 = $fullFilename;

$idcuenta = 0;
if (sonIguales($mquery, "qcja2comprobantes"))
{
	$idcomprobante = $mid;

	include("app-cja2.php");
	
	$rsComp = compTitularComprobante($idcomprobante);
	$celular = $rsComp->getValue("cliente_celular");
	$idcuenta = $rsComp->getValueInt("idcuenta");
	$titulo = $SITIO . " - " . $rsComp->getValue("numero_completo");
	$nombre = $rsComp->getValue("cliente_nombre");
	$cuerpo = "Estimado " . $nombre . ",\nLe adjuntamos su comprobante. Atte.";
	$rsComp->close();
}

if ($idtemplate != 0)
{
	$rstemplate = locateRecordId("ema_templates", $idtemplate);
	$rstemplate->close();
	$cuerpo = $rstemplate->getValue("cuerpo_texto");
	$cuerpo = str_replace("@nombre@", $nombre, $cuerpo);
}

?>
<!doctype html>
<html lang="es">
<head>
<title>Enviar SMS - por SC3</title>

<?php include("include-head.php"); ?>

<style>

body
{
	margin-bottom: 40px;
}

</style>
</head>
<body onload="document.getElementById('toemail').focus();">

<script type="text/javascript">
<?php 
echo($jscript); 
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
			<td align="center" width="85%">Enviar por SMS </td>
			<td width="15%"> 
				<a id="linkcerrarW" href="#" onclick="window.close();">
					<img src="images/close.gif" border="0" title="Cerrar [Esc]"  alt="Cerrar [Esc]"/> 
				</a> 
			</td>
		</tr>
		</table>
	</td>
	</tr>
	
	<?php
	if ($error != "")
	{
	?>
	<tr>
	<td colspan="2" class="td_error">
		<?php 
		echo($error); 
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
		$txtSMS = new HtmlInputText("celular", $celular);
		$txtSMS->setSize(20);
		$txtSMS->setPlaceholder("Nro celular");
		echo($txtSMS->toHtml());

		$hlp = new HtmlHelp("Nro de celular sin 0 ni 15 y con codigo de area, ej: 1145674567"); 
		echo($hlp->toHtml());
		$req->add("celular", "Destinatario");
		?>
	</td>
	</tr>

	<tr>
	<td class="td_etiqueta">SMSFacil </td>
	<td class="td_dato">
		<?php 
		echo($usuario);
		?>
	</td>
	</tr>

	<tr>
	<td class="td_etiqueta">Acortador </td>
	<td class="td_dato">
		<?php 
		echo($urlCortosApi);
		?>
	</td>
	</tr>

	<tr>
	<td class="td_etiqueta">Cod Area </td>
	<td class="td_dato">
		<?php 
		echo($lac);
		?>
		<small>(si se omite)</small>
	</td>
	</tr>

	<tr>
	<td class="td_etiqueta"> </td>
	<td class="td_dato">
		<?php 
		$cboTemplate = new HtmlCombo("idtemplate", "");
		$cboTemplate->add("", " - aplicar plantilla - ");
		$rs = getRs("select id, nombre from ema_templates order by nombre");
		$cboTemplate->cargarRs($rs, "id", "nombre");
		$cboTemplate->onchangeSubmit();
		echo($cboTemplate->toHtml());
		?>
	</td>
	</tr>

	<tr>
	<td class="td_etiqueta"></td>
	<td class="td_dato">
		<?php
		$filename = thisUrl() . $adjunto1;
		echo(imgFa("fa-paperclip", "fa-2x", "verde", $filename) . " <b>" . resumirTexto($filename, 50) . "</b>");
		
		$hidFilename = new HtmlHidden("file1", $adjunto1);
		echo($hidFilename->toHtml());
		?>
	</td>
	</tr>
	
	<tr>
	<td class="td_dato" colspan="2">
		<?php 
		$txtcuerpo = new HtmlInputTextarea("cuerpo", $cuerpo);
		echo($txtcuerpo->toHtml());
		?>
	</td>
	</tr>
</table>

<div class="div-botones-inferior">
	<?php 
	$bok = new HtmlBotonOkCancel(true, false);
	$bok->setLabel("Enviar");
	echo($bok->toHtml());
	?>
</div>

<script language="JavaScript" type="text/javascript">
	<?php
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		if (validar())
		{
			pleaseWait2();
			document.getElementById('form1').submit();
		}
	}
	
	</script>
</form>
</body>
</html>