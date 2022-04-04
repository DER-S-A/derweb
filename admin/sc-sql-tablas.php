<?php
require("funcionesSConsola.php");
checkUsuarioLogueadoRoot();
?>
<!doctype html>
<html lang="es">

<head>
	<title>SQL - por SC3</title>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

	<style>
		.icono-show {
			border-radius: 3px;
			margin: 3px;
			background-color: #54a0ff;
			padding: 3px;
			color: white;
		}
	</style>

</head>

<script>
	function agregarTabla(tblName, tblFields) {
		ifrm = parent.document.getElementById('query');
		console.log(ifrm);
		ifrm.contentWindow.agregarTabla(tblName, tblFields);
	}

	function agregarShow(tblName, tblFields) {
		ifrm = parent.document.getElementById('query');
		ifrm.contentWindow.agregarShow(tblName, tblFields);
	}
</script>

<body>
	<form name="frmsql" action="ejecsql.php" target="resultado" method="post">

		<table class="">
			<tr>
				<td class="" valign="top">

					<div id="divesquema" style="width:100%; height:100%; overflow: scroll;overflow-x: hidden;">

						<table class="w3-table-all data_table" style="width: 100%;">

							<thead>
								<tr class="grid_filter">
									<th align="center" class="grid_filter">
										<input style="margin-left: 25px;height: 35px;" name="filter" id="filter" size="10" onkeyup="Table.filter(this,this)" title="Ingrese un valor a filtrar" onclick="sc3SelectAll('filter')" />
									</th>
								</tr>
							</thead>

							<tbody>
								<?php
								$sql = "show tables";
								$rsTbl = new BDObject();
								$rsTbl->execQuery($sql);
								$i = 1;
								while (!$rsTbl->EOF()) {
									$tbl = $rsTbl->getValue(0);
									if (strContiene($tbl, " "))
										$tbl = "`" . $rsTbl->getValue(0) . "`";

									$aFields = getFieldsInArray($tbl, "");
									$campos = implode(", ", $aFields);
									echo ("<tr><td class=\"td_dato\">");
									echo ("<i class=\"fa fa-code icono-show\" onclick=\"javascript:agregarShow('$tbl', '$campos')\" title='show create table'></i><a onclick=\"javascript:agregarTabla('$tbl', '$campos')\">$tbl</a>");
									echo ("</td></tr>");
									$rsTbl->Next();
									$i++;
								}
								?>
							</tbody>
						</table>

					</div>

				</td>

		</table>
	</form>

</body>

</html>