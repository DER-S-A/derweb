<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

//blanquea mensaje
setMensaje("");

//Definicion de variables globales a la pagina
$NroPag = 0;
$PageSize = 0;
$FromRecNro = 0;
$ToRecNro = 0;

//variables del Request
$rorderby = Request("orderby");
$rfilter = Request("filter");
$rquery = Request("query");
$rpalabra = Request("palabra");
$rregistrovalor = RequestInt("registrovalor");

//una pila con otro nombre indica que est� en una solapa con su propia pila
$stackname = Request("stackname");

//analiza si es un master
$rmquery = Request("mquery");
$rmid = Request("mid");
$mfield = Request("mfield");

if (strcmp($rquery,"") == 0)
	echo("<h3>Falta parametro: query</h3> Ej: sc-viewitem.php<b>?query=qquery</b>");
	
//......recuperar qinfo....
//busca la definicion y el obj en la cache
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else
{
	$qinfo = new ScQueryInfo($query_info);
	$tc->saveQueryObj($rquery, $qinfo);	
}	
saveCache($tc);
//.......fin recuperar qinfo

//arma fav icon, asume que existe
$favicon = favIconBuild($qinfo->getQueryIcon(), true);
//va a guardar todo el contenido en la cache (de archivos)
$fileCache = new ScFileCache();

//analiza si viene de la navegacion de un cursor
$hayCursor = false;
if (($rregistrovalor == 0) && (sonIguales(Request("registrovalor"), "NAV")))
{
	//borra cache para que al salir esta marcado el registro visitado
	$fileCache->clear($stackname);
	
	$hayCursor = true;
	$navlast = RequestInt("navlast");
	$navseq = RequestInt("navseq");

	if ($navseq == 1) 
		$rregistrovalor = $qinfo->nextCursorId($navlast);
	if ($navseq == -1)
		$rregistrovalor = $qinfo->prevCursorId($navlast);
}

$query_info = 0;
$grupos = getGruposArray($qinfo->getFieldsDef());
$fk_cache = Array();

//manejo de stack, apila el url actual
$stack = getStack($stackname);
if ($hayCursor) 
	$stack->desapilar();
$stack->addCurrentKey($rquery . $rregistrovalor);
saveStack($stack, $stackname);


//$stack->debug();


//Si estq el parametro fstack=1 no hay navegadores, viene del escritorio
if (isset($_REQUEST["fstack"]) && $_REQUEST["fstack"] == 1)
{
	$qinfo->startCursor();
}

$fileCache->start($stack->getCount(), $stackname);	

$rsPpal = new BDObject();
$sql = $qinfo->locateRecordSql2($rregistrovalor);
$rsPpal->execQuery($sql);
$record = $rsPpal->getRow();

//guarda ultimo registro visto
setSession("sc3-last-$rquery", $rregistrovalor);
setSession("sc3-last-$rquery-desc", substr($rsPpal->getValue($qinfo->getComboField()), 0, 25));

//obtiene la lista de parametros
function getParams($xregistrovalor = 0)
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
	$url->add("registrovalor", $xregistrovalor);
	$url->add("stackname", $stackname);
	return $url->toUrl();
}

function linkEditarFa($xregistrovalor, $xflat = false, $xopPorRoot = false)
{
	$color = "";
	if ($xopPorRoot)
		$color = "naranja";
	
	$strResult = "<a href=\"sc-edititem.php" . getParams($xregistrovalor) . "\" class=\"boton\" id=\"linkeditar\" title=\"Editar [F2]\">
						<i class=\"fa fa-pencil-square-o fa-2x $color\" ></i>
						<br><span class=\"w3-tiny\">Editar</span>
					</a>";
			
	return $strResult;
}


