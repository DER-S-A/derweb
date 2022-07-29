<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();
?>
<!doctype html>
<html lang="es">
<head>
<title>Generar campos - por SC3</title>

<?php include("include-head.php"); ?>

</head>

<body>
<form action="genfieldsinfo2.php" id="form1" method="post">

<table class="dlg">
	<tr> 
		<td class="td_titulo" colspan="2">Generar Informaci&oacute;n de Campos para un Query</td>
	</tr>
	<tr> 
		<td class="td_etiqueta">Seleccione Query</td>
		<td class="td_dato"> 
		<?php
		$selQ = new HtmlSelector("idquery", "sc_querysall");
		$selQ->checkMaster();
		echo($selQ->toHtml());
		?>
		</td>
	</tr>
	<tr> 
		<td class="td_etiqueta"></td>
		<td class="td_dato"> 
		<?php 
		$bok = new HtmlBotonOkCancel();
		echo($bok->toHtml());
		?>
		</td>
	</tr>
	</table>

        
	<script type="text/javascript">

	

	function submitForm()
	{
		pleaseWait2();
		document.getElementById('form1').submit();	
	}
		
	</script>


</form>
</body>
</html>