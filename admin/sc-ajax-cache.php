<?php 
require("funcionesSConsola.php");

//TODO: establecer conexión seguro
//checkUsuarioLogueado();

//set-2019: en garage papá

$tabla = RequestSafe("tabla");
$chk = RequestInt("chk", -1);
$hr = date("H:i");
//si viene es en base64
$where = Request("where");
if (!esVacio($where))
	$where = base64_decode($where);

$fecha = date("d/m/Y");

$aResult = array("checksum" => 0,
				"reload" => 1,
				"reload_hr" => $hr,
				"reload_fecha" => $fecha,
				"status" => "",
				"where" => $where,
				"data" => 0);

if (esVacio($tabla))
{
	$aResult["reload"] = -1;
	$aResult["status"] = "ERROR: Falta parametro tabla";
}
else
{    
	//determina si necesita un refresh a la version dada por el usuario ($chk)
	$rs = getRs("select table_checksum, keyfield_, order_by
				from sc_querys 
				where table_ = '$tabla'
				limit 1");

	if ($rs->EOF())                
	{
		$aResult["reload"] = -1;
		$aResult["status"] = "ERROR: No existe la tabla $tabla";
	}
	else
	{
		$keyField = $rs->getValue("keyfield_");
		$orderby = $rs->getValue("order_by");
		if (esVacio($orderby))
			$orderby = $keyField;

		$chk1 = $rs->getValueInt("table_checksum");
		$aResult["checksum"] = $chk1;
		$aResult["orderby"] = $orderby;

		//no hace falta actualizar (chk informado es igual al de la BD)
		if ($chk == $chk1)
		{
			$aResult["reload"] = 0;
			$aResult["status"] = "No requiere reload";
		}
		else
		{
			$aResult["reload"] = 1;
			$aResult["status"] = "Requiere reload";
			$aResult["data"] = sc3ArrayJasonTablaWhere($tabla, $where, $orderby);
		}
	}
}

echo(json_encode($aResult));
?>