function linkLog($xregistrovalor, $xflat = false)
{
	$strResult = "<a href=\"";
	$strResult .= "sc-log-item.php";
	$strResult .= getParams($xregistrovalor) . "\" title='Auditar los cambios realizados' class=\"td_toolbar_flat\"><img src='./images/scauditar.gif' border=0>";
	if (!$xflat)
		$strResult .= "<br />";	
	else
		$strResult .= " ";	
	$strResult .= "Auditar</a>";
	return $strResult;
}

function linkAgendar($xquery, $xregistrovalor, $xflat = false)
{
	$strResult = "<a href=\"";
	$strResult .= "sc-calendaritem.php?mquery=$xquery&mid=$xregistrovalor\" title='Agendar este elemento' class=\"td_toolbar_flat\"><img src='./images/scagenda.png' border=0>";
	if (!$xflat)
		$strResult .= "<br />";	
	else
		$strResult .= " ";	
	$strResult .= "Agendar</a>";
	return $strResult;
}


function linkNautilus($xquery, $xregistrovalor)
{
	$strResult = "<a href=\"";
	$strResult .= "sc-infoquery.php?mquery=$xquery&mid=$xregistrovalor\" title='Info Nautilus' target=\'infonautilus\' class=\"td_toolbar_flat\"><img src='./images/scinfo.gif' border=0>";
	$strResult .= " Info Nautilus</a>";
	return $strResult;
}

function linkConfTabla()
{	
	global $qinfo;
	$xregistrovalor = $qinfo->getQueryId();
	$xquery = "sc_querysall";
	$strResult = "<a href=\"";
	$strResult .= "sc-viewitem.php?query=$xquery&registrovalor=$xregistrovalor&stackname=sc3_config\" title='Configurar tabla' target=\configurartabla\' class=\"td_toolbar_flat\"><img src='./images/scinfo.gif' border=0>";
	$strResult .= " Config tabla</a>";
	return $strResult;
}

function linkBorrarFa($xregistrovalor, $xflat = false, $xopPorRoot = false)
{
	$color = "";
	if ($xopPorRoot)
		$color = "naranja";
	
	$str = "<a class=\"boton\" href=\"javascript:dardebaja(" . $xregistrovalor . ")\" id=\"linkborrar\" title=\"Borrar este registro [Supr]\">
				<i class=\"fa fa-trash-o fa-2x $color\" ></i>
				<br><span class=\"w3-tiny\">Borrar</span>
		    </a>";
	return $str;
}


function linkPrintFa($xquery, $xsql, $xregistrovalor, $xclass)
{
	return "<a class=\"$xclass\" title=\"Imprimir\" href=\"sc-printitempdf.php?query=" . $xquery . "&sql=" . saveSessionStr($xsql) . "&mid=" . $xregistrovalor . "\" target=\"_blank\">
				<i class=\"fa fa-print fa-lg \" ></i>
			</a>";
}


function linkCopiarFa($xquery, $xid, $xopPorRoot = false, $xstackname = "")
{
	$color = "";
	if ($xopPorRoot)
		$color = "naranja";

	return "<a  class=\"boton\"  title=\"Copiar\" href=\"sc-edititem.php?query=" . $xquery . "&stackname=$xstackname&registrovalor=$xid&insert=1\">
				<i class=\"fa fa-copy fa-2x $color\" ></i>
				<br><span class=\"w3-tiny\">Copiar</span>
			</a>";
}


function linkExcel($xquery, $xsql)
{
	return "<a class=\"td_toolbar_flat\" title=\"Exportar a Excel [ALT + SHIFT + e]\" href=\"sc-viewitemcsv.php?query=" . $xquery . "&format=excel&sql=" . saveSessionStr($xsql) . "\" target=\"_blank\"><img src=\"images/excell.jpg\" border=\"0\" title=\"Exportar a Excel\"> Exportar a Excel</a>";
}				  


