<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

$hayQueryInfo = false;

$title = Request("title");
$filtername = Request("filtername");
$codigo = "";
$mfield = "";
$query = Request("query");
if (!sonIguales($query, ""))
{
	$hayQueryInfo = true;
	$qinfo = getQueryObj($query);
	$title = $qinfo->getQueryDescription();
	if (!sonIguales($filtername, ""))
		$title .= " (" . $filtername . ")";
	
	$mid = RequestInt("mid");
	$mquery = Request("mquery");
	if ($mid > 0)
	{
		$qminfo = getQueryObj($mquery);
		$codigo = $qminfo->getQueryDescription() . ": " . translateCode($mid, $mquery);
		if ($mfield == "")
			$mfield = getFkField($qinfo->getQueryId(), $mquery);
	}		
}

function puntos_cm ($medida, $resolucion=72)
{
   return ($medida/(2.54))*$resolucion;
}


//$pdf = & new Cezpdf("a4", "landscape");
$pdf = new Cezpdf("a4", "landscape");
$font = getParameter("sc3-font-pdf", "Helvetica.afm");
$pdf->selectFont('./pdf/fonts/' . $font);

$pdf->ezSetCmMargins(1, 1, 2, 1.5);

$datacreator = array (
                    'Title'=>$title,
                    'Author'=>$SITIO,
                    'Subject'=>$title,
                    'Creator'=>'info@sc3.com.ar',
                    'Producer'=>'http://www.sc3.com.ar/'
                    );

$pdf->addInfo($datacreator);

$sql = getSessionStr(Request("_SQL"));
$bd = new BDObject();
$bd->execQuery($sql);
$totEmp = $bd->cant();
$ixx = 0;

//arma titulos
$col = 0;
$titles = array();
$colOptions = array();

while($col < $bd->cantF())
{
	$colName = $bd->getFieldName($col);
	$colOptions[$colName] = array("justification"=>"left");

	if ($qinfo->isFileField($colName))
		$colOptions[$colName] = array("justification"=>"left", "width"=>180);
		
	if ($hayQueryInfo)
		$titles[$colName] = "<b>" . $qinfo->getFieldCaption($colName) . "</b>";
	else
		$titles[$colName] = "<b>" .  str_replace("_", " ", $colName) . "</b>";
	$col++;
}

//elimina las clumnas con "_fk"
$col = 0;
foreach ($titles as $colName => $colTitle)
{
	if (strpos($colName, "_fk") > 0)
		unset($titles[$colName]);
	if (sonIguales($colName, $mfield))	
		unset($titles[$colName]);
}

$i = 0;
while(!$bd->EOF())
{
	$datatmp = $bd->getRow();

	//formatea datos
	$col = 0;
	while($col < $bd->cantF())
	{
		$tipoCampo = $bd->getFieldType($col);
		$colName = $bd->getFieldName($col);
		$valorCampo = $bd->getValue($colName);
		
		if (!sonIguales($valorCampo, ""))
		{
			if (esCampoFecha($tipoCampo))//tipo fecha
			{
				$colOptions[$colName] = array("justification"=>"right");
				$Day = getdate(toTimestamp($valorCampo));
				$valorCampo = Sc3FechaUtils::formatFecha2($Day, false);
			}
			else
			if (esCampoInt($tipoCampo)) //tipo INT, prueba si es una FK
			{
				$valorCampo = getFKValue2($colName, $valorCampo, $qinfo->getFieldsRef(), $fk_cache, false, $datatmp);
				if (is_numeric($valorCampo))
					$colOptions[$colName] = array("justification"=>"right");
			}
			else
			if (esCampoBoleano($tipoCampo)) //tipo booleano
			{
				if ($valorCampo == 1) 
					$valorCampo = "Si";
				else
					$valorCampo = "No";
			}
			else
			if (esCampoFloat($tipoCampo))
			{
				$colOptions[$colName] = array('justification'=>'right');
				$valorCampo = formatFloat($valorCampo);
			}
			else 
			if ($qinfo->isFileField($colName))
				$valorCampo = "::IMG::" . getImagesPath() . $valorCampo;
				
		}
		$datatmp[$colName] = $valorCampo;
		$col++;
	}
	
	//elimino las claves numericas del row
	$data[$i] = array_intersect_key($datatmp, $titles);
	$bd->Next();
	$i++;
}

$fontSize = getParameterInt("pdf-font-size", 8);

/*
 $pdf->ezText("\nHeader shading <b>since 0.12-rc9</b>\n");
 $pdf->ezTable($data,$cols,'',array('shadeHeadingCol'=>array(0.4,0.6,0.6),'width'=>400));
 
$pdf->ezTable($data, $cols,'', array('showHeadings'=>1,
									'shaded'=>1,
									'gridlines'=>$i,
									'cols'=>$coloptions, 
									'innerLineThickness' => 0.5,
									'outerLineThickness' =>3));

 */


// Light Steel Blue	176-196-222	b0c4de
$r = getParameter("sc3-pdf-colorgrid-r", 176);
$g = getParameter("sc3-pdf-colorgrid-g", 196);
$b = getParameter("sc3-pdf-colorgrid-b", 222);

$options = array(
				'shadeCol' => array(0.9, 0.9, 0.9),
				'lineCol' => array(176/255, 196/255, 222/255),
				'xOrientation' => 'center', 
				'cols'=>$colOptions,
				'fontSize' => $fontSize,
				'width' => 750,

				//alineacion del header, independientemente de cada columna
				'alignHeadings' => 'center',
				//20 es un bitmask para mostrar las lineas de header y footer, ver tables.php
				'gridlines' => 20,

				'shadeHeadingCol' => array($r/255, $g/255, $b/255)
				);
				
$rsempresa = getRsEmpresa();
$empresa = $rsempresa->getValue("nombre");
if (!sonIguales($rsempresa->getValue("direccion"), ""))
	$empresa .= " - " . pdfVisible($rsempresa->getValue("direccion"));
if (!sonIguales($rsempresa->getValue("telefono"), ""))
	$empresa .= " - Tel: " . $rsempresa->getValue("telefono");
if (!sonIguales($rsempresa->getValue("email"), ""))
        $empresa .= " - " . $rsempresa->getValue("email");
if (!sonIguales($rsempresa->getValue("web"), ""))
        $empresa .= " - " . $rsempresa->getValue("web");

$pdf->addJpegFromFile("./app/logo.jpg", puntos_cm(1.5), puntos_cm(18.5), 150, 55);
$txttit = "<b>" . $title . "</b>\n";
$pdf->addText(puntos_cm(13), puntos_cm(19), 16, $txttit);
$pdf->addText(puntos_cm(1.9), puntos_cm(18.2), 7, $empresa);
$pdf->line(puntos_cm(1.9), puntos_cm(18), puntos_cm(28), puntos_cm(18));
$pdf->ezSetY(puntos_cm(18));
                      
$pdf->ezText("$codigo\n", 10);

$pdf->ezTable($data, $titles, "", $options);

$pdf->ezText("\n\n\n", 10);
$pdf->ezText($SITIO, 9);
$optionsLine = array('justification' => 'right');
$pdf->ezText("<b>Fecha:</b> " . date("d/m/Y") . " " . date("H:i:s")."\n\n", 9, $optionsLine);

//ver pdf/examples/tables.php

$pdf->ezStream();
?>