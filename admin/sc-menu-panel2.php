<?php 
require("funcionesSConsola.php"); 
checkUsuarioLogueado();

//arma logo.ico
favIconBuild("app/logo.png");


if(function_exists('lcfirst') === false) {
	function lcfirst($str) {
		$str[0] = strtolower($str[0]);
		return $str;
	}
}

function buildMenuExpanding($xid, $xcolWidth, $xcols)
{
	$res = "";
	
	$sec = new SecurityManager();
	$rsTablas = $sec->getRsQuerys($xid);
	
	$cant = $rsTablas->cant();
	$rsOp = $sec->getRsOperaciones($xid);
	$cant += $rsOp->cant();

	if ($cant >= 100)
	{
		$res .= "<thead>
				<tr>
					<th align=\"center\">
						<i class=\"fa fa-filter fa-lg gris\"></i>					
						<input name=\"filter\" size=\"5\" onkeyup=\"Table.filter(this,this)\" title=\"Ingrese un valor a filtrar\">
					</th>
				</tr>
				</thead>";
	}
	
	$res .= "<tbody><tr>";
	$i = 0;
	while (!$rsTablas->EOF()) 
	{
		$url = new HtmlUrl("selitems2.php");
		$url->add("query", $rsTablas->getValue("queryname"));
		$url->add("fstack", "1");
		$url->add("todesktop", "1");
		$item = $rsTablas->getValue("querydescription");
		$icon = $rsTablas->getValue("icon");
		if ($icon == "")
			$icon = "images/table.png";
		$target = "contenido";
		
		if (($i % $xcols == 0) && ($i > 0))
			$res .= "</tr>\n<tr>";
		
		$res .= "<td class=\"td_menuitem\" width=\"$xcolWidth\" title=\"Administrar $item\">
					<a href=\"" . $url->toUrl() . "\" target=\"$target\" class=\"td_toolbarIzq\">
						<img src=\"$icon\" border=0> $item
					</a>
				</td>";

		//abre en ventana aparte, en otra pila
		$url->add("stackname", "sel_" . lcfirst(escapeJsNombreVar($item)));
		$res .= "<td align=left valign=top width=\"20\" title=\"Ver en otra solapa\">
					<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg iconoOtraSolapa\"></i>
					</a>
				</td>";
				
		$rsTablas->Next();
		$i++;
	}
		
	$res .= "</tr><tr>";
	
	$rsTablas = $sec->getRsOperaciones($xid);

	$i = 0;
	while (!$rsTablas->EOF()) 
	{
		$url = new HtmlUrl($rsTablas->getValue("url"));
		$url->add("opid", $rsTablas->getValue("id"));

		$item = $rsTablas->getValue("nombre");
		$icon = $rsTablas->getValue("icon");
		if (esVacio($icon))
			$icon = "images/table.png";
		$favicon = favIconBuild($icon, true);
		$url->add("favicon", $favicon);
				
		$ayuda = $rsTablas->getValue("ayuda");
		$target = $rsTablas->getValue("target");
		if (esVacio($target))
		{
			$target = "contenido";
			$url->add("fstack", "1");
		}
		
		if ($i % $xcols == 0)
			$res .= "</tr>\n<tr>";
			
		$res .= "<td align=left valign=top width=\"$xcolWidth\" title=\"$ayuda\">
					<a href=\"" . $url->toUrl() . "\" target=\"$target\" class=\"td_toolbarIzq\">
						<img src=\"$icon\" border=0> $item
					</a>
				</td>";
		
		//abre en ventana aparte, en otra pila
		$url->add("stackname", "op_" . lcfirst(escapeJsNombreVar($item)));
		$url->add("fstack", "1");
		
		$res .= "<td align=left valign=top width=\"20\" title=\"Ver en otra solapa\">
					<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg iconoOtraSolapa\"></i>
					</a>
				</td>";
		
		$rsTablas->Next();
		$i++;
	}	
	
	$res .= "</tr>
			</tbody>
			<tfooter></tfooter>";
	echo($res);
	
}

?>
<!DOCTYPE html>
<html>
<head>

<title>sc3</title>

<?php include("include-head2.php"); ?>

<script type="text/javascript">

function agrandarMenu()
{
	var iframe = parent.document.getElementById('iframeArriba');
	var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
	innerDoc.hideUnhideMenu();
}

</script>

<style type="">

a
{
	display:block;
	font-size: 13px;
}

a:hover 
{  
	background-color: #eeeeee;
}

.fa:hover
{
	background-color: #607d8b!important;
	color: orange!important;
}

.fa.iconoMenu
{
	color: white;
}

.fa.iconoOtraSolapa
{
	color: #cb769e;
}

body 
{
	margin-left: 1px;
	margin-top: 0px!important;
	overflow-x: hidden;
	background-color:#ffffff!important;
}

.dlg_menu
{
	border:0px;
	width: 230px;
	margin: 0px;
	padding: 0px;
}

.td_toolbarIzq
{
	display: block;
	width: 100%;
	padding: 2px;
}

.td_toolbarIzq:HOVER
{
	background-color: #cdcdcd;
	text-decoration: none;
}


</style>

</head>

<body>

