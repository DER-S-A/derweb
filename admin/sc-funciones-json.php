<?php

/**
 * Funciones JSON (JASON!) de SC3
 * Fecha: 18-may-2021
 */

/**
 * Retorna un array JSON de una tabla dada
 * @param string $xtabla
 * @param string $xorderby
 * @return string
 */
function sc3ArrayJasonTabla($xtabla, $xorderby = "id", $xpuntoComaFinal = true)
{
	$sql = "select *
			from $xtabla
			order by $xorderby";

	$rsTbl = new BDObject();
	$rsTbl->execQuery($sql, false, true);
	return rsToJsonArray($rsTbl, $xpuntoComaFinal);
}

/**
 * Retorna un array JSON de una tabla dada
 * @param string $xtabla
 * @param string $xorderby
 * @return JSON
 */
function sc3ArrayJasonTablaWhere($xtabla, $xwhere, $xorderby = "", $xconvierteUtf = true)
{
	$aSql = [];
	$aSql[] = "select *
				from $xtabla";

	if (!esVacio($xwhere))
		$aSql[] = "where $xwhere";

	if (!esVacio($xorderby))
		$aSql[] = "order by $xorderby";

	$rsTbl = new BDObject();
	$rsTbl->execQuery(implode(" ", $aSql), false, true);
	return rsToJsonArray($rsTbl, false, $xconvierteUtf);
}


/**
 * Arma un array con el RS pero ademas arma los subquerys y carga los arreglos internos
 * @param BDObject $xrs
 * @param string $xMasterField
 * @param string $xDetailField
 * @param string $xDetailKey
 * @param BDObject $xbd
 * @param string $xsqlDetail
 */
function rsToJsonArray2Levels($xrs, $xMasterField, $xDetailField, $xDetailKey, $xbd, $xsqlDetail)
{
	$aResult = [];
	while (!$xrs->EOF()) {
		$row = $xrs->getRow();
		$row = array_map('utf8_encode', $row);

		$masterValue = $xrs->getValue($xMasterField);
		$sqlD = str_replace("MASTER_ID", $masterValue, $xsqlDetail);

		$xbd->execQuery($sqlD, false, true);
		$rowsDetail = $xbd->getAsArray("");
		$row[$xDetailKey] = $rowsDetail;

		$aResult[] = json_encode($row);
		$xrs->Next();
	}
	return "[" . implode(",\r\n\t", $aResult) . "];";
}

/**
 * json_decode() pero resuelve e error JSON_ERROR_UTF8 cuando vienen sin UTF
 */
function safe_json_decode($value)
{
	$encoded = json_decode($value, true, 512);
	if ($encoded === NULL && json_last_error() == JSON_ERROR_UTF8) {
		$encoded = json_decode(utf8ize($value), true, 512);
	}
	return $encoded;
}

/**
 * Crea un array json con el RS dado
 * @param BDObject $xrs
 * @return string
 */
function rsToJsonArray($xrs, $xpuntoComaFinal = true, $xconvierteUtf = true)
{
	$aResult = [];
	while (!$xrs->EOF()) {
		$row = $xrs->getRow();
		if ($xconvierteUtf) {
			$row = array_map('utf8_encode', $row);
		}
		$aResult[] = json_encode($row);
		$xrs->Next();
	}

	$puntoComa = ";";
	if (!$xpuntoComaFinal)
		$puntoComa = "";

	return "[" . implode(",\r\n\t", $aResult) . "]$puntoComa";
}

/**
 * Crea un array json indexado por el ID
 * @param BDObject $xrs
 * @return string
 */
function rsToJsonArrayId($xrs, $xpuntoComaFinal = true)
{
	$aResult = [];
	while (!$xrs->EOF()) {
		$row = $xrs->getRow();
		//$row = array_map('utf8_encode', $row);
		$row = sinCaracteresEspecialesArray($row);

		$aResult[$xrs->getId()] = json_encode($row);
		$xrs->Next();
	}
	$puntoComa = ";";
	if (!$xpuntoComaFinal)
		$puntoComa = "";
	return "[" . implode(",\r\n\t", $aResult) . "]$puntoComa";
}
