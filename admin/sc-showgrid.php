<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = Request("query");

//Definicion de variables globales a la pagina
$NroPag = 0;
$FromRecNro = 0;
$ToRecNro = 0;

if (strcmp($rquery,"") == 0)
	echo("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=queryname</b>");
$rtop = 1000;

//Manejo de details
$rmquery = Request("mquery");
$rmid = Request("mid");
$mfield = Request("mfield");

//echo("mquery: $rmquery, mid: $rmid, mfield: $mfield");

$query_info = Array();
$fk_cache = Array();

//......recupe
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

$query_info = 0;

if (isDetail())
{
	$qminfo = getQueryObj($rmquery);
	$codigo = "" . translateCode($rmid, $rmquery);
	if ($mfield == "")
		$mfield = getFkField($qinfo->getQueryId(), $rmquery);
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
	
	$url = new HtmlUrl("");
	$url->add("query", $rquery);
	$url->add("mquery", $rmquery);
	$url->add("mid", $rmid);
	$url->add("mfield", $mfield);
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

function linkVer($xregistrovalor, $xflat = false)
{
	global $qviewpage;
	$strResult = "<a href=\"";
	$strResult .= urlVer($xregistrovalor);
	$strResult .= "\">";
	$strResult .= img("./images/view2.gif", "Ver y operar");	
	if (!$xflat)
		$strResult .= "<br />";	
	else
		$strResult .= " ";	
	$strResult .= "Ver</a>";	
	return $strResult;
}

function linkInsertar()
{
	global $rquery;
	global $rorderby;
	global $NroPag;
	
	$strResult = "<a accesskey=\"n\" title=\"Insertar nuevo dato [ALT + SHIFT + n]\" href=\"";
	$strResult .= "sc-edititem.php";
	$strResult .= getParams() . "&insert=1\">";
	$strResult .= "<i class=\"fa fa-plus-circle fa-2x fa-fw boton-fa\"></i>
					</a>";
	return $strResult;
}

/**
 * Arma un link para editar y si tiene ID usa el F2
 * @param string $xregistrovalor
 * @param string $xid
 * @return string
 */
function linkEditar($xregistrovalor, $xid = "")
{
	$str = "<a class=\"w3-button w3-text-purple boton-fa\" href=\"";
	$str .= "sc-edititem.php";
	$str .= getParams($xregistrovalor) . "&registrovalor=" . $xregistrovalor . "\" id=\"$xid\">";
	$titulo = "Editar";
	if (!sonIguales($xid, ""))
		$titulo .= " [F2]";
	$str .= "<i class=\"fa fa-pencil-square-o fa-fw fa-2x\" title=\"Editar\"></i>";
	$str .= "</a>";
	return $str;
}


function linkBorrar($xregistrovalor, $xflat = false)
{
	$str = "<a class=\"w3-button w3-text-purple boton-fa\" href=\"javascript:dardebaja(" . $xregistrovalor . ")\">
				<i class=\"fa fa-trash-o fa-fw fa-2x\" title=\"Borrar\"></i>
			</a>";
	return $str;
}

function getDataSorter($xtipoCampo)
{
	$result = " table-sortable:";
	if (esCampoInt($xtipoCampo))
		$result .= "numeric";
	else
	if (esCampoFloat($xtipoCampo))
		$result = ""; //currency"; //TODO: no ordena negativos !
	else	
	if (esCampoFecha($xtipoCampo))
		$result .= "date"; 
 	else	
		$result .= "ignorecase";

	return $result;		
}


$savedSql = RequestSafe("sql");
if (!sonIguales($savedSql, ""))
	$sql = getSession($savedSql);
else
	//arma el  con el sql de la consulta
	$sql = $qinfo->getQuerySql2("", "", "", "", "", $rmquery, $rmid, $mfield, "");

$rsPpal = new BDObject();
$rsPpal->execQuery($sql);
$cantRecs = $rsPpal->cant();

?>
<!doctype html>
<html lang="es">
<head>

<title>sc3 - <?php echo($qinfo->getQueryDescription()); ?></title>

<?php include("include-head.php"); ?>

<script language="javascript">
	function dardebaja(xrecid)
	{
		if (confirm("Esta seguro que desea borrar este registro ?"))
			document.location.href = "sc-delitem.php<?php echo(getParams()) ?>" + "&registrovalor=" + xrecid
	}
	
	function buscar()
	{
		f=document.getElementById('form1');
		palabra = document.getElementById('palabra');
		f.submit();
	}

	function ver(xurl)
	{
		document.location.href = xurl;
	}
	
</script>

<style type="text/css">

.celda_insertar
{
	background-color:#ffffff!important;
	text-align: center!important;
	padding: 4px!important;
}

.celda_herramienta
{
	width: 40px;
	padding: 4px!important;
}

</style>

</head>
<body>

<div class="w3-container headerSubtitulo w3-center">
	<?php 
		$icon = $qinfo->getQueryIcon();
		if ($icon == "")
		$icon = "images/question.gif";
		echo(img($icon, ""));
		echo(" " . htmlVisible($qinfo->getQueryDescription()));
	?>
</div>


      <?php 
		echo("<table class=\"data_table table-autosort sort02 w3-table-all w3-hoverable w3-small\">\n");
		echo("<thead>");
		echo("<tr class=\"grid_header\">");
		$i=1;
		$rowFilters = "<tr>";
		
		while ($i < $rsPpal->cantF())
		{
			$fieldname = $rsPpal->getFieldName($i);
			$pos = strpos($fieldname, "_fk");
			$tipoCampo = $rsPpal->getFieldType($i);
			if (!sonIguales($mfield, $fieldname) && ($pos === FALSE))
			{
				echo("<th class=\"grid_header2 ");
				echo(getDataSorter($tipoCampo));
				echo("\"><b>");
				echo($qinfo->getFieldCaption($fieldname));
				echo("</b></th>");
				
				$rowFilters .= "<th class=\"grid_filter\">";
				$rowFilters .= "<input name=\"filter\" size=\"5\" onkeyup=\"Table.filter(this,this)\" title=\"Ingrese un valor a filtrar\">";
				$rowFilters .= "</th>";
			}
			$i++;
		}
		
		echo("<th colspan=\"3\" class=\"celda_insertar\">");
		if ($qinfo->canInsert() || esRoot())
		 	echo(linkInsertar());
		else
			echo(espacio());
		echo("</th></tr>");
		
		//arma los filtros
		if ($cantRecs > 9)
		{
			$rowFilters .= "<th class=\"grid_filter\" colspan=\"3\">";
			$rowFilters .= espacio();
			$rowFilters .= "</th>";
			$rowFilters .= "</tr>";
			echo($rowFilters);
		}
		echo("</thead>");

echo("<tbody>");

$cantAux = 0;

if ($rsPpal->EOF())
{ 
	echo("<tr>");
	$cols = $rsPpal->cantF() + 1;
	echo("<td colspan=\"$cols\"><i>(sin datos)</i>" . espacio());
	echo("</td>");
	echo("</tr>");
}

//recupera el ultimo id visto
$lastId = (int) getSession("sc3-last-$rquery");
$anchoMax = getParameterInt("sc3-grid-trunca-string", 80);

$cantFilas = 0;

//l�mite de 500, mas no se pueden mostrar aca, pasarlo a in_master = 0
while (!$rsPpal->EOF() && ($cantFilas <= 500))
{ 
	$qinfo->addCursor($rsPpal->getValue($qinfo->getKeyField())); 
	$keyvalue = $rsPpal->getValue($qinfo->getKeyField());
	$record = $rsPpal->getRow();

	$backColor = "";
	if ($lastId == $keyvalue)
		$backColor = "#aaccdd";
	
	echo("\n<tr class=\"\">");
	$i=1;
	while ($i < $rsPpal->cantF())
	{				
		$nombreCampo = $rsPpal->getFieldName($i);
		$record = $rsPpal->getRow();
		$pos = strpos($nombreCampo, "_fk");
		if (!sonIguales($mfield, $nombreCampo) && ($pos === FALSE))
		{
		    $claseAlign = "";
			$tipoCampo = $rsPpal->getFieldType($i);
			$dataAlign = getDataAlign($nombreCampo, $tipoCampo, $qinfo->getFieldsRef());
			if (sonIguales($dataAlign, "right"))
			    $claseAlign = "align-right";
			
			echo("<td valign=\"top\" align=\"" . $dataAlign . "\" class=\"" . $qinfo->getFieldClass($nombreCampo, $record) . " $claseAlign\">");
			$valorCampo = ($rsPpal->getValue($i));
			
			if ((strcmp($valorCampo, "") == 0) || (strcmp($valorCampo, "null") == 0))
				echo(espacio());
			else
			{
				if (strcmp($nombreCampo, "clave") == 0)
					echo("**********");
				else
				if (strpos($nombreCampo, "email") !== FALSE)
				{
					$link = "mailto:" . $valorCampo;
					echo(href($valorCampo, $link));
				}
				else
				if (strpos($nombreCampo, "web") !== FALSE)
					echo(href($valorCampo, $valorCampo, "_blanck"));
				else
				if ($qinfo->isFileField($nombreCampo))
				{
				    $imageSize = 200;
				    $file = new HtmlInputFile($nombreCampo . $cantFilas, $valorCampo);
    				$file->setWidthPreview($imageSize);
    				$file->setReadOnly(true);
    				echo($file->toHtml());				
				}
				else
				if ($qinfo->isColorField($nombreCampo))
					echo("<table width=55 height=20 title=" . $valorCampo . "><tr><td bgcolor=" . $valorCampo . ">&nbsp;</td></tr></table>");
				else
				if(esCampoStr($tipoCampo))			
				{
					if (strlen($valorCampo) > $anchoMax)
					{
						$valorCampo = "<div title=\"$valorCampo\">" . substr($valorCampo, 0, $anchoMax) . "...</div>"; 
					}
					echo($valorCampo);
				}
				else
				if(esCampoMemo($tipoCampo))			
				{
					if (strlen($valorCampo) > $anchoMax)
					{
						$valorCampo = "<div title=\"$valorCampo\">" . substr($valorCampo, 0, $anchoMax) . "...</div>"; 
					}
					echo($valorCampo);
				}
				else
				if (esCampoFecha($tipoCampo))//tipo fecha
				{
					$Day = getdate(toTimestamp($valorCampo));
					echo( Sc3FechaUtils::formatFecha($Day, true, true));
				}
				else
				if (esCampoInt($tipoCampo)) //tipo INT, prueba si es una FK
				{
					echo(getFKValue2($nombreCampo, $valorCampo, $qinfo->getFieldsRef(), $fk_cache, false, $record));
				}
				else
				if (esCampoBoleano($tipoCampo)) //tipo booleano
				{
					if ($rsPpal->getValue($i) == 1) 
						echo("<div class=\"booleano si\">Si</div>");
					elseif ($rsPpal->getValue($i) == 0)
						echo("<div class=\"booleano no\">No</div>");
				}
				else
				if (esCampoFloat($tipoCampo))
				{
					echo(formatFloat($valorCampo));
				}
				else
				if (strcmp($nombreCampo,"clave") == 0)
					echo("********");
				else			
					echo($valorCampo);
			}	
			echo("</td>");
		}
		$i++;
	}

	$colspan=1;
	
	echo("<td class=\"celda_herramienta centrada\">");
	
	if (!$qinfo->isDebil())
	{
		$str = "<a href=\"" . urlVer($keyvalue, true) . "\" target=\"contenido\">";
		$str .= img("images/operaciones.png", "Ver / Herramientas") . "</a>";	
		echo($str);
	}
	
	echo("</td><td class=\"celda_herramienta centrada\">");
	if ($qinfo->canEdit() || esRoot())
	{
		echo(linkEditar($keyvalue));
	}
	
	echo("</td><td class=\"celda_herramienta centrada\">");
	if ($qinfo->canDelete() || esRoot())
	{
		echo(linkBorrar($keyvalue));
	}
	echo("</td></tr>");
	
	$rsPpal->Next();
	$cantRecs++;
	$cantAux++;
	$cantFilas++;
}
echo("</tbody>");

//footer de la grilla
$i = 1;
echo("<tfoot><tr class=grid_header>");
while ($i < $rsPpal->cantF())
{
	$fieldname = $rsPpal->getFieldName($i);
	$pos = strpos($fieldname, "_fk");
	if (!sonIguales($mfield, $fieldname) && ($pos === FALSE))
	{
		echo("<td>" . espacio() . "</td>");
	}
	$i++;
}
echo("<td colspan=\"3\">" . espacio() . "</td>");
echo("</tr></tfoot>");

echo("</table>\n");

if ($cantFilas == 500)
{
	echo("Demasiados datos (consulte al administrador)...");
}

?>
</body>
</html>