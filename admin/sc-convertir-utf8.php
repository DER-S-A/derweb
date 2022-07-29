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

					sc3ConvertirTablaUtf($bd, "gri_presentaciones", "direccion");
					sc3ConvertirTablaUtf($bd, "gri_presentaciones", "observaciones");
					sc3ConvertirTablaUtf($bd, "gri_trabajos", "observaciones");
					sc3ConvertirTablaUtf($bd, "gri_trabajos", "direccion");
					sc3ConvertirTablaUtf($bd, "gri_trabajos", "titular");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "Propietario");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "Localizacion");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "LimitesyLinderos");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "Perito");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "Observaciones");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "Observaciones2");
					sc3ConvertirTablaUtf($bd, "gri_fichas", "DomicilioParcelario");

					sc3ConvertirTablaUtf($bd, "far_farmacias", "nombre");
					sc3ConvertirTablaUtf($bd, "far_farmacias", "direccion");

					sc3ConvertirTablaUtf($bd, "obr_obras", "nombre");
					sc3ConvertirTablaUtf($bd, "obr_rubros", "nombre");

					sc3ConvertirTablaUtf($bd, "srv_periodos", "nombre");
					sc3ConvertirTablaUtf($bd, "srv_productos", "descripcion");

					sc3ConvertirTablaUtf($bd, "mat_materiales", "nombre");
					sc3ConvertirTablaUtf($bd, "mat_materiales", "descripcion");

					sc3ConvertirTablaUtf($bd, "gen_personas", "nombre");
					sc3ConvertirTablaUtf($bd, "gen_personas", "jur_nombre_fantasia");
					sc3ConvertirTablaUtf($bd, "gen_personas", "direccion");
					sc3ConvertirTablaUtf($bd, "cta_cuentas", "nombre");
					sc3ConvertirTablaUtf($bd, "cta_cuentas", "descripcion");
					sc3ConvertirTablaUtf($bd, "sto_articulos", "nombre");
					sc3ConvertirTablaUtf($bd, "sto_articulos", "descripcion");
					sc3ConvertirTablaUtf($bd, "sto_combos", "nombre");
					sc3ConvertirTablaUtf($bd, "sto_depositos", "nombre");
					sc3ConvertirTablaUtf($bd, "bp_localidades", "nombre");
					sc3ConvertirTablaUtf($bd, "bp_localidades", "prefijo");
					sc3ConvertirTablaUtf($bd, "bp_provincias", "nombre");
					sc3ConvertirTablaUtf($bd, "cja2_cajas", "nombre");
					sc3ConvertirTablaUtf($bd, "cja2_medios_pago", "nombre");
					sc3ConvertirTablaUtf($bd, "cja2_conceptos", "nombre");
					sc3ConvertirTablaUtf($bd, "cja2_movimientos", "observaciones");
					sc3ConvertirTablaUtf($bd, "cja2_comprobantes", "observaciones");
					sc3ConvertirTablaUtf($bd, "bco_bancos", "nombre");
					sc3ConvertirTablaUtf($bd, "cja_movimientos", "observaciones");

					sc3ConvertirTablaUtf($bd, "gal_estudios", "motivo");
					sc3ConvertirTablaUtf($bd, "gal_estudios", "diagnostico");
					sc3ConvertirTablaUtf($bd, "gal_estudios", "tratamiento");
					sc3ConvertirTablaUtf($bd, "gal_turnos", "direccion");
					sc3ConvertirTablaUtf($bd, "gal_turnos", "apellido");
					sc3ConvertirTablaUtf($bd, "gal_turnos", "doctor");
					sc3ConvertirTablaUtf($bd, "gal_nomenclaturas", "descripcion");

					sc3ConvertirTablaUtf($bd, "eco_consorcios", "nombre");
					sc3ConvertirTablaUtf($bd, "eco_consorcios", "direccion");
					sc3ConvertirTablaUtf($bd, "eco_propietarios", "nombre");
					sc3ConvertirTablaUtf($bd, "eco_propietarios", "direccion");
					sc3ConvertirTablaUtf($bd, "eco_propietarios", "observaciones");
					sc3ConvertirTablaUtf($bd, "eco_propietarios", "inq_nombre");
					sc3ConvertirTablaUtf($bd, "eco_propietarios", "codigo_fantasia");
					sc3ConvertirTablaUtf($bd, "eco_propietarios", "unidad");
					sc3ConvertirTablaUtf($bd, "eco_facturas_proveedor", "observaciones");
					sc3ConvertirTablaUtf($bd, "eco_facturas_proveedor", "medio_pago");
					sc3ConvertirTablaUtf($bd, "eco_periodos", "nombre");

					sc3ConvertirTablaUtf($bd, "kio_fotocopias", "titulo");
					sc3ConvertirTablaUtf($bd, "kio_fotocopias", "descripcion");

					sc3ConvertirTablaUtf($bd, "gen_impuestos", "nombre");

					sc3ConvertirTablaUtf($bd, "ema_templates", "nombre");
					sc3ConvertirTablaUtf($bd, "ema_templates", "cuerpo_html");
					sc3ConvertirTablaUtf($bd, "ema_templates", "cuerpo_text");

					sc3ConvertirTablaUtf($bd, "pro_propiedades", "descripcion");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "descripcion_larga");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "obs_internas");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "distancias");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "com_habitaciones");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "com_camas");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "com_cochera");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "direccion");
					sc3ConvertirTablaUtf($bd, "pro_propiedades", "com_parque");
					sc3ConvertirTablaUtf($bd, "pro_complejos", "como_llegar");
					sc3ConvertirTablaUtf($bd, "pro_complejos", "direccion");
					sc3ConvertirTablaUtf($bd, "pro_complejos", "descripcion");
					sc3ConvertirTablaUtf($bd, "pro_ocupacion", "observaciones");
					sc3ConvertirTablaUtf($bd, "pro_pedidos", "observaciones");
					//sc3ConvertirTablaUtf($bd, "pro_pedidos", "cliente");

					sc3ConvertirTablaUtf($bd, "b2b_afip_datos", "nombre");
					sc3ConvertirTablaUtf($bd, "b2b_afip_datos", "resultado");

					sc3ConvertirTablaUtf($bd, "tic_reparaciones", "nombre");
					sc3ConvertirTablaUtf($bd, "tic_reparaciones", "observaciones");
					sc3ConvertirTablaUtf($bd, "tic_tipos_trabajos", "nombre");

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