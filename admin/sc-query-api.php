<?php
include_once("sc-updversion-utils.php");

/**
 * Define el grupo de un campo. Llamado desde la definicion con Drag&Drop
 * Limpia cache
 */
function cambiarGrupoCampo($aParams)
{
	$idquery = $aParams['idquery'];
	$campo = $aParams['campo'];
	$grupo = $aParams['grupo'];

	if ($grupo == "Datos")
		$grupo = "";

	$aResult = getAjaxResponseArray("cambiarGrupoCampo", 1, $aParams);

	$bd = new BDObject();
	$SQL = "UPDATE sc_fields
			SET grupo = '$grupo'
			WHERE idquery = $idquery AND
				field_ = '$campo'";
	$bd->execQuery($SQL);
	$aResult["cant"] = $bd->cant();
	$aResult["params"] = $aParams;
	$aResult["grupo"] = $grupo;

	$bd->close();

	//limpia cache para ver los campos actualizados
	$tc = getCache();
	$tc->flushCache();
	saveCache($tc);

	return $aResult;
}

function getFieldInfo($aParams)
{
	$campo = $aParams['campo'];
	$idquery = $aParams['idquery'];

	$aResult = getAjaxResponseArray("getFieldInfo", 1, $aParams);

	$SQL = "SELECT * 
			FROM sc_fields 
			WHERE idquery = $idquery AND
				field_ = '$campo'";
	$bd = new BDObject();
	$bd->execQuery($SQL);
	$aResult["data"] = $bd->getRow();
	$aResult["idquery"] = $idquery;
	$aResult["campo"] = $campo;
	return $aResult;
}

function generateInfoCampo($aParams)
{
	$idquery = $aParams["idquery"];
	$aResult = getAjaxResponseArray("generateInfoCampo", 1, $aParams);
	$bd = new BDObject();
	$SQl = "SELECT table_ 
			FROM sc_querys
			WHERE id = $idquery";
	$bd->execQuery($SQl);
	$tabla = $bd->getValue("table_");
	sc3generateFieldsInfo($tabla);

	//obtengo la info del campo para rellenar el div.
	$fieldInfo = getFieldInfo($aParams);
	$aResult["data"] = $fieldInfo["data"];

	$tc = getCache();
	$tc->flushCache();
	saveCache($tc);

	return $aResult;
}

//prefix, anchom ecriptado se van
function actualizarCampo($aParams)
{
	$aResult = getAjaxResponseArray("actualizarCampo", 1, $aParams);
	$idQuery = $aParams['idquery'];
	$campo = $aParams['campo'];
	$values = [
		"is_required" => $aParams['is_required'],
		"password_field" => $aParams['password_field'],
		"file_field" => $aParams['file_field'],
		"color_field" => $aParams['color_field'],
		"rich_text" => $aParams['rich_text'],
		"is_google_point" => $aParams['is_google_point'],
		"is_editable" => $aParams['is_editable'],
		"ocultar_vacio" => $aParams['ocultarVacio'],
		"visible" => $aParams['visible'],
		"show_name" =>  comillasSql($aParams['show_name']),
		"subgrupo" =>  comillasSql($aParams['subgrupo']),
		"default_value_exp" => comillasSql($aParams['default_value_exp']),
		"example" => comillasSql($aParams['example']),
		"class" => comillasSql($aParams['class']),
		"field_help" => comillasSql($aParams['field_help']),
	];

	$sql = updateTable("sc_fields", $values, "idquery = $idQuery AND field_ = '" . $campo . "'");
	$bd = new BDObject();
	$bd->execQuery($sql);
	$aResult["msg"] = "Campo actualizado";

	$tc = getCache();
	$tc->flushCache();
	saveCache($tc);

	return $aResult;
}
