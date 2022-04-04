<?php
require("funcionesSConsola.php");
require("sc-updversion-utils.php");
checkUsuarioLogueado();

if (enviado()) {

	$tabla = Request("tabla");
	$idperfil = RequestInt("idperfil");
	$idmenu = RequestInt("idmenu");
	$insertar = RequestInt("insertar");
	$editar  = RequestInt("editar");
	$borrar  = RequestInt("borrar");

	$menu = "";
	if ($idmenu != 0)
	{
		$rsMenu = locateRecordId("sc_menuconsola", $idmenu, "idItemMenu");
		$menu = $rsMenu->getValue("Item");
	}

	sc3agregarQuery($tabla, $tabla, $tabla, $menu, "", $insertar, $editar, $borrar, "", 10, "images/table.png");
	sc3generateFieldsInfo($tabla, true);
	sc3UpdateRequeridos($tabla);

	$rsQuery = locateRecordWhere("sc_querys", "table_ = '$tabla'");
	$id = $rsQuery->getId();

	//agrega tabla al perfil y perfil al usuario
	if ($idperfil != 0)
	{
		$rsPerfil = locateRecordId("sc_perfiles", $idperfil);
		$perfil = $rsPerfil->getValue("nombre");
		$rsPerfil->close();

		sc3AgregarQueryAPerfil($tabla, $perfil);

		$rsPerfilAsignado = locateRecordWhere("sc_usuarios_perfiles", "idperfil = $idperfil and idusuario = :IDUSUARIO");
		if ($rsPerfilAsignado->EOF())
		{
			$bd = new BDObject();
			
			$bd->execQuery("insert into sc_usuarios_perfiles (idperfil, idusuario)
							values($idperfil, :IDUSUARIO)");
						
			$bd->close();
		}
	}

	goToPageView("sc_querysall", $id);
}
?>
<!doctype html>
<html lang="es">

<head>
	<title>Instalar tabla - por SC3</title>

	<?php include("include-head.php"); ?>

</head>

<body>

	<?php
	$req = new FormValidator();
	?>

	<form action="" id="form1" method="post">

		<table class="dlg">
			<tr>
				<td class="td_titulo" colspan="2">Instalar tabla</td>
			</tr>
			<tr>
				<td class="td_etiqueta">Tabla a instalar</td>
				<td class="td_dato">
					<?php

					$cboTablas = new HtmlCombo("tabla", "");
					$cboTablas->addSeleccione();
					$cboTablas->setRequerido();

					$sql = "show tables";
					$rsTbl = new BDObject();
					$rsTbl->execQuery($sql);
					$i = 1;
					while (!$rsTbl->EOF()) {
						$tbl = $rsTbl->getValue(0);
						if (strContiene($tbl, " "))
							$tbl = "`" . $rsTbl->getValue(0) . "`";

						$rsQuery = locateRecordWhere("sc_querys", "table_ = '$tbl'");
						if ($rsQuery->EOF())
							$cboTablas->add($tbl, $tbl);

						$rsTbl->Next();
						$i++;
					}

					echo ($cboTablas->toHtml());
					$req->add("tabla", "Tabla");
					?>
				</td>
			</tr>
			<tr>
				<td class="td_etiqueta">ABM</td>
				<td class="td_dato">
					<?php
					$bInsertar = new HtmlBoolean2("insertar", 1);
					$bEditar = new HtmlBoolean2("editar", 1);
					$bBorrar = new HtmlBoolean2("borrar", 1);
					echo("Insertar: " . $bInsertar->toHtml() . " Editar: " . $bEditar->toHtml() . " Borrar: " . $bBorrar->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">En Menú</td>
				<td class="td_dato">
					<?php
					$selMenu = new HtmlSelector("idmenu", "sb_menuconsola");
					echo($selMenu->toHtml());
					?>
				</td>
			</tr>

			<tr>
				<td class="td_etiqueta">En Perfil</td>
				<td class="td_dato">
					<?php
					$selPerfil = new HtmlSelector("idperfil", "qperfiles");
					echo($selPerfil->toHtml());
					?>
				</td>
			</tr>
			
			<tr>
				<td class="td_etiqueta"></td>
				<td class="td_dato">
					<?php
					$bok = new HtmlBotonOkCancel();
					echo ($bok->toHtml());
					?>
				</td>
			</tr>
		</table>

		<script type="text/javascript">
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