<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = (Request("query"));
$mid = RequestInt("mid");
$rsql = getSessionStr(Request("_SQL"));
if (strcmp($rquery,"") == 0)
	echo("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=queryname</b>");

$query_info = Array();
$fk_cache = Array();

$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);
$query_info = 0;

//arma el  con el sql de la consulta
$rsPpal = new BDObject();
$rsPpal->execQuery($rsql, false, true);
$columns = $rsPpal->cantF();
$cant = $rsPpal->cant();
$fileName = sc3CsvFilename($qinfo->getQueryDescription() . $mid, false);

//Arma encabezado
$i = 0;
$encabezados = array();
while ($i < $columns)
{
	$nombreCampo = $rsPpal->getFieldName($i);
	if (!strContiene($nombreCampo, "_fk"))
		$encabezados[] = $qinfo->getFieldCaption(str_replace("_fk", "", $nombreCampo));
	$i++;
}

//Arma datos
$data = array();
while (!$rsPpal->EOF())
{
	$i = 0;
	$row = array();
	while ($i < $columns)
	{
		$nombreCampo = $rsPpal->getFieldName($i);
		$valorCampo = $rsPpal->getValue($nombreCampo);
		$tipoCampo = $rsPpal->getFieldType($i);
		$record = $rsPpal->getRow();

		if (!strContiene($nombreCampo, "_fk"))
		{
			if (esCampoInt($tipoCampo))
			{
				$row[] = getFKValue2($nombreCampo, $valorCampo, $qinfo->getFieldsRef(), $fk_cache, false, $record);
			}
			else
				$row[] = $valorCampo;
		}
		$i++;
	}
	
	$data[] = $row;
	$rsPpal->Next();
}

sc3CsvSaveArray($fileName, $encabezados, $data);

$filecsv = strtolower(sinCaracteresEspecialesNiEspacios($qinfo->getQueryDescription())) . "-" . date("Ymd-hi") . ".csv";
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="' . $filecsv . '";');

$file = file_get_contents($fileName);
echo($file);
?>