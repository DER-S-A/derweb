<?php
include("sc-api.php");

/**
 * Recupera los datos de la empresa a partir de un código
 * $aParams puede tener 'codigo' o 'cuit'
 */
function getEmpresa($aParams)
{
	//recupera por código
	if (isset($aParams["codigo"])) {
		$codigo = $aParams["codigo"];
		$sql = "SELECT * 
				FROM b2b_empresas 
				WHERE codigo = '$codigo'";
	}

	if (isset($aParams["cuit"])) {
		$cuit = $aParams["cuit"];
		$sql = "SELECT * 
				FROM b2b_empresas 
				WHERE cuit = '$cuit'";
	}

	$aResult = getAjaxResponseArray("getEmpresa", 1);

	$bd = new BDObject();
	$bd->execQuery($sql, false, true);
	$aData = $bd->getAsArray();
	$bd->close();

	$aResult["data"] = json_encode($aData);
	return $aResult;
}


/**
 * Avisa (no traiciona) que esta empresa provee CIF
 */
function empresaHabilitarCif($aParams)
{
	$cuit = $aParams["cuit"];
	$nombre = $aParams["nombre"];
	$url = $aParams["url"];

	$aResult = getAjaxResponseArray("empresaHabilitarCif", 1);
	$aResult["cuit"] = $cuit;

	//analiza si inserta empresa
	$rsEmpresa = locateRecordWhere("b2b_empresas", "cuit = '$cuit'");
	if ($rsEmpresa->EOF()) {

		//estás desarrollando ?
		if (!strContiene($url, "localhost")) {

			$aValues = [];
			$aValues['nombre'] = $nombre;
			$aValues['cuit'] = $cuit;
			$aValues['url'] = $url;
			$aValues['provee_cif'] = 1;

			$sql = insertIntoTable2("b2b_empresas", $aValues);
			$rsEmpresa->execQuery($sql);
		}
	} else {

		$rsEmpresa->execQuery("update b2b_empresas 
								set provee_cif = 1
								WHERE cuit = '$cuit'");
	}

	$rsEmpresa->close();

	$aResult["msg"] = "Empresa $cuit actualizada";
	return $aResult;
}