<div class="dlg_menu">

	<?php
	$area = new HtmlExpandingArea("Mis favoritos");
	$area->setTitleClass("expandingcellmenu");
	$area->setClassMenu();
	$area->setInTable(false);
	$area->setWidth(212);
	$area->setAwesomeFont("fa fa-star fa-3x iconoMenu");
	echo($area->start(true));
		
	//recupera los querys que tiene acceso el usuario
	$desk = getEscritorio();
	$sec = new SecurityManager();
	$rsTablas = $sec->getRsFavoritos();
		
	while (!$rsTablas->EOF())
	{
		$tipo = $rsTablas->getValue("tipo");
		if (sonIguales($tipo, "Q"))
		{
			$url = new HtmlUrl("selitems2.php");
			$url->add("query", $rsTablas->getValue("queryname"));
			$url->add("fstack", "1");
			$url->add("todesktop", "1");
			$item = $rsTablas->getValue("querydescription");
			
			//nombre que tendrá la pila, sin el filtro
			$stackname = "sel_" . lcfirst(escapeJsNombreVar($item));
			
			$icon = $rsTablas->getValue("icon");
			if ($icon == "")
				$icon = "images/table.png";
					
			$filters = $rsTablas->getValue("valor2");
			$afilters = explode("--", $filters);
			if (count($afilters) > 1)
			{
				$filtername = $afilters[1];
				$filter =  $afilters[0];
				if (!esVacio($filtername))
				{
					$url->add("filtername", $filtername);
					$url->add("filter", $filter);

					$item .= " (" . $filtername . "...)";
				}
			}
				
			$target = "contenido";
		}
		else
		{
			$url = new HtmlUrl($rsTablas->getValue("queryname"));
			$url->add("opid", $rsTablas->getValueInt("id"));
			$item = $rsTablas->getValue("querydescription");
			$icon = $rsTablas->getValue("icon");
			$target = $rsTablas->getValue("target");
			if (esVacio($target))
			{
				$target = "contenido";
				$url->add("fstack", "1");
			}
			
			$stackname = "op_" . lcfirst(escapeJsNombreVar($item));
		}
			
		echo("<tr><td class=\"td_menuitem\" >");
		echo(href(img($icon, $item) . " " .  $item, $url->toUrl(), $target, "", "td_toolbarIzq"));
		
		//abre en ventana aparte
		$url->add("stackname", $stackname);
		$url->add("fstack", "1");
		
		$favicon = favIconBuild($icon, true);
		$url->add("favicon", $favicon);
		
		$res = "<td align=left valign=top width=\"20\" title=\"Ver en otra solapa\">
					<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg iconoOtraSolapa\"></i>
					</a>
				</td>";
		
		echo("$res</tr>");
			
		$rsTablas->Next();
	}
	echo("<tr><td class=\"td_dato\" colspan=\"2\">");
	echo("<a href=\"sc-adminfavoritos.php\" title=\"Administrar datos y operaciones que aparecen aqui\" ><img src=\"images/addon.gif\"/>...</a>");
	
	echo("</td></tr>");
	
	echo($area->end());
	
	$sec = new SecurityManager();
	$rsmenu = $sec->getMenuSc3();

//	echo("<table align=center>");
	$cols = RequestInt("cols");
	if ($cols == 0)
		$cols = 3;
		
	$innerCols = RequestInt("innercols");
	if ($innerCols == 0)
		$innerCols = 3;
		
	$colWidth = 106;
	if ($innerCols == 1)
	{
		$colWidth = $colWidth * 2;
	}
	$i = 0;		
	while (!$rsmenu->EOF()) 
	{
		$idmenu = $rsmenu->getValue("idItemMenu");
		$url = $rsmenu->getValue("url");
		$item = $rsmenu->getValue("Item");
		$target = $rsmenu->getValue("target");
		$icon = $rsmenu->getValue("icon");
		
		if ($i % $cols == 0)
			echo("</tr><tr>");
			
		$expA = new HtmlExpandingArea($item);
		
		$expA->setAwesomeFont("fa fa-folder-o fa-3x iconoMenu");
		if (!esVacio($icon))
			$expA->setAwesomeFont("fa $icon fa-3x iconoMenu");
				
		$expA->setWidth($colWidth * $innerCols);
		$expA->setInTable(false);
		$expA->setClassMenu();

		$expanded = true;
		if ($i > (($cols * 2) + 1))
			$expanded = false;
		
		if ($innerCols <= 2)
			$expanded = false;
		
		echo("<td valign='top'>");
		$expA->setTitleAlign("left");
		$expA->setTitleClass("expandingcellmenu");
		echo($expA->start($expanded, true));
		
		buildMenuExpanding($idmenu, $colWidth, $innerCols);
		
		echo($expA->end());
		echo("</td>");	
		
		$rsmenu->Next();
		$i++;
	}
	echo("</table>");
	?>

</div>

<?php 
//Arma fav icons de todo, tablas y operaciones para que la navegación usual no tenga que crearlos 
$rsTablas = getRs("select distinct icon from sc_querys");
while (!$rsTablas->EOF())
{
	$icon = $rsTablas->getValue("icon");
	if ($icon == "")
		$icon = "images/table.png";

	//ARMA FAVICON para futuros usos
	favIconBuild($icon);
	
	$rsTablas->Next();
}

$rsTablas->execQuery("update sc_querys set icon='images/table.png' where icon = ''");

$rsTablas = getRs("select distinct icon from sc_operaciones");
while (!$rsTablas->EOF())
{
	$icon = $rsTablas->getValue("icon");

	//ARMA FAVICON para futuros usos
	favIconBuild($icon);

	$rsTablas->Next();
}
?>

</body>
</html>
