<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//recuerda la opcion elegida por el usuario
$idusuario = getCurrentUser();

$opid = RequestInt("opid");
$query = Request("query");
$filter = RequestInt("filter");
$filtername = Request("filtername");

function sc3FavoritosAgregarQry($xquery, $xfilter = "", $xfiltername = "")
{
	//	$where = "idusuario = " . getCurrentUser() . " and atributo = 'desktop' and valor1 = '$query' and valor2 = '$filter--$filtername'";
	$where = "idusuario = " . getCurrentUser() . " and atributo = 'desktop' and valor1 = '$xquery' and (valor2 = '$xfilter--$xfiltername' or valor2 = ''  or valor2 = '--')";
	$rs = locateRecordWhere("sc_usuarios_preferencias", $where);
	if ($rs->EOF()) {
		//recupera los querys que tiene acceso el usuario
		$sql = "insert into sc_usuarios_preferencias (idusuario, atributo, valor1, valor2) ";
		$sql .= " values(";
		$sql .= "" . getCurrentUser();
		$sql .= ", 'desktop'";
		$sql .= ", '$xquery'";
		$sql .= ", '$xfilter--$xfiltername'";
		$sql .= ")";
	} else {
		//recupera los querys que tiene acceso el usuario
		$sql = "delete from sc_usuarios_preferencias";
		$sql .= " where " . $where;
	}
	$rs->execQuery($sql);
}


function sc3FavoritosAgregarOp($xopid)
{
	$where = "idusuario = " . getCurrentUser() . " and atributo = 'desktop-op' and valor1 = '$xopid'";
	$rs = locateRecordWhere("sc_usuarios_preferencias", $where);
	if ($rs->EOF()) {
		//recupera los querys que tiene acceso el usuario
		$sql = "insert into sc_usuarios_preferencias (idusuario, atributo, valor1, valor2) ";
		$sql .= " values(";
		$sql .= "" . getCurrentUser();
		$sql .= ", 'desktop-op'";
		$sql .= ", '$xopid'";
		$sql .= ", ''";
		$sql .= ")";
	} else {
		//recupera los querys que tiene acceso el usuario
		$sql = "delete from sc_usuarios_preferencias";
		$sql .= " where " . $where;
	}
	$rs->execQuery($sql);
}


if ($opid != 0) {
	sc3FavoritosAgregarOp($opid);
}

if (!esVacio($query)) {
	sc3FavoritosAgregarQry($query, $filter, $filtername);
}