function linkNotasFa($xquery, $xid, $xstackname = "")
{
	return "<a class=\"boton\"  title=\"Editar notas o color [F4]\"  id=\"linknotas\" href=\"sc-extrainfo2.php?mquery=$xquery&mid=$xid&stackname=$xstackname\">
				<i class=\"fa fa-sticky-note fa-2x \" ></i>
				<br><span class=\"w3-tiny\">Nota</span>
			</a>";
}


/**
 * 
 * @param unknown_type $xkey
 * @param unknown_type $xrecord
 * @param unknown_type $xnro
 * @param ScQueryInfo $xquery
 * @param unknown_type $xsql
 * @return string
 */
function linkMenu($xkey, $xrecord, $xnro, $xquery, $xsql, $xstackname = "")
{
	global $qinfo; 
	//divToolbar
	$str = "<table class=\"menuEditar\">";

	$verlog = false;

	$str .= "<tr>
			 <td>";
	
	//primero las operaciones fijas
	$str .= "\n<td width=\"25%\" align=\"center\">";
	if ($qinfo->canEdit() || esRoot())
	{
		$opPorRoot = !$qinfo->canEdit() && esRoot();
		$str .= linkEditarFa($xkey, true, $opPorRoot);
	}
	$str .= "</td>";
	
	$str .= "\n<td width=\"25%\" align=\"center\">";
	if ($qinfo->canInsert() || esRoot())
	{
		$opPorRoot = !$qinfo->canInsert() && esRoot();
		$str .= linkCopiarFa($xquery, $xkey, $opPorRoot, $xstackname);
	}
	$str .= "</td>";

	$str .= "\n<td width=\"25%\" align=\"center\">";
	if ($qinfo->canDelete() || esRoot())
	{
		$opPorRoot = !$qinfo->canDelete() && esRoot();
		$str .= linkBorrarFa($xkey, true, $opPorRoot);
	}
	$str .= "</td>";
	
	$str .= "\n<td width=\"25%\" align=\"center\">";
	$str .= linkNotasFa($xquery, $xkey, $xstackname);
	$str .= "</td>";
	
	$str .= "</tr>
		</table>";
	
	if ($qinfo->canInsert() || $qinfo->canEdit() || esRoot())
		$verlog = true;

	$sec = new SecurityManager();


	$area1 = new HtmlAccordeon("Operaciones", "fa fa-bolt fa-2x fa-fw iconoMenu");
	$area1->setHeaderClass("sc3-accordion menuDerecho");
		
	//Operaciones relacionadas a este registro
	$rs = $sec->getRsOperacionesQuery($qinfo->getQueryId());

	$idop = 10;
	while (!$rs->EOF())
	{
		$boton = new HtmlBotonToolbar($rs->getRow(), $qinfo->getQueryName(), $xkey, $xrecord, $xstackname);
		$boton->setFlat(true);
		if ($rs->getValueInt("emergente") == 1)
			$boton->setEmergente();
		$boton->setInTable(false);
		$area1->addDiv($boton->toHtml($idop), "menu-item");
		$rs->Next();
		$idop++;
	}
	
	$rs->close();

	if ($idop > 10)
		$str .= $area1->toHtml(true);

    // ----------------------- DETALLES ------------------------------------------------------
	$area1 = new HtmlAccordeon("Detalles", "fa fa-sitemap fa-2x fa-fw iconoMenu");
	$area1->setHeaderClass("sc3-accordion menuDerecho");
	
	$i = 0;
	$rs = $sec->getRsOperacionesRelacionadas($qinfo->getQueryId());
	$totalDetalles = 0;
	while (!$rs->EOF())
	{
	    $op = array();
	    $count = "";
	    //parametrizable, muestra la cantidad de detalles...
	    if (getParameterInt("sc3-count-detalles", "1"))
	    {
	        $detQuery = $rs->getValue("queryname");
	        //metadata: recupera de la cache el obj del query info
	        $qinfoDetalle = getQueryObj($detQuery);
	        $sql2 = $qinfoDetalle->getRecordCount($rs->getValue("mfield"), $xkey);
	        
	        $rsCount = new BDObject();
	        $rsCount->execQuery($sql2);
	        $count = $rsCount->getValue("cant");
			$totalDetalles += $count;
			
			$count2 = "";
			if ($count > 0)
	        	$count2 = " " . span($count, "span-cantidad");
	    }
	    
	    $op["url"] = "sc-selitems.php?query=" . $rs->getValue("queryname") . "&stackname=$xstackname";
	    $op["icon"] = $rs->getValue("icon");
	    if (sonIguales($op["icon"], ""))
	        $op["icon"] = "images/table.png";
	        
        $op["ayuda"] =  $rs->getValue("querydescription") . " de " . $xrecord[$qinfo->getComboField()];
        $op["nombre"] = $rs->getValue("querydescription") . "$count2";
        
        $boton = new HtmlBotonToolbar($op, $qinfo->getQueryName(), $xkey, $xrecord);
        $boton->setFlat(true);
        $boton->setInTable(false);
        $boton->hideOpId();
        
        $area1->addDiv($boton->toHtml(), "menu-item");
        $rs->Next();
        $i++;
	}

	$rs->close();
	
	if ($i > 0)
	{
		if ($totalDetalles == 0)
			$str .= $area1->toHtml(false);
		else
			$str .= $area1->toHtml(true);
	}	
	
	// ---------------- DATOS RELACIONADOS -----------------------------------------------------
	/*
	$area1 = new HtmlAccordeon("Datos relacionados", "fa fa-link fa-2x fa-fw iconoMenu");
	$area1->setHeaderClass("sc3-accordion menuDerecho");
	
	//Masters del registro actual
	$hayRelacionado = false;
	$sec = new SecurityManager();
	$rs = $sec->getRsOperacionesMasters($qinfo->getQueryId());
	
	$i = 0;
	while (!$rs->EOF())
	{
		$hayRelacionado = true;

	    $fieldfk = $qinfo->getFieldToFk($rs->getValue("queryname"));
	    $fkvalue = $xrecord[$fieldfk];
	    $op = array();
	    $op["url"] = "sc-viewitem.php?query=" . $rs->getValue("queryname") . "&registrovalor=$fkvalue&stackname=$xstackname";
	    $op["icon"] = $rs->getValue("icon");
	    if (sonIguales($op["icon"], ""))
	        $op["icon"] = "images/table.png";
	        
		$op["ayuda"] = "Ver en detalle a '" . $xrecord[$fieldfk . "_fk"] . "'";
		$op["nombre"] = $rs->getValue("querydescription");
		if (sonIguales($fkvalue, ""))
		{
			$op["ayuda"] = "No hay un dato de " . $rs->getValue("querydescription") . " cargado para '" . $xrecord[$qinfo->getComboField()] . "'";;
			$op["condicion"] = "XXXcondicion = false;";
			$op["condicion"] = str_replace("XXX", "$", $op["condicion"]);
		}
		
		//arma el boton sin Master query ni master id
		$boton = new HtmlBotonToolbar($op, "", "", $xrecord);
		$boton->setFlat(true);
		$boton->setInTable(false);
		$area1->addDiv($boton->toHtml(), "menu-item");
		
		$rs->Next();
		$i++;
	}

	$rs->close();
	
	if ($hayRelacionado)
		$str .= $area1->toHtml();
	*/

	// -------------------MAS HERRAMIENTAS ------------------------------------
	$area1 = new HtmlAccordeon("Mas herramientas", "fa fa-cog fa-2x fa-fw iconoMenu");
	$area1->setHeaderClass("sc3-accordion menuDerecho");
	
	if (esRoot())
	{
		$area1->addDiv(linkNautilus($xquery, $xkey, true), "menu-item");
		$area1->addDiv(linkConfTabla(), "menu-item");
	}
	
	if ($verlog)
	{
	    $boton3 = new HtmlBotonToolbar("", "", "", "");
	    $boton3->setUrl(linkLog($xkey, true));
	    $boton3->setFlat(true);
	    $boton3->setInTable(false);
		$area1->addDiv($boton3->toHtml(), "menu-item");
	}
	$str .= $area1->toHtml();
	
	return $str;
}

