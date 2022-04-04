<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//una pila con otro nombre indica que está en una solapa con su propia pila
$stackname = Request("stackname");

//manda al escritorio
$todesktop = RequestInt("todesktop");
$rquery = Request("query");

//se agrega en pila actual
$stack = getStack($stackname);
$stack->addCurrentKey($rquery);
saveStack($stack, $stackname);
$stack->gotoTope();

//Definicion de variables globales a la pagina
$NroPag = 0;
$FromRecNro = 0;
$ToRecNro = 0;

$recuerdaOrden = getParameterInt("sc3-recuerda-orden", 1);
if ($recuerdaOrden)
	$rorderby = requestOrSession("orderby" . $rquery);
else
	$rorderby = Request("orderby" . $rquery);

$rpalabra = Request("palabra");
if (strcmp($rquery, "") == 0)
	echo ("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=queryname</b>");

//recuerda la opcion elegida por el usuario	
$idusuario = getCurrentUser();
$rtop = (int) requestOrParameter("top", "top-$idusuario", "20");

//Manejo de details
$rmquery = Request("mquery");
$rmid = Request("mid");
$mfield = Request("mfield");

//filtros
$filter = RequestInt("filter");
$filtername = Request("filtername");

$query_info = [];
$fk_cache = [];

//cache de operaciones
$aopQuery = "";
$adetQuery = "";

//busca la definicion y el obj en la cache ----------------------------
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else {
	$qinfo = new ScQueryInfo($query_info);
	$tc->saveQueryObj($rquery, $qinfo);
}
saveCache($tc);
//------fin recuperar qinfo ------------------------------------------

//viene nombre de filtro pero no ID
if (($filter == 0) && !esVacio($filtername)) {
	$rsF = locateRecordWhere("sc_querys_filters", "idquery = " . $qinfo->getQueryId() . " and descripcion = '$filtername'");
	$filter = $rsF->getId();
	$rsF->close();
}

//busca filtros especiales del usuario (usado para los permisos)
$rsFilter2 = locateRecordWhere("sc_usuarios_filtros", "idquery = " . $qinfo->getQueryId() . " and idusuario = :IDUSUARIO");
if (!$rsFilter2->EOF())
	$filter = $rsFilter2->getValue("filter");
$rsFilter2->close();

//Escritorio: analiza si lo manda al escritorio (viene del menu)
if ($todesktop == 1) {
	$desk = getEscritorio();
	$desk->addQuery($query_info, $filter, $filtername);
	saveEscritorio($desk);
} else {
	//no va al escritorio, pero hay palabra de búsqueda y la queremos recordar
	$desk = getEscritorio();
	$desk->setQuerySearch($rquery, $filtername, $rpalabra);
	saveEscritorio($desk);
}
$query_info = 0;


//agrega a la pila la info de fantasia para el stack
$stackdesc = $qinfo->getQueryDescription();
if (strcmp($filtername, "") != 0)
	$stackdesc .= " (" . $filtername . ")";
$stack->addExtraInfo($stackdesc, $qinfo->getQueryIcon());
saveStack($stack, $stackname);

//$stack->debug();

//arma favicon y retorna path
$favicon = favIconBuild($qinfo->getQueryIcon(), true);

if (isDetail()) {
	$qminfo = getQueryObj($rmquery);
	$codigo = "" . translateCode($rmid, $rmquery);
	if ($mfield == "")
		$mfield = getFkField($qinfo->getQueryId(), $rmquery);
}

// Setear Variables de Navegacion
//Resuelve en que pagina estamos y setea variable NroPag
if (strcmp(Request("pag"), "") == 0)
	$NroPag = 1;
else
	$NroPag = round(Request("pag"));
if ($NroPag == 0)
	$NroPag = 1;
if ($rtop < 20)
	$rtop = 20;
$PageSize = $rtop;

//pag 1: [1-20], pag 2: [21-40]
$FromRecNro = round(($NroPag - 1) * $PageSize + 1);
$ToRecNro   = round($FromRecNro + $PageSize - 1);

//dado por parametro, se utiliza para hacer el link adelante y atr�s
function getFormBusqueda($xnropag, $ximg, $xaltimg, $xid = "", $xhideSmall = false)
{
	global $rquery;
	global $stackname;
	$href = "sc-selitems.php?query=$rquery&pag=$xnropag&stackname=$stackname";

	$hideSmall = "";
	if ($xhideSmall)
		$hideSmall = " w3-hide-small ";

	$strAux = href("<i class=\"$ximg fa-lg\"></i>", $href, "", $xid,  "w3-button botonPaginado w3-text-white w3-margin-left $hideSmall");
	return $strAux;
}


//Hace el link hacia adelante
function linkAdelante($xnropag)
{
	return getFormBusqueda($xnropag + 1, "fa fa-chevron-right", "Siguiente (CTRL + ->)", "linksiguiente");
}

//Hace el link hacia adelante
function linkMuyAdelante($xnropag)
{
	return getFormBusqueda($xnropag, "fa fa-forward", "Ir Pagina " . $xnropag . " ->>", "", true);
}

//Hace el link hacia adelante
function linkAtras($xnropag)
{
	return getFormBusqueda($xnropag - 1, "fa fa-chevron-left", "Anterior (CTRL + <-)", "linkatras");
}

//Hace el link hacia atras, retrocediendo de a muchas paginas
function linkMuyAtras($xnropag)
{
	return getFormBusqueda($xnropag, "fa fa-backward", "<<- Ir a Pagina " . $xnropag, "", true);
}

//Hace el link hacia la primer pagina
function linkPrimera()
{
	return getFormBusqueda(1, "fa fa-arrow-left", "<<- Primer pagina", "", true);
}

//Hace el link hacia la ultima pagina
function linkUltima($xnropag)
{
	return getFormBusqueda($xnropag, "fa fa-arrow-right", "Ultima pagina ->>", "", true);
}

//obtiene la lista de parametros
function getParams()
{
	global $rquery;
	global $rorderby;
	global $rpalabra;
	global $rmquery;
	global $rmid;
	global $mfield;
	global $stackname;

	$url = new HtmlUrl("");
	$url->add("query", $rquery);
	$url->add("mquery", $rmquery);
	$url->add("mid", $rmid);
	$url->add("mfield", $mfield);
	$url->add("stackname", $stackname);
	$url->add("palabra", $rpalabra);

	return $url->toUrl();
}

/*
Retorna si es detail de otro query
*/
function isDetail()
{
	global $rmquery;
	if ($rmquery == "")
		return false;
	return true;
}

function urlVer($xregistrovalor)
{
	$strResult = "sc-viewitem.php";
	$strResult .= getParams($xregistrovalor) . "&registrovalor=" . $xregistrovalor;
	return $strResult;
}


function linkInsertar($xpermiso = 1)
{
	global $rquery;
	global $rorderby;
	global $NroPag;

	$strResult = "<a id=\"linknuevo\" accesskey=\"n\" title=\"Insertar nuevo dato [INS]\" href=\"";
	$strResult .= "sc-edititem.php";
	$strResult .= getParams() . "&insert=1\">";
	$strResult .= "<i class=\"fa fa-plus-circle fa-2x\"></i>";

	if (esRoot() && ($xpermiso == 0))
		$strResult .= ".";

	$strResult .= "</a>";
	return $strResult;
}



$savedSql = RequestSafe("sql");
if (!sonIguales($savedSql, "")) {
	$sql = getSession($savedSql);
	$sql = addOrderby($sql, $rorderby, TRUE);
} else
	//arma el sql con el sql de la consulta
	$sql = $qinfo->getQuerySql2("", $rpalabra, $rorderby, "", "", $rmquery, $rmid, $mfield, $filter, "", false);

//obtiene un sql simiar pero con count(*), sólo para contar y elimina el ORDER BY
$sqlCount = sc3SqlObtenerCount($sql, $qinfo->getQueryTable());

$rsPpal = new BDObject();
$rsPpal->execQuery($sqlCount);
$cantRows = $rsPpal->getValue("cant_rows");

//resuelve error en caso que se filtre estando en la p�gina N
if ($cantRows < $FromRecNro) {
	$FromRecNro = 1;
	$ToRecNro = $PageSize;
	$NroPag = 1;
}

//hace el query pero sólo recupera los registros segun la pagina actual
$sqlLimit = sc3SqlObtenerLimitXY($sql, $FromRecNro, $PageSize);
$rsPpal->execQuery($sqlLimit);

//Determina cantidad de paginas	
$cantPaginas = floor(($cantRows + $rtop - 1) / $PageSize);
?>
<!doctype html>
<html lang="es">

<head>

	<title>
		<?php echo ($qinfo->getQueryDescription()); ?>
	</title>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

	<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-selitems.js")); ?>"></script>

	<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/sc-selitems.css")); ?>">

	<script language="javascript">
		var queryname = '<?php echo ($rquery); ?>';
		var stackname = '<?php echo ($stackname); ?>';
	</script>

</head>

<body onload="javascript:document.getElementById('palabra').focus();sc3SelectAll('palabra');sc3LoadQueryOperaciones(queryname);">

	<?php
	//si está en otra pila es que abrió en solapa aparte
	if (esVacio($stackname)) {
		echo ('<div class="historial-horizontal">');

		//agrega las filas de tabla con los ultimos querys realizados
		$desk = getEscritorio();
		echo ($desk->showQuerys(false, false, "boton", $rquery, $stackname));
		echo ('</div>');
	}
	?>

	<header class="w3-container headerTitulo">
		<?php
		echo ($stack->showNavigation());

		if ($stack->getCount() < 2) {
			$icon = $qinfo->getQueryIcon();
			echo (img($icon, ""));
			echo (" " . $qinfo->getQueryDescription());
			if (strcmp($filtername, "") != 0)
				echo (" <i>(" . $filtername . ")</i>");
		}

		$sqlSaved = "_sql$rquery";
		//TODO: compactar SQL antes de guardarlo
		setSession($sqlSaved, $sql, 1);
		$urlpdf =   "sc-showpdf.php?query=" . $rquery . "&mquery=" . $rmquery . "&mid=" . $rmid . "&filtername=$filtername&_SQL=" . $sqlSaved;
		$urlcsv =   "sc-selitemscsv.php?query=" . $rquery . "&mquery=" . $rmquery . "&mid=" . $rmid . "&filtername=$filtername&_SQL=" . $sqlSaved;

		if (esVacio($stackname) || $stack->getCount() > 1) {
			echo (linkCerrar(1, $stackname, "w3-right boton-fa-sup"));
		}

		if (!isDetail()) {
		?>
			<a class="w3-right boton-fa-sup w3-hide-small" href="sc-escritorioagregar.php?query=<?php echo ($rquery . "&filter=" . $filter . "&filtername=" . $filtername); ?>" target="contenido" title="Agregar o quitar a Favoritos">
				<i class="fa fa-star"></i>
			</a>
		<?php
		}
		if (esRoot()) {
		?>
			<a class="w3-right boton-fa-sup w3-hide-small" href="sc-importardatos.php?query=<?php echo ($rquery); ?>" title="Importar datos en Excel">
				<i class="fa fa-cloud-upload"></i>
			</a>
		<?php
		}
		?>
		<a class="w3-right boton-fa-sup w3-hide-small" title="Exportar a Excel" href="<?php echo ($urlcsv); ?>" target="_blank">
			<i class="fa fa-file-excel-o" aria-hidden="true"></i>
		</a>

		<a class="w3-right boton-fa-sup" title="Exportar a pdf / imprimir" href="<?php echo ($urlpdf); ?>" target="_blank">
			<i class="fa fa-print" aria-hidden="true"></i>
		</a>

		<a class="w3-right boton-fa-sup w3-hide-small" href="sc-reportar.php?query=<?php echo ($rquery); ?>" target="reportes" title="Búsquedas avanzadas">
			<i class="fa fa-search-plus"></i> Buscar
		</a>

	</header>


	<form name="form1" id="form1" method="get" action="">

		<div class="w3-card w3-light-gray w3-row">

			<div class="w3-halfX">

				<?php
				$hideMedium = "";

				$hidStack = new HtmlHidden("stackname", $stackname);
				echo ($hidStack->toHtml());

				$c = new HtmlCombo("top", $rtop);
				$c->add("20", "20");

				if (!isMobileAgent()) {
					$c->add("40", "40");
				}

				$c->onchangeSubmit();
				$c->setClass("w3-margin-small w3-hide-small");
				echo ($c->toHtml());

				if (!isDetail()) {
					$filters = $qinfo->getFilters();
					if (count($filters) > 0) {
						echo (imgFa("fa-filter", "fa-2x iconoFiltro w3-hide-small", "Filtros"));
						$comboFilter = new HtmlCombo("filter", $filter);
						$comboFilter->add("", " - todos -");
						$comboFilter->cargarArray2($filters, "id", "descripcion2", "");
						$comboFilter->onchange("submitFilter();");
						$comboFilter->setClass("w3-margin-small-bt w3-mobile select-filter");
						echo ($comboFilter->toHtml());

						$hiden = new HtmlHidden("filtername", $filtername);
						echo ($hiden->toHtml());

						//oculta los buscadores si hay filtros, no entran todos en una linea
						$hideMedium = "w3-hide-medium";
					}
				}

				$hidq = new HtmlHidden("query", $rquery);
				echo ($hidq->toHtml());

				$hids = new HtmlHidden("sql", "");
				echo ($hids->toHtml());
				?>

				<span class="w3-tiny w3-margin-small" title="Cantidad de datos">
					#<b><?php echo ($cantRows) ?></b>
				</span>

				<a href="javascript:buscar()" class="w3-margin-small w3-right btn-flat btn-action">
					<i class='fa fa-search '></i> Buscar
				</a>

				<?php
				$pal = new HtmlInputText("palabra", $rpalabra);
				$pal->setSize(12);
				$pal->setMaxSize(50);
				$pal->setAutoselect();
				$pal->setClass("w3-margin-small w3-right");
				echo ($pal->toHtml());
				?>
			</div>

		</div>

	</form>

	<div class="w3-white">

		<?php
		//Operaciones grupales de este query
		$sec = new SecurityManager();
		$rs = $sec->getRsOperacionesQuery($qinfo->getQueryId(), 1);
		$xrecord = array();
		while (!$rs->EOF()) {
			if (isDetail()) {
				$boton = new HtmlBotonToolbar($rs->getRow(), $qminfo->getQueryName(), $rmid, $xrecord, $stackname);
			} else {
				$boton = new HtmlBotonToolbar($rs->getRow(), $qinfo->getQueryName(), $rmid, $xrecord, $stackname);
			}
			$boton->setFlat(true);
			$boton->hideOpId();
			$boton->setInTable(false);
			$boton->setClass("w3-button");
			$str = $boton->toHtml();
			echo ($str);
			$rs->Next();
		}
		?>

	</div>

	<?php
	if (isDetail()) {
	?>
		<div class="w3-text-white w3-padding w3-dark-gray">
			<?php
			echo ($qinfo->getQueryDescription() . " de <b>" . $codigo . "</b>");
			?>
		</div>
	<?php
	}

	$qinfo->startCursor();
	$cantRecs = $FromRecNro;

	debug("cantRows: $cantRows, cantRecs = $cantRecs, fromRecNro = $FromRecNro, ToRecNro = $ToRecNro");

	//INICIO de la grilla de datos
	if (isMobileAgent()) {
		echo ("<div class=\"w3-responsive\">");
	}

	echo ("<table class=\"w3-table-all w3-hoverable\" >\n");

	echo ("<thead >");
	echo ("<tr>");

	//por si hay notas en los registros
	echo ("<th class='grid_header'></th>");

	$hayCampoFile = false;

	// ---- ENCABEZADOS ---------------------------------------------------------------
	$i = 1;
	while ($i < $rsPpal->cantF()) {
		$fieldname = $rsPpal->getFieldName($i);
		if ($qinfo->isFileField($fieldname))
			$hayCampoFile = true;

		$pos = strpos($fieldname, "_fk");
		if (!sonIguales($mfield, $fieldname) && ($pos === FALSE)) {
			$imgOrder = "";
			$orderField = "";
			$orderASC_DESC = "";
			$classOrdenado = "";
			//determina si es "campo desc"
			$aorder = explode(" ", $rorderby);
			if (sizeof($aorder) == 1)
				$orderField = $rorderby;
			else
				$orderField = $aorder[0];

			if (sonIguales($orderField, $fieldname)) {
				if (sizeof($aorder) == 1) {
					$orderASC_DESC = " desc";
					$imgOrder = imgFa("fa-caret-up", "fa-lg", "blanco");
				} else
					$imgOrder = imgFa("fa-caret-down", "fa-lg", "blanco");
				$classOrdenado = "columna-orden";
			}

			getParams();
			$fieldCaption = htmlVisible($qinfo->getFieldCaption($fieldname));
			//oculta campo color, lo usa para colorear la fila
			if (sonIguales($fieldCaption, "Color")) {
				$fieldCaption = "";
			}
			echo ("\r\n<th align=\"center\" class='grid_header header_ordenar $classOrdenado' onclick=\"document.location='sc-selitems.php?query=" . $rquery . "&stackname=$stackname&orderby$rquery=" . $fieldname . $orderASC_DESC . "'\" title=\"Click para ordenar\">$fieldCaption $imgOrder</th>");
		}
		$i++;
	}

	echo ("<th align=\"center\" style=\"width:50px\" colspan=\"1\" class='grid_header'>");
	if ($qinfo->canInsert() || esRoot())
		echo (linkInsertar($qinfo->canInsert()));
	else
		echo (espacio());
	echo ("</th></tr>");
	echo ("</thead>");

	echo ("<tbody>");

	$cantAux = 0;

	if ($rsPpal->EOF()) {
		echo ("<tr>");
		$cols = $rsPpal->cantF() + 1;
		echo ("<td colspan=\"$cols\"><i>(sin datos)</i>" . espacio());
		echo ("</td>");
		echo ("</tr>");
	}

	//recupera el ultimo id visto
	$lastId = (int) getSession("sc3-last-$rquery");
	$valorFijado = (int) getSession("selector-" . $rquery);

	while ((!$rsPpal->EOF()) && ($cantRecs <= $ToRecNro)) {

		$qinfo->addCursor($rsPpal->getValue($qinfo->getKeyField()));
		$keyvalue = $rsPpal->getValue($qinfo->getKeyField());
		$record = $rsPpal->getRow();

		$nota = $rsPpal->getValue("nota_fk");
		$adjunto1 = $rsPpal->getValue("adjunto1_fk");

		$nota_usuario = $rsPpal->getValue("usuario_nota_fk");
		$color = $rsPpal->getValue("color_fk");

		$backColor = "";
		$lastRecord = "";
		if ($lastId == $keyvalue)
			$lastRecord = "<i class=\"fa fa-hand-o-right fa-lg verde\" title=\"&Uacute;ltimo dato visitado\"></i>";
		if ($valorFijado == $keyvalue)
			$lastRecord .= "<i class=\"fa fa-thumb-tack fa-lg gris\"></i>";

		if (!sonIguales($color, ""))
			$backColor = $color;

		//si hay un campo color en la lista, lo usa	
		if (esVacio($backColor)) {
			$color2 = $rsPpal->getValue("color");
			if (!esVacio($color2) && !sonIguales($color2, "#ffffff"))
				$backColor = $color2;
		}

		//campo para el título del menú
		$valorCampoCombo = substr(escapeJsValor($rsPpal->getValue($qinfo->getComboField())), 0, 25);

		//arma el ROW sin los numéricos ni los FK
		$row = $rsPpal->getRow();
		foreach ($row as $key => $value) {
			if (is_int($key)) {
				unset($row[$key]);
			}
			if (endsWith($key, "_fk"))
				unset($row[$key]);
			if (sonIguales($value, "null"))
				$row[$key] = "";
		}
		$row = base64_encode(json_encode($row));

		echo ("<tr id='tr$keyvalue'");
		echo (" onmouseover=\"style.cursor='pointer'\"");
		if (!isMobileAgent())
			echo (" oncontextmenu=\"crearMenuContextual('$keyvalue', '$valorCampoCombo', '$row');return false;\" ");

		//mejor por fila y no por celda
		if (!$hayCampoFile)
			echo (" onclick=\"ver('" . urlVer($keyvalue, true) . "');return true;\"");

		echo (">");

		//TD para las notas
		echo ("<td align=\"center\" bgcolor=\"$backColor\" width=\"16\">");
		if ((!sonIguales($nota, ""))) {
			echo ("<i class=\"fa fa-sticky-note fa-lg gris\" title=\"$nota_usuario: $nota\"></i>");
		}
		if ((!sonIguales($adjunto1, ""))) {
			$icono = "fa-file";

			if (endsWith($adjunto1, "pdf"))
				$icono = "fa-file-pdf-o";

			echo ("<i class=\"fa $icono fa-lg gris\" title=\"Contiene archivos adjuntos\"></i>");
		}

		echo ($lastRecord);
		echo ("</td>");

		$anchoMax = getParameterInt("sc3-grid-trunca-string", 80);
		$i = 1;
		while ($i < $rsPpal->cantF()) {
			$nombreCampo = $rsPpal->getFieldName($i);
			$record = $rsPpal->getRow();
			$pos = strpos($nombreCampo, "_fk");
			if (!sonIguales($mfield, $nombreCampo) && ($pos === FALSE)) {

				$class = "";
				$tipoCampo = $rsPpal->getFieldType($i);
				$dataAlign = getDataAlign($nombreCampo, $tipoCampo, $qinfo->getFieldsRef());
				$fieldCaption = $qinfo->getFieldCaption($nombreCampo);
				$class = $qinfo->getFieldClass($nombreCampo, $record);

				if (sonIguales($dataAlign, "right"))
					$class .= " align-right";

				if (sonIguales($dataAlign, "left"))
					$class .= " align-left";

				if (sonIguales($dataAlign, "middle"))
					$class .= " align-middle";

				echo ("<td valign=\"top\" align=\"" . $dataAlign . "\" class=\"$class\"");

				//TODO: revisar, no funciona en fotos, para que no ingrese con un click si hay un archivo
				//si no hay campo FILE entonces el onclick está por TR
				if (!$qinfo->isFileField($nombreCampo) && $hayCampoFile)
					echo (" onclick=\"ver('" . urlVer($keyvalue, true) . "');return true;\"");

				if ($cantRecs > 12 && ($cantRecs % 3 == 0))
					echo (" title=\"$fieldCaption\"");
				echo (">");
				$valorCampo = $rsPpal->getValue($i);

				if (esVacio($valorCampo) || (sonIguales($valorCampo, "null")))
					echo (espacio());
				else {
					if (strcmp($nombreCampo, "clave") == 0)
						echo ("**********");
					else
				if (strpos($nombreCampo, "email") !== FALSE) {
						//$emailUrl = "<img src=\"images/enviar.gif\" alt=\"$valorCampo\" title=\"$valorCampo\" border=\"0\"/>";
						$emailUrl = imgFa("fa-envelope-o", "fa-2x", "gris", $valorCampo);
						$link = "mailto:" . $valorCampo;
						echo (href($emailUrl, $link));
					} else
				if (strpos($nombreCampo, "web") !== FALSE)
						echo (href($valorCampo, $valorCampo, "_blanck"));
					else
				if ($qinfo->isFileField($nombreCampo))
						echo (sc3getImgSmall(getImagesPath(), $valorCampo, 80, true));
					else
				if ($qinfo->isColorField($nombreCampo))
						echo ("<table width=45 height=15 title=" . $valorCampo . "><tr><td bgcolor=" . $valorCampo . ">&nbsp;</td></tr></table>");
					else
				if (esCampoStr($tipoCampo)) {
						if (sonIguales($nombreCampo, "color")) {
							echo ("");
						} else if (strlen($valorCampo) > $anchoMax) {
							$valorSinComillas = str_replace('"', " ", $valorCampo);
							$valorCampo = "<div title=\"$valorSinComillas\">" . substr(htmlVisible($valorCampo), 0, $anchoMax) . "...</div>";
							echo ($valorCampo);
						} else
							echo (htmlVisible($valorCampo));
					} else
				if (esCampoMemo($tipoCampo)) {
						if (strlen($valorCampo) > $anchoMax) {
							$valorCampo = "<div title=\"$valorCampo\">" . substr($valorCampo, 0, $anchoMax) . "...</div>";
						}
						echo ($valorCampo);
					} else
				if (esCampoFecha($tipoCampo)) {
						$Day = getdate(toTimestamp($valorCampo));
						echo (Sc3FechaUtils::formatFechaGrid($Day));
					} else
				if (esCampoInt($tipoCampo)) {
						echo (htmlVisible(getFKValue2($nombreCampo, $valorCampo, $qinfo->getFieldsRef(), $fk_cache, false, $record)));
					} else
				if (esCampoBoleano($tipoCampo)) {
						if ($rsPpal->getValue($i) == 1)
							echo ("<div class=\"booleano si\">Si</div>");
						elseif ($rsPpal->getValue($i) == 0)
							echo ("<div class=\"booleano no\">No</div>");
						else
							echo ("");
					} else
				if (esCampoFloat($tipoCampo)) {
						echo (formatFloat($valorCampo));
					} else
				if (strcmp($nombreCampo, "clave") == 0)
						echo ("********");
					else
						echo ($valorCampo);
				}
				echo ("</td>");
			}
			$i++;
		}

		$colspan = 1;

		echo ("<td width=\"25\" align=\"center\">");
		//antes menu linkMenu3()	
		echo ("</td></tr>\r\n");
		$rsPpal->Next();
		$cantRecs++;
		$cantAux++;
	}

	echo ("</tbody>");
	echo ("</table>\n");

	//del div responsive
	echo ("</div>\n");

	//para guardar la secuencia del cursor....
	$tc->saveQueryObj($rquery, $qinfo);
	saveCache($tc);

	if (!isset($DESARROLLADOR_NOMBRE))
		$DESARROLLADOR_NOMBRE = "SC3 Sistemas";
	if (!isset($DESARROLLADOR_WEB_SITE))
		$DESARROLLADOR_WEB_SITE = "https://www.sc3.com.ar";
	if (!isset($DESARROLLADOR_LOGO))
		$DESARROLLADOR_LOGO = "images/sc3-logo45x45.png";
	?>

	<div class="divfooter w3-white">

		<a href="<?php echo ($DESARROLLADOR_WEB_SITE); ?>" target="_blank" class="w3-right w3-margin-right w3-margin-left">
			<img src="<?php echo ($DESARROLLADOR_LOGO); ?>" title="<?php echo ($DESARROLLADOR_NOMBRE); ?>" height="35" />
		</a>

		<div id="divperformance" class="w3-tiny w3-display-left w3-margin-left w3-win8-taupe w3-padding-small">

		</div>

		<div class="w3-center">
			<?php
			//Hay que hacer link hacia atr�s
			if ($cantPaginas > 1) {
				//Hay que hacer link hacia atras
				if ($NroPag > 1) {
					//Hay que hacer link hacia atras
					if ($NroPag > 2)
						echo (linkPrimera($NroPag));

					//Hay que hacer link hacia atras, retrocediendo 10 p�ginas	
					if ($NroPag > 11)
						echo (linkMuyAtras($NroPag - 10));
					echo (linkAtras($NroPag));
				}

				//coloca la p�gina actual
				echo ("<span class=\"w3-margin w3-small\"><b>$NroPag</b> de $cantPaginas</span>");
				if ($cantPaginas > 1) {
					echo (linkAdelante($NroPag));
					//Hay que hacer link hacia adelante, avanzando 10 p�ginas
					if ($NroPag < ($cantPaginas - 10))
						echo (linkMuyAdelante($NroPag + 10));

					if ($NroPag < ($cantPaginas - 1))
						echo (linkUltima($cantPaginas));
				}
			}
			?>
		</div>

		<?php
		if (isMobileAgent())
			echo ("</div>");

		$rsPpal->close();
		?>

		<?php
		if (strlen($rpalabra) > 2) {
		?>
			<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", function() {
					var myHilitor = new Hilitor("");
					myHilitor.setMatchType("left");
					myHilitor.apply("<?php echo ($rpalabra); ?>");
				}, false);
			</script>

		<?php
		}
		?>

		<?php include("footer.php"); ?>

</body>

</html>