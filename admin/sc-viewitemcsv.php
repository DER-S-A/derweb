<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = (Request("query"));
$rsql = getSessionStr(Request("sql"));
if (strcmp($rquery,"") == 0)
	echo("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=queryname</b>");

$query_info = Array();
$fk_cache = Array();

$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);
$query_info = 0;

$grupos = getGruposArray($qinfo->getFieldsDef());

//arma el  con el sql de la consulta
$rsPpal = new BDObject();
$rsPpal->execQuery($rsql);
$columns = $rsPpal->cantF();
$record = $rsPpal->getRow();

$data = array();

//recorre los grupos de datos
foreach ($grupos as $grupoActual)
{
	$data[] = array("", $grupoActual);
	
	$i=0;
	while ($i < $rsPpal->cantF())
	{
		$nombreCampo = $rsPpal->getFieldName($i);
		$pos = strpos($nombreCampo, "_fk");
		if ($pos === FALSE)
		{
			$tipoCampo = $rsPpal->getFieldType($i);

			$campo = $qinfo->getFieldCaption($nombreCampo);
			$fieldGroup = $qinfo->getFieldGrupo($nombreCampo);
			
			if (sonIguales($grupoActual, $fieldGroup)
					|| (sonIguales("", $fieldGroup) && sonIguales("Datos", $grupoActual)))
			{

				$valorsel = $rsPpal->getValue($i);
				
				if (!esVacio($valorsel))
				{
					if (esCampoStr($tipoCampo)) //Tipo texto
					{
						if (strcmp($nombreCampo, "clave") == 0)
							$valorsel = "*******";
					}
					else
					if (esCampoBoleano($tipoCampo)) //tipo booleano
					{
						if ($valorsel == 1) 
							$valorsel = "Si";
						else
							$valorsel = "No";
					}
					else		
					if (esCampoInt($tipoCampo)) //tipo integer (intentar� buscar info de FK)
					{
						$valorsel = getFKValue2($nombreCampo, $valorsel, $qinfo->mfields_ref, $fk_cache, false, $record);
					}
					else
					if (esCampoFecha($tipoCampo)) //tipo fecha
					{
						$fechaReg = getdate(toTimestamp($rsPpal->getValue($i)));
						$valorsel = Sc3FechaUtils::formatFecha2($fechaReg);
					}
					else	
					if (esCampoFloat($tipoCampo))
					{
						$valorsel = formatFloat($valorsel);
					}
				}		
				$data[] = array($campo, $valorsel);	
			}
		}			
		$i++;
	}
}

$fileName = sc3CsvFilename($qinfo->getQueryDescription() . "-" . $rsPpal->getValue($qinfo->getKeyField()), false);
sc3CsvSaveArray($fileName, "", $data);
goToPage("./tmp/" . basename($fileName));

?>