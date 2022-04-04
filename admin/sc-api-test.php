<?php
include("funcionesSConsola.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>

	<title>SC3 Test API</title>

	<?php include("include-head.php"); ?>

	<script type="text/javascript">

		function callApi() {

			let src = document.getElementById("url").value;
			let fn = document.getElementById("fn").value;
			let p = document.getElementById("p").value;

			let datos = new Object();

			//codifica base64
			let params = btoa(p);
			src += "?fn=" + fn + "&p=" + params;

			var xhttp = getHTTPObject();
            xhttp.open("GET", src, true); 
            xhttp.onreadystatechange = function(e) {
					if (this.readyState == 4 && this.status === 200) {
						rta = this.responseText;
						console.log(JSON.parse(rta));
						rta = rta.replaceAll(",", ",\r\n ");
						document.getElementById("rta").value = rta;
					}
				};

			xhttp.send(null);
		}

	</script>
</head>

<body>
	<form action="" name="form1" method="post">
	<?php
	$req = new FormValidator();
	?>
	<table class="dlg">
		<tr>
		<td colspan="2" align="center" class="td_titulo">
			<table width="100%" border="0" cellspacing="1" cellpadding="1">
			<tr>
				<td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
				<td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
				<td width="50" align="center"><?php echo(linkCerrar(1)); ?></td>
			</tr>
			</table>
		</td>
		</tr>
	
		<tr>
			<td class="td_etiqueta">URL: </td>
			<td class="td_dato">
				<input name="url" id="url" type="text" size="60" value="<?php echo("http://" . thisUrl()); ?>">
			</td>
		</tr>	

		<tr>
			<td class="td_etiqueta">Funcion: </td>
			<td class="td_dato">
				<input name="fn" id="fn" type="text" size="60">
			</td>
		</tr>	

		<tr>
			<td class="td_etiqueta">Parametros: </td>
			<td class="td_dato">
				<textarea name="p" id="p" cols="30" rows="5"></textarea>
			</td>
		</tr>	

		<tr>
			<td class="td_etiqueta"></td>
			<td class="td_dato">
				<input type="button" value="ACEPTAR" onclick="javascript:callApi()">
			</td>
		</tr>	

		<tr>
			<td class="td_etiqueta">Rta: </td>
			<td class="td_dato">
				<textarea name="rta" id="rta" cols="30" rows="10"></textarea>
			</td>
		</tr>	
	</table>
		
	</form> 

</body>

</html>