?>
<!doctype html>
<html lang="es">
<head>

<?php 
$SC3_EVITAR_PRECARGA = 1;
include("include-head.php"); 
?>

<script type="text/javascript">
	document.write('<style type="text/css">.tabber{display:none;}<\/style>');
</script>

<style type="text/css">

.menu-item 
{
	height: 30px;
	text-align: left;
}

.menuEditar
{  
    float:left; 
    width: 230px; 
    margin: 1px;

	font-size: 13px;
	font-style: normal;
	line-height: normal;
	
	text-decoration: none;
	padding: 5px;
	background-color: #ffffff;

	color:#607d8b;
}

.boton
{
	display: block;
	padding: 3px;
}

.boton:HOVER
{
	background-color: #cdcdcd;
	border-radius: 6px;
}

</style>

<script language="javascript">

	function dardebaja(xrecid)
	{
		if (confirm("Esta seguro que desea borrar este dato ?"))
		{
			pleaseWait2();
			document.location.href = "sc-delitem.php<?php echo(getParams()) ?>" + "&registrovalor=" + xrecid
		}
	}
	
	//expande un atributo con el nombre dado
	function expand(x_strExpand, x_strExpansor, xlin, xnombre) 
	{
		styleObj = document.getElementById(x_strExpand).style;
		styleLin = document.getElementById(xlin).style;
		if (styleObj.display == 'none') 
		{
			styleObj.display = '';
			styleLin.display = 'none';
			document.getElementById(x_strExpansor).innerHTML = '- ' + xnombre;
		}
		else 
		{
			styleObj.display = 'none';
			styleLin.display = '';
			document.getElementById(x_strExpansor).innerHTML = '+ ' + xnombre;
		}
	}
	
	
	function expandMaster(x_strExpand, x_strExpansor, xnombre) 
	{
		styleObj = document.getElementById(x_strExpand).style;
		if (styleObj.display == 'none') 
		{
			styleObj.display = '';
		}
		else 
		{
			styleObj.display = 'none';
		}
	}
	
</script>
		
<title><?php echo($qinfo->getQueryDescription()); ?></title>

</head>
<body>

<?php
//si está en otra pila es que abrió en solapa aparte
if (esVacio($stackname))
{
	echo('<div class="historial-horizontal">');
	
	//agrega las filas de tabla con los ultimos querys realizados
	$desk = getEscritorio();
	echo($desk->showQuerys(false, false, "boton", $rquery, $stackname));		  
	echo('</div>');
}
?>

<header class="w3-container headerTitulo">
	<?php
	//agrega a la pila la info de fantasia para el stack
	$registroDesc = resumirTexto(str_replace("'", " ", $rsPpal->getValue($qinfo->getComboField())), 30);

	$stack->addExtraInfo($qinfo->getQueryDescription() . " (" . htmlVisible($registroDesc) . ")", $qinfo->getQueryIcon());
	saveStack($stack, $stackname);
	echo($stack->showNavigation(0));

	
	$h1 = new HtmlUrl("sc-viewitem.php");
	$h1->add("query", $rquery);
	$h1->add("registrovalor", "NAV");
	$h1->add("navlast", $rregistrovalor);
	$h1->add("navseq", "-1");
	$h1->add("stackname", $stackname);
	$linkAnt = href("<i class=\"fa fa-arrow-left fa-lg\"></i>", $h1->toUrl(), "", "linkatras", "w3-right w3-text-white w3-button boton-fa-sup");
	
	$h2 = new HtmlUrl("sc-viewitem.php");
	$h2->add("query", $rquery);
	$h2->add("registrovalor", "NAV");
	$h2->add("navlast", $rregistrovalor);
	$h2->add("navseq", "1");
	$h2->add("stackname", $stackname);
	$linkProx = href("<i class=\"fa fa-arrow-right fa-lg\"></i>", $h2->toUrl(), "", "linksiguiente", "w3-right w3-text-white w3-button boton-fa-sup");
	
	//quizás esto en la pila!
	if ($stack->getCount() > 1)
		echo(linkCerrar(1, $stackname, "w3-button w3-text-white w3-right boton-fa-sup"));
	
	if ($qinfo->hayNextCursor($rregistrovalor) && ($stack->getCount() < 3))
		echo($linkProx);
	
	if ($qinfo->hayPrevCursor($rregistrovalor) && ($stack->getCount() < 3))
		echo($linkAnt);

	echo(linkPrintFa($rquery, $sql, $rregistrovalor, "w3-button w3-text-white w3-right boton-fa-sup"));
	?>
</header>

<div class="w3-responsive">

<table class="dlg" id="tbldlg" style="background-color:#f1f1f1;">

	
<!--           Fin de barra superior, comienzo del body de datos            -->

	<tr>
		<td>
        
<?php
$tabs = new HtmlTabber();
$sec = new SecurityManager();
$rs = $sec->getRsOperacionesRelacionadas($qinfo->getQueryId(), 1);

echo("<div class=\"solapainterior\">");
echo($tabs->startTabs());
echo($tabs->startSolapa($qinfo->getQueryDescription()));

?>

<table width="100%" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td valign="top">

	<table width="99%">
	<tr>
		<td valign="top">
		
		<?php
		//busca info extra, color y nota !
		$rsExtra = locateRecordWhere("sc_adjuntos", "iddato = $rregistrovalor and idquery  = " . $qinfo->getQueryId());
		$adjunto1 = "";
		$adjunto2 = "";
		if (!$rsExtra->EOF())
		{
			$col = $rsExtra->getValue("color");
			$nota = $rsExtra->getValue("nota");
			$nota_usuario = $rsExtra->getValue("usuario");
			$adjunto1 = $rsExtra->getValue("adjunto1");
			$adjunto2 = $rsExtra->getValue("adjunto2");
			
			if (!sonIguales($col . $nota . $adjunto1 . $adjunto2, ""))
			{
				echo("<tr><td colspan=\"2\" style=\"padding:5px\">");
				if (!sonIguales($nota, ""))
				{
					$nota = htmlVisible($nota);							
					echo("<i class=\"fa fa-sticky-note fa-2x\" style=\"color:$col\"></i>  <b>$nota_usuario</b>: $nota ");
				} 						
				if (!esVacio($adjunto1))
				{
					$f1 = new HtmlInputFile("f1", $adjunto1);
					$f1->setReadOnly(true);
					echo($f1->toHtml());
				}
				if (!esVacio($adjunto2))
				{
					$f2 = new HtmlInputFile("f2", $adjunto2);
					$f2->setReadOnly(true);
					echo($f2->toHtml());
				}
				echo("</td></tr>");
			}
		}
		?>
		
		<tr>
			<td colspan="2">
				<div class="divContainer" id="divContainer1">

					<?php 
		
		$cantGrupos = 0;
		//recorre los grupos de datos
		foreach ($grupos as $grupoActual)
		{
			$area1 = new HtmlDivDatos($grupoActual);
			$area1->setExpandible(true);
			$i = 0;

			$hayDatosGrupo = false;
			$subGrupoActual = "";

			$aCamposValores = [];
			
			//recorre todos los campos y solo muestra los del grupo = $grupoActual
			while ($i < $rsPpal->cantF())
			{
				$nombreCampo = ($rsPpal->getFieldName($i));
				$fieldGroup = $qinfo->getFieldGrupo($nombreCampo);
				$fieldSubGroup = $qinfo->getFieldSubgrupo($nombreCampo);
				$class = $qinfo->getFieldClass($nombreCampo, $record);
				$visible = $qinfo->getFieldVisible($nombreCampo);
				$ocultarVacio = $qinfo->getFieldOcultarVacio($nombreCampo);

				//sin subgrupo significa que va solo en su par "Etiqueta: Valor"
				if (esVacio($fieldSubGroup))
					$fieldSubGroup = "sg$i";

				$pos = strpos($nombreCampo, "_fk");
				if ($pos === FALSE && ($visible == 1))
				{
					//analiza si el campo esta en el grupo que estoy mostrando
					if (sonIguales($grupoActual, $fieldGroup)
							|| (sonIguales("", $fieldGroup) && sonIguales("Datos", $grupoActual)))
					{
						$etiqueta = htmlVisible($qinfo->getFieldCaption($nombreCampo));
						$valor = "";
						
						$valorsel = $rsPpal->getValue($i);
						$tipo = $rsPpal->getFieldType($i);
						$decimales = $rsPpal->getFieldDecimals($i);
						
						//por los float que queden "#$%&/
						if ($decimales > 4)
							$decimales = 2;

						$queryMaster = $qinfo->getRelatedQueryToFk($nombreCampo);
						if (sonIguales($queryMaster, ""))
							$valor = $qinfo->showField($rsPpal, $nombreCampo, $tipo, false, $decimales);
						else
						{
							//arma tabla de preview del registro relacionado
							$valorFk = $rsPpal->getValue($nombreCampo . "_fk");
							
							$qinfoM = getQueryObj($queryMaster);
							$aColores = $qinfoM->getColorDatoRelacionado($valorsel);

							//Solo si puede ver la tabla MASTER se arma link a la misma
							// sinó que se conforme con el valor de combo
							if ($sec->tienePermisoQuery($queryMaster) && !esVacio($valorsel))
								$valor = href($valorFk, "sc-viewitem.php?query=$queryMaster&registrovalor=$valorsel&stackname=$stackname");
							else
								$valor = $valorFk;

							//hay un color en el dato relacionado
							if (!esVacio($aColores["bgcolor"]))	
							{
								$valor = "<span style='padding: 3px 8px; border-radius: 3px;background-color: " . $aColores["bgcolor"] . ";color: " . $aColores["color"] . "'>$valor</span>";
							}
						}

						if (!esVacio($valorsel))
							$hayDatosGrupo = true;

						//si es vacio y oculta los vacios no se muestra	
						if (!esVacio(trim($valor)) || ($ocultarVacio == 0))	
							$aCamposValores[$fieldSubGroup][] = [$etiqueta, $valor, $class];
					}
				}
				$i++;
			}

			//Recorre todos los pares de etiqueta-valor armados del grupo
			foreach ($aCamposValores as $aPar)
			{
				//clásico: un par Etiqueta: valor, clase
				if (count($aPar) == 1)
				{
					$par = $aPar[0];
					$area1->add($par[0] . ":", $par[1], $par[2]);
				}
				else
				{
					//hay varios pares "Etiqueta: valor" del mismo subgupo
					$contenido = "";
					$clase = "";
					foreach ($aPar as $par)
					{	
						$contenido .= div($par[0] . ":", "info-etiqueta");
						$contenido .= div($par[1], "info-dato");
						$clase .= " " . $par[2];
					}

					$contenido = div($contenido, "informacion $clase");
					$area1->add("", $contenido);
				}
			}

			if (!$hayDatosGrupo)
				$area1->setExpandida(false);
			
			echo($area1->toHtml());
			$cantGrupos++;
		}	

		//busca datos relacionados
		$rsLinks = getSqlLinks1($rquery, $rregistrovalor);
		if (!$rsLinks->EOF())
		{
			$cantGrupos++;
			
			$area1 = new HtmlDivDatos("Otros datos");
				
			$id = 1;
			while (!$rsLinks->EOF())
			{
				$link2QryDesc = $rsLinks->getValue("querydescription");
				$link2Qry = $rsLinks->getValue("queryname");
				$link2Id = $rsLinks->getValueInt("id2");

				$selL = new HtmlSelector("link$id", $link2Qry, $link2Id);
				$selL->setReadOnly();

				$area1->add($link2QryDesc, $selL->toHtml());
				
				$rsLinks->Next();
				$i++;
			}
			echo($area1->toHtml());
		}

		$rsPpal->close();
		?>
    
    			</div>
			</td>
	</tr>
		
	<tr>
		<td class="td_dato" align="center">
			<input type="button" value="Salir" name="bcancelar" class="btn" accesskey="0" onclick="javascript:document.location = 'hole.php?anterior=1&stackname=<?php echo($stackname); ?>'" />
		</td>
	</tr>              
    </table>
    
    </td>
    
    <td align="right" valign="top" width="20%">
		<?php 
		$record["adjunto1"] = $adjunto1;
		echo(linkMenu($rregistrovalor, $record, "1", $rquery, $sql, $stackname)); 
		?>
	</td>
	
  </tr>
</table>

<?php
echo($tabs->endSolapa());

//incluye los detalles, solo los que van con el master in_master = 1
$i = 0;
while (!$rs->EOF())
{
	$detQuery = $rs->getValue("queryname");
	$detMfield = $rs->getValue("mfield");
	$detQinfo = getQueryObj($detQuery);
	$detIcon = $rs->getValue("icon");
	$detNombre = $rs->getValue("querydescription");

	$count = "";
	$count2 = "";
	$qinfoDetalle = getQueryObj($detQuery);
	
	//parametrizable, muestra la cantidad de detalles...
	if (getParameterInt("sc3-count-detalles", "1"))
	{
		$sql2 = $qinfoDetalle->getRecordCount($detMfield, $rregistrovalor);

		$rsCount = new BDObject();
		$rsCount->execQuery($sql2);
		$count = $rsCount->getValue("cant");
		if ($count > 0)
			$count2 = " (" . $count . ")";
	}		

	if ((getParameterInt("sc3-count-detalles", "1") == 1 && $count > 0) || $qinfoDetalle->canInsert())
	{
		echo($tabs->startSolapa($detNombre . $count2, $detIcon));
		
		$url = new HtmlUrl("sc-showgrid.php");
		$url->add("query", $rs->getValue("queryname"));
		$url->add("mquery", $qinfo->getQueryName());
		$url->add("mid", $rregistrovalor);
		$url->add("mfield", $detMfield);
		
		?>
		<iframe width="100%" height="500" src="
		<?php 
												echo($url->toUrl()); 
												?>" frameborder="0" id="<?php echo($rs->getValue("queryname")); ?>"></iframe>
		<?php
		echo($tabs->endSolapa());
	}
	$rs->Next();
	$i++;
	$cantGrupos++;
}
	
echo($tabs->endTabs());
echo("</div>");
?>
</td>
</tr>
</table>


</div>

<script type="text/javascript">

	/* Since we specified manualStartup=true, tabber will not run after
	   the onload event. Instead let's run it now, to prevent any delay
	   while images load.
	*/
	tabberAutomatic(tabberOptions);

	var cantGrupos = <?php echo($cantGrupos); ?>;
</script>

<?php 
if (strlen($rpalabra) > 2)
{
	?>
	<script type="text/javascript">

	  document.addEventListener("DOMContentLoaded", function() {
				var myHilitor = new Hilitor("");
				myHilitor.setMatchType("left");
	  			myHilitor.apply("<?php echo($rpalabra);?>");
			  }, false);
	  
	</script>
	
	<?php 
}
?>

<?php
include("footer.php");
?>

</body>
</html>
<?php
$fileCache->end($stack->getCount(), $stackname);   
?>