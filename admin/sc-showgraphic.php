<?php
include("funcionesSConsola.php"); 
include 'terceros/plchart/v1/class.plchart.php';

$title = Request("title");
$gtype = Request("gtype");
$etiquetas = array();
$data = array(); 

$sql = getSessionStr(Request("sql"));
$rs = new BDObject();	
$rs->execQuery($sql);

$i = 0;
$maximo = -100000;
$minimo =  100000;
$serie = $rs->getFieldName(0);

$longEtiquetas = 6;
if (sonIguales($gtype, "pie_3d"))
{
	$longEtiquetas = 15;
}

while (!$rs->EOF())
{
	$valory = (float) splitValorConMoneda($rs->getValue(1));
	$etiquetas[$i] = substr($rs->getValue(0), 0, $longEtiquetas);
	$data[$i] = round($valory);
	if ($valory > $maximo)
		$maximo = $valory;
	if ($valory < $minimo)
		$minimo = $valory;

	$rs->Next();
	$i++;
}

$demo = new plchart($data, $gtype, 650, 450);
$demo->set_title($title, 12, 0, 100);
if ($minimo > 0)
	$minimo = 0;
	
if (sonIguales($gtype, "pie_3d"))
{
	$demo->set_scale($etiquetas);
	$demo->set_desc(400);
	$demo->set_graph(10, 30, 350, 350, 0.1);
}
else
{
	$demo->set_color('plchart/bg.png', 'line_scatter');
	$escalax = array($minimo, round(($maximo - $minimo)/ 4), round(($maximo - $minimo) / 2), round(($maximo - $minimo) * 3 / 4), round($maximo + 1));
	$demo->set_scale($escalax, $etiquetas);
	$demo->set_desc(220);
	$demo->set_graph(10, 30, 550, 400, 0.1);
}
$demo->output();
?>