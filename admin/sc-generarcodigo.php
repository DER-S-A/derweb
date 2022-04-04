<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();
?>
<!doctype html>
<html lang="es">

<head>
	<title>Generar codigo - por SC3</title>

	<?php include("include-head.php"); ?>

</head>

<body>

	<table class="dlg">
		<tr>
			<td class="td_titulo" colspan="2">Generar codigo
				<?php echo (linkCerrar(0)); ?>
			</td>

		</tr>

		<tr>
			<td colspan="2" class="td_dato">
				<textarea rows="60">
					<?php
					$mid = RequestInt("mid");
					$mquery = Request("mquery");

					$bd = new BDObject();

					if (sonIguales($mquery, "sc_querysall")) {
						/*	
						$tabla = "gen_personas_direcciones";
						$query = getQueryName($tabla);
						$grupo = "";
						if (!sc3existeTabla($tabla))
						{
							echo("<br>creando <b>$tabla</b>... ");
						*/
						$rsQ = locateRecordMaster();
						$tabla = $rsQ->getValue("table_");

						$code = '$tabla = "' . $tabla . '";
								$query = getQueryName($tabla);
								$grupo = "";
								if (!sc3existeTabla($tabla)) { 
									echo("<br>creando <b>$tabla</b>... ");';
						echo ($code);

						$sql = "show create table $tabla";
						$bd->execQuery($sql);
						echo ('$sql = "' . $bd->getValue("1") . '";
');

						/*		
						$bd = new BDObject();
						$bd->execQuery($sql);

						sc3agregarQuery($query, $tabla, "Direcciones", "", "nombre", 1, 1, 1, "principal desc, nombre", 14, "images/arrow_out.png", 1, "id");
						sc3generateFieldsInfo($tabla, true);
						sc3UpdateRequeridos($tabla);
						sc3AgregarQueryAPerfil($query, "agenda");
						*/

						$code = '// $bd = new BDObject();
$bd->execQuery($sql);
';
						echo ($code);

						$code = 'sc3agregarQuery($query, $tabla, "' . $rsQ->getValue("querydescription") . '", "", "' . $rsQ->getValue("combofield_") . '", ' . $rsQ->getValue("caninsert") . ', ' . $rsQ->getValue("canedit") . ',' . $rsQ->getValue("candelete") . ', "' . $rsQ->getValue("orderby") . '", 14, "' . $rsQ->getValue("icon") . '");
';
						echo ($code);

						$code = 'sc3generateFieldsInfo($tabla, true);
sc3UpdateRequeridos($tabla);
sc3AgregarQueryAPerfil($query, "X");

}
';
						echo ($code);

						$rsTabla = locateRecordWhere("sc_querys", "id = $mid");
						$nombreTabla = $rsTabla->getValue("table_");
						$queryTabla = $rsTabla->getValue("queryname");
						//obtencion de campos
						$rsCampos = locateRecordWhere("sc_fields", "idquery = $mid");
						while (!$rsCampos->EOF()) {
							$field = $rsCampos->getValue("field_");
							$fieldDesc = $rsCampos->getValue("show_name");
							$required = $rsCampos->getValueInt("is_required");
							$default = $rsCampos->getValue("default_value_exp");
							$fileField = $rsCampos->getValueInt("file_field");
							$grupo = $rsCampos->getValue("grupo");
							$subgrupo = $rsCampos->getValue("subgrupo");

							echo "\n" . 'sc3updateField("' . $queryTabla . '", "' . $field . '", "' . $fieldDesc . '", ' . $required . ', "' . $default . '", ' . $fileField . ', "' . $grupo . '");';
							$editable = $rsCampos->getValueInt("is_editable");
							if ($editable == 0)
								echo "\n" . 'sc3updateFieldReadOnly("' . $queryTabla . '", "' . $field . '");';

							if (!esVacio($subgrupo))	
								echo "\n" . "sc3updateFieldSubgrupo('$queryTabla', '$field', '$subgrupo');";
							$ocultarVacio = $rsCampos->getValueInt("ocultar_vacio");
							if ($ocultarVacio == 1)
								echo "\n" . 'sc3updateFieldOcultarVacio("' . $queryTabla . '", "' . $field . '", ' . $ocultarVacio . ');';
							$rsCampos->Next();
						}

						echo "\n\n";
						echo '//referencias entre tablas';
						$SQL = "SELECT ref.in_master, ref.campo_ AS field1, 
										q2.table_ AS table2, 
										q2.keyfield_ AS field2, 
										q2.queryname AS table2_query
								FROM sc_referencias ref 
									left join sc_querys q1 on (ref.idquerymaster = q1.id)
									left join sc_querys q2 on (ref.idquery = q2.id)
								WHERE ref.idquerymaster = $mid";
						$bd->ExecQuery($SQL);
						while (!$bd->EOF()) {
							$row = $bd->getRow();
							echo "\n" . 'sc3addFk("' . $nombreTabla . '", "' . $row["field1"] . '", "' . $row["table2"] . '", "' . $row["field2"] . '", false);';
							echo "\n" . 'sc3addlink("' . $queryTabla . '", "' . $row["field1"] . '", "' . $row["table2_query"] . '", ' . $row["in_master"] . ');';
							echo "\n";
							$bd->Next();
						}
					}

					?>
		</textarea>
			</td>
		</tr>
	</table>

</body>

</html>