<?php
include_once("funcionesSConsola.php");

if (enviado()) {
	$datos = array();
	$datos["url"] = Request("url");
	$datos["usuario"] = Request("usuario");
	$datos["clave"] = Request("clave");
	$params = base64_encode(json_encode($datos));

	CallAPI($params);
}

function CallAPI($data)
{
	$urlConsulta = thisUrl() . "url.php";
	$url = new HtmlUrl($urlConsulta);
	$url->add("fn", "getShort");
	$url->add("p", $data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url->toUrl());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/41.0');
	$header = array("User-Agent: Mozilla/5.0");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$response = curl_exec($ch);
	$aResponse = json_decode($response, true);
	$urlBase = $aResponse["url_corta"];
	echo $urlBase;
	curl_close($ch);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<? include("include-head.php"); ?>
</head>

<body>

	<form method="post" name="form1" id="form1">
		<label style="display: block;" for="url">URL</label>
		<input style="display: block;" name="url" type="text">
		<label style="display: block;" for="usuario">USUARIO</label>
		<input style="display: block;" name="usuario" type="text">
		<label style="display: block;" for="clave">CLAVE</label>
		<input style="display: block;" name="clave" type="text">
		<?php
		$botones = new HtmlBotonOkCancel();
		echo $botones->toHtml();
		$req = new FormValidator();
		?>

		<script language="JavaScript" type="text/javascript">
			<?php
			echo ($req->toScript());
			?>

			function submitForm() {
				//make sure hidden and iframe values are in sync for all rtes before submitting form
				document.getElementById('form1').submit();
			}
		</script>
	</form>
</body>

</html>