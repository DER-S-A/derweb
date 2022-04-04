<?php

function getEmpresa($aParams)
{   
	$codigo = $aParams["codigo"];
	$aResult = getAjaxResponseArray("getEmpresa", 1);
	$sql = "SELECT * 
			FROM b2b_empresas 
				WHERE codigo = '$codigo'";

	$bd = new BDObject();
	$bd->execQuery($sql, false, true);
	$data = array();
	while (!$bd->EOF()) {
		$row = $bd->getRow();
		$data[] = $row;
		$bd->next();
	}
	$aResult["data"] = json_encode($data);
	return $aResult;
}
