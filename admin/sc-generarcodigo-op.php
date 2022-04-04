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
			<?php echo(linkCerrar(0));?>
		</td>

	</tr>

	<tr> 
	<td colspan="2" class="td_dato"> 
		<textarea rows="60"><?php
			$mid = RequestInt("mid");
			$mquery = Request("mquery");
			$rs = locateRecordId("sc_operaciones", $mid);
			
			$url = $rs->getValue("url");
			$nombre = $rs->getValue("nombre");
			$icono = $rs->getValue("icon");
			$ayuda = $rs->getValue("ayuda");
			$bd = new BDObject();

			$sql = "SELECT t1.*, t2.table_ , t3.item
					FROM sc_operaciones t1 
						LEFT JOIN sc_querys t2 on (t1.idquery = t2.id)
						LEFT JOIN sc_menuconsola t3 on (t1.idmenu = t3.idItemMenu)
					WHERE t1.id = $mid";
			$bd->execQuery($sql);
			$table = $bd->getValue("table_");
			$menu = $bd->getValue("item");
			echo('$url = "' . $url . '";');
			echo "\n";
			echo('sc3AgregarOperacion("' . $nombre . '", $url, "' . $icono . '", "' . $ayuda .'", "' . $table . '", "' . $menu . '", 0, "");');	
		?>
		</textarea>
	</td>
	</tr>
</table>

</body>
</html>