function buildTableMenu($xtitulo, $xid, $xafavs)
{

	$prefijo = "<small><i>$xtitulo</i></small> / ";
	$class = "td_dato";

	$sec = new SecurityManager();
	$rsTablas = $sec->getRsQuerys($xid);

	while (!$rsTablas->EOF()) {
		$iconfav = "fa-plus-circle";
		$accion = "Agregar";

		$url = new HtmlUrl("sc-selitems.php");
		$query = $rsTablas->getValue("queryname");

		$url->add("query", $query);
		$item = $rsTablas->getValue("querydescription");
		$icon = $rsTablas->getValue("icon");
		if ($icon == "")
			$icon = "images/table.png";
		$target = "contenido";

		echo ("\r\n<tr><td class=\"$class\">");
		echo ($prefijo . img($icon, $item) . " " .  $item);
		echo ("</td><td>");

		//si ya esta en favoritos cambia el icono
		if (in_array("Q:" . $query, $xafavs)) {
			$iconfav = "fa-trash-o";
			$accion = "Quitar";
		}
		$urlf = new HtmlUrl("sc-adminfavoritos.php");
		$urlf->add("query", $rsTablas->getValue("queryname"));
		echo (href(imgFa($iconfav, "fa-lg", "", "$accion $item"), $urlf->toUrl()));
		echo ("</td></tr>");


		//filtros guardados
		$sql = "select id, descripcion from sc_querys_filters where idquery=" . $rsTablas->getValue("id") . " order by descripcion";
		$rsFilters = new BDObject();
		$rsFilters->execQuery($sql);
		while (!$rsFilters->EOF()) {
			$iconfav = "fa-plus-circle";
			$accion = "Agregar";

			$filter = $rsFilters->getValue("descripcion");
			//$icon = "images/filter.gif";

			$urlf = new HtmlUrl("sc-adminfavoritos.php");
			$urlf->add("query", $rsTablas->getValue("queryname"));
			$urlf->add("filter", $rsFilters->getValue("id"));
			$urlf->add("filtername", $filter);

			//si ya esta en favoritos cambia el icono
			if (in_array("Q:" . $query . $filter, $xafavs)) {
				$iconfav = "fa-trash-o";
				$accion = "Quitar";
			}

			echo ("\r\n<tr><td class=\"$class\">");
			echo ($prefijo . img($icon, $item) . " $item ($filter...)");
			echo ("</td><td>");
			echo (href(imgFa($iconfav, "fa-lg", "", "$accion $item / $filter"), $urlf->toUrl()));
			echo ("</td></tr>");

			$rsFilters->Next();
		}

		$rsTablas->Next();
	}

	$rsTablas = $sec->getRsOperaciones($xid);

	while (!$rsTablas->EOF()) {
		$iconfav = "fa-plus-circle";
		$accion = "Agregar";

		$opid = $rsTablas->getValue("id");
		$url = $rsTablas->getValue("url");

		$item = $rsTablas->getValue("nombre");
		$icon = $rsTablas->getValue("icon");
		$ayuda = $rsTablas->getValue("ayuda");
		if ($icon == "")
			$icon = "images/table.png";

		//si ya esta en favoritos cambia el icono
		if (in_array("O:" . $url, $xafavs)) {
			$iconfav = "fa-trash-o";
			$accion = "Quitar";
		}
		echo ("\r\n<tr><td class=\"$class\">");
		echo ($prefijo . img($icon, $item) . " <b>$item</b><br><small>$ayuda</small>");
		echo ("</td><td>");

		$urlf = new HtmlUrl("sc-adminfavoritos.php");
		$urlf->add("opid", $rsTablas->getValue("id"));
		echo (href(imgFa($iconfav, "fa-lg", "", "$accion $item"), $urlf->toUrl()));
		echo ("</td></tr>");

		$rsTablas->Next();
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<title>sc3 - Administrar favoritos</title>

	<?php
	include("include-head.php");
	?>

	<style type="">

		body 
{
	margin-left: 1px;
	margin-top: 0px;
	background-color: #aaaaaa;
	overflow-x: hidden;
}

.tabla_menu_fav .fa
{
	color: #616161;
}

.tabla_menu_fast td
{
	vertical-align: top;
	padding: 10px 5px;	
}

.par
{
	background-color: #dedede;
}

</style>


</head>

<body class="menu_body" onload="javascript:document.getElementById('filter').focus();">

	<table class="w3-table-all tabla_menu_fav">
		<thead>
			<tr>
				<th class="headerTitulo">
					Mis Favoritos
				</th>

				<th class="headerTitulo">
					<a href="sc-menu-panel.php?cols=1&innercols=1">
						<i class="fa fa-list fa-lg fa-fw" title="Men&uacute;"></i>
					</a>
				</th>
			</tr>

			<tr class="grid_filter">
				<th class="grid_filter" colspan="2">
					<input name="filter" id="filter" size="10" onkeyup="Table.filter(this,this)" placeholder="buscar" />
				</th>
			</tr>
		</thead>

		<tbody>
			<?php

			$sec = new SecurityManager();

			$afavs = array();
			$rsFavs = $sec->getRsFavoritos();

			while (!$rsFavs->EOF()) {
				$tipo = $rsFavs->getValue("tipo");
				$fav = $rsFavs->getValue("queryname");

				$filters = $rsFavs->getValue("valor2");
				$afilters = explode("--", $filters);
				if (count($afilters) > 1)
					$fav .= $afilters[1];

				$afavs[] = $tipo . ":" . $fav;
				$rsFavs->Next();
			}

			$rsmenu = $sec->getMenuSc3();
			$i = 1;
			while (!$rsmenu->EOF()) {
				$idmenu = $rsmenu->getValue("idItemMenu");
				$url = $rsmenu->getValue("url");
				$item = $rsmenu->getValue("Item");
				$target = $rsmenu->getValue("target");
				$icon = $rsmenu->getValue("icon");

				$favsm = buildTableMenu($item, $idmenu, $afavs);
				$rsmenu->Next();
				$i++;
			}
			?>
		</tbody>
	</table>

</body>

</html>