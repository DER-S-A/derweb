<?php
include("funcionesSConsola.php");
checkUsuarioLogueado();

$error = "";
$tabla = Request("tabla");
$campo = Request("campo");



?>
<!doctype html>
<html lang="es">

<head>
	<title>Convertir UTF8 - por SC3</title>

	<?php include("include-head.php"); ?>

</head>

<body>

	<form method="post" name="form1" id="form1">

		<?php
		$req = new FormValidator();
		?>

		<table class="dlg">
			<tr>
				<td align="center" colspan="2" class="td_titulo">
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

				<?php
				if (enviado()) {
				?>
			<tr>
				<td class="td_dato" colspan="2"><textarea rows="20" cols="60">
			<?php
					$bd = new BDObject();

					sc3ConvertirTablaUtf($bd, "sc_querys", "querydescription");
					sc3ConvertirTablaUtf($bd, "sc_menuconsola", "Item", "idItemMenu");
					sc3ConvertirTablaUtf($bd, "sc_operaciones", "nombre");
					sc3ConvertirTablaUtf($bd, "sc_operaciones", "ayuda");
					sc3ConvertirTablaUtf($bd, "sc_usuarios", "nombre");
					sc3ConvertirTablaUtf($bd, "sc_adjuntos", "nota");

					sc3ConvertirTablaUtf($bd, "subrubros", "descripcion");
					sc3ConvertirTablaUtf($bd, "rubros", "descripcion");
					sc3ConvertirTablaUtf($bd, "marcas", "descripcion");
					sc3ConvertirTablaUtf($bd, "articulos", "codigo");
					sc3ConvertirTablaUtf($bd, "articulos", "codigo_original");
					sc3ConvertirTablaUtf($bd, "articulos", "descripcion");
					sc3ConvertirTablaUtf($bd, "articulos", "informacion_general");
					sc3ConvertirTablaUtf($bd, "articulos", "datos_tecnicos");
					sc3ConvertirTablaUtf($bd, "articulos", "diametro");
					sc3ConvertirTablaUtf($bd, "art_catalogos", "descripcion");
					sc3ConvertirTablaUtf($bd, "paises", "nombre");
					sc3ConvertirTablaUtf($bd, "provincias", "nombre");
					sc3ConvertirTablaUtf($bd, "lfw_operaciones", "nombre");
					sc3ConvertirTablaUtf($bd, "entidades", "nombre");
					sc3ConvertirTablaUtf($bd, "entidades", "direccion");
					sc3ConvertirTablaUtf($bd, "entidades", "email");
					sc3ConvertirTablaUtf($bd, "entidades", "nombre");
					sc3ConvertirTablaUtf($bd, "sucursales", "codigo_sucursal");
					sc3ConvertirTablaUtf($bd, "sucursales", "calle");
					sc3ConvertirTablaUtf($bd, "sucursales", "ciudad");
					sc3ConvertirTablaUtf($bd, "banner_portada", "descripcion");
		
					/*
			
			*/

			?>
			</textarea>
				</td>
			</tr>

		<?php
				}
		?>

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