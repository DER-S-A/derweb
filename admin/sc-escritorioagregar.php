<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();
?>
<!doctype html>
<html lang="es">

<head>

	<title> Agregar a Favoritos</title>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

</head>

<body onload="document.getElementById('form1').submit()">


	<div class="info-update">
		Agregando a Mis Favoritos ...
	</div>

	<?php
	$query = Request("query");
	$filter = RequestInt("filter");
	$filtername = Request("filtername");

	$where = "idusuario = " . getCurrentUser() . " and 
				atributo = 'desktop' and 
				valor1 = '$query' and 
				valor2 = '$filter--$filtername'";

	$rs = locateRecordWhere("sc_usuarios_preferencias", $where);
	if ($rs->EOF()) {
		//recupera los querys que tiene acceso el usuario
		$sql = "insert into sc_usuarios_preferencias (idusuario, atributo, valor1, valor2) values(";
		$sql .= "" . getCurrentUser();
		$sql .= ", 'desktop'";
		$sql .= ", '$query'";
		$sql .= ", '$filter--$filtername'";
		$sql .= ")";
	} else {
		//recupera los querys que tiene acceso el usuario
		$sql = "delete from sc_usuarios_preferencias 
					where " . $where;
	}

	$bd = new BDObject();
	$bd->execQuery($sql);
	$bd->close();
	?>

	<form action="sc-selitems.php" name="form1" id="form1" method="get">
		<input type="hidden" name="query" value="<?php echo ($query); ?>" />
		<input type="hidden" name="filter" value="<?php echo ($filter); ?>" />
		<input type="hidden" name="filtername" value="<?php echo ($filtername); ?>" />
	</form>

	<?php include("footer.php"); ?>
</body>

</html>