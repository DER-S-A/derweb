<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//recuerda la opcion elegida por el usuario
$idusuario = getCurrentUser();
saveParameter("menu-fast-$idusuario", "1");


function buildTableMenu($xtitulo, $xid, $xicon)
{
	$prefijo = imgFa($xicon, "fa-lg", "gris", $xtitulo) . " ";
	$class = "";

	$sec = new SecurityManager();
	$rsTablas = $sec->getRsQuerys($xid);

	while (!$rsTablas->EOF()) {
		$url = new HtmlUrl("sc-selitems.php");
		$url->add("query", $rsTablas->getValue("queryname"));
		$url->add("fstack", "1");
		$url->add("todesktop", "1");
		$item = $rsTablas->getValue("querydescription");
		$icon = $rsTablas->getValue("icon");
		if ($icon == "")
			$icon = "images/table.png";
		$target = "contenido";

		echo ("\n<tr>\n	<td class=\"$class\" colspan=\"\">");
		echo ($prefijo . href(img($icon, $item) . " " .  $item, $url->toUrl(), $target));

		$url->add("stackname", "sel_" . lcfirst(escapeJsNombreVar($item)));
		$res = "\n	<td align=left valign=top width=\"20\" title=\"Ver en otra solapa\">
					<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg gris\"></i>
					</a>
				</td>";

		echo ("$res</tr>");

		$rsTablas->Next();
	}

	$rsTablas = $sec->getRsOperaciones($xid);

	while (!$rsTablas->EOF()) {
		$url = new HtmlUrl($rsTablas->getValue("url"));
		$url->add("fstack", "1");
		$url->add("opid", $rsTablas->getValue("id"));

		$item = $rsTablas->getValue("nombre");
		$icon = $rsTablas->getValue("icon");
		$ayuda = $rsTablas->getValue("ayuda");
		if ($icon == "")
			$icon = "images/table.png";
		$favicon = favIconBuild($icon, true);
		$url->add("favicon", $favicon);
		$target = $rsTablas->getValue("target");
		if (esVacio($target))
			$target = "contenido";

		echo ("<tr><td class=\"$class\" colspan=\"\">");
		echo ($prefijo . href(img($icon, $item) . " " . $item, $url->toUrl(), $target) . "<br><small>$ayuda</small>");

		$url->add("stackname", "op_" . lcfirst(escapeJsNombreVar($item)));
		$res = "<td align=left valign=top width=\"20\" title=\"Ver en otra solapa\">
					<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg gris\"></i>
					</a>
				</td>";
		echo ("$res </tr>");

		$rsTablas->Next();
	}
}

?>
<!doctype html>
<html lang="es">

<head>
	<title>Menu rapido</title>

	<?php include("include-head.php"); ?>

	<style type="">

		body 
{
	margin-left: 1px;
	margin-top: 0px;
	background-color: #aaaaaa;
	overflow-x: hidden;
}

.boton_menu
{
	float: right;
	margin: 3px;
	color: #616161!important;
}

.tabla_menu_fast .fa
{
	color: #f0932b;
}

.tabla_menu_fast td
{
	vertical-align: top;
	padding: 8px!important;	
}

.par
{
	background-color: #dedede;
}

</style>

</head>

<body onload="javascript:document.getElementById('filter').focus();" class="body-menu">

	<table class="tabla-logo menu-fijo">
		<tr>
			<td style="width: 100%;">
				<a href="hole.php?fstack=1" class='hover-white' target="contenido" title="Cerrar todas las ventanas y acceder al escritorio">
					<img src="app/logo.png" alt="<?php echo ($SITIO); ?>" title="<?php echo ($SITIO); ?>" style="max-width: 100%; max-height: 66px;">
				</a>
			</td>
			<td>
				<a title="Men&uacute; tradicional" href="sc-menu-panel.php" target="indice" class="boton_menu">
					<i class="fa fa-list fa-lg" title="Men&uacute; r&aacute;pido"></i>
				</a>
			</td>
		</tr>
	</table>

	<table class="w3-table-all tabla_menu_fast" style="margin-top: 70px;">
	
		<thead>
			<tr class="grid_filter">

				<th colspan="2">
					<input type="text" name="filter" id="filter" size="10" onkeyup="Table.filter(this,this)" autocomplete="off" placeholder="buscar..." onclick="sc3SelectAll('filter')" style="width: 95%;" />
				</th>
			</tr>
		</thead>

		<tbody>
			<?php
			$sec = new SecurityManager();
			$rsmenu = $sec->getMenuSc3();

			$i = 1;
			while (!$rsmenu->EOF()) {
				$idmenu = $rsmenu->getValue("idItemMenu");
				$url = $rsmenu->getValue("url");
				$item = $rsmenu->getValue("Item");
				$target = $rsmenu->getValue("target");
				$icon = $rsmenu->getValue("icon");

				buildTableMenu($item, $idmenu, $icon);
				$rsmenu->Next();
				$i++;
			}
			?>
		</tbody>
	</table>

</body>

</html>