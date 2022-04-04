<?php
require("funcionesSConsola.php");
checkUsuarioLogueadoRoot();
?>
<!doctype html>
<html lang="es">

<head>

	<title>SQL</title>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

	<script src="https://www.sc3-app.com.ar/codemirror-5.54.0/lib/codemirror.js"></script>
	<link rel="stylesheet" href="https://www.sc3-app.com.ar/codemirror-5.54.0/lib/codemirror.css">
	<link rel="stylesheet" href="https://www.sc3-app.com.ar/codemirror-5.54.0/theme/material-darker.css">
	<script src="https://www.sc3-app.com.ar/codemirror-5.54.0/mode/sql/sql.js"></script>

	<script type="text/javascript">
		var myCodeMirror = null;

		function cargar() {
			txtArea = document.getElementById("sql");
			myCodeMirror = CodeMirror.fromTextArea(txtArea);
		}

		function agregarTabla(tblName, tblFields) {
			var currentValue = myCodeMirror.getValue();
			console.log(currentValue);
			campo = tblFields.split(",");
			j = 0;
			var res = new Array();
			for (let i = 0; i < campo.length; i++) {
				res[i] = campo[i];

				if (j == 6) {
					res[i] = "\r\n\t" + res[i];
					j = 0;
				}
				j++;

			}
			str = "select " + res + " \r\nfrom " + tblName + "\r\nlimit 10;\r\n\r\n" + currentValue;
			myCodeMirror.getDoc().setValue(str);
		}

		function agregarShow(tblName, tblFields) {
			var currentValue = myCodeMirror.getValue();
			str = "show create table " + tblName + ";\r\n" + currentValue;
			myCodeMirror.getDoc().setValue(str);
		}

		function deleteAll() {
			myCodeMirror.getDoc().setValue("");
		}
	</script>

	<style>
		body {
			background-color: #616161 !important;
		}
	</style>

</head>

<body onload="cargar();">

	<form name="frmsql" action="sc-sql-ejec.php" target="resultado" method="post" id="form1">

		<table class="">
			<td class="" valign="top">
				<textarea name="sql" id="sql" rows="10"></textarea>
			</td>

			<td class="" valign="top" width="15%">
				<input type="text" size="10" maxlength="50" name="key">
				<br />
				<select name="formato">
					<option value="html" selected>HTML</option>
					<option value="texto">Texto separado</option>
				</select>
				<input type="text" name="separator" size="5" maxlength="5" value=",">
				<br />
				<a href="javascript:document.getElementById('form1').submit()" class="btn-flat btn-success">
					<i class="fa fa-database fa-lg"></i> Ejecutar
				</a>
				<br />
				<br />
				<a href="javascript:deleteAll()" class="btn-flat btn-warning">
					<i class="fa fa-trash fa-lg"></i> Borrar
				</a>
			</td>
			</tr>

		</table>
	</form>

</body>

</html>