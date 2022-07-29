<?php 
require("funcionesSConsola.php");

checkUsuarioLogueado();

$rquery = (Request("query"));
$rsql = getSessionStr(Request("sql"));
$mid = Request("mid");

if (strcmp($rquery,"") == 0)
	echo("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=queryname</b>");

$query_info = Array();
$fk_cache = Array();

$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);
$query_info = 0;

//inicia PDF
$pdf = new HtmlPdf($qinfo->getQueryDescription());

//agrega el registro con sus detalles al PDF
$pdf = sc3PdfRowToPDF($pdf, $qinfo, $rsql, $mid, array(), array());

$pdf->getPdfObj()->ezStream();
?>