<?php

/**
 * Retorna el filtro asociado a este query y campo
 */
function sc3FiltroAsociado($xaParams)
{
	$query = $xaParams["query"];
	$campo = $xaParams["campo"];
	$indice = $xaParams["indice"];

	$campoControl = "busqvalor_$indice";

	$aResult = getAjaxResponseArray("sc3FiltroAsociado");
	if (esVacio($query) || esVacio($campo))
	{
		$aResult["error"] = "Query o campo vacio";
		return $aResult;
	}

	//vuelven todos!
	$aResult["indice"] = $indice;
	$aResult["campo"] = $campo;

	$query_info = Array();
	$tc = getCache();
	$query_info = $tc->getQueryInfo($query);
	saveCache($tc);

	$qinfo = new ScQueryInfo($query_info);

	//$fieldsDefs = $qinfo->getFieldsDef();
	$fieldsRefs = $qinfo->getFieldsRef();

	//sólo para recuperar meta info, tipos de campos, busca con id 0
	$rsMeta = locateRecordId($qinfo->getQueryTable(), 0, $qinfo->getKeyField());
	$type = $rsMeta->getFieldTypeByName($campo);

	$cboCondicion = new HtmlCombo("busqcondicion_$indice", "");

	$aResult["nombrecontrol"] = $campoControl;

	if (esCampoFecha($type))
	{
		$cboCondicion->add("FECHA_IGUAL", "igual a");
		$cboCondicion->add("FECHA_DISTINTO", "distinto a");
		$cboCondicion->add("NULL", "es vacio");
		$cboCondicion->add("NOT_NULL", "no es vacio");
		$cboCondicion->add("FECHA_MAYOR", "mayor a");
		$cboCondicion->add("FECHA_MENOR", "menor a");

		$cfecha = new HtmlDate($campoControl, "");
		$cfecha->setOnlyDate();
		$aResult["control"] = $cfecha->toHtml();
	}

	if (esCampoStr($type) || esCampoMemo($type))
	{
		$cboCondicion->add("STR_IGUAL", "igual a");
		$cboCondicion->add("STR_DISTINTO", "distinto a");
		$cboCondicion->add("STR_CONTIENE", "contiene");
		$cboCondicion->add("STR_VACIO", "es vacio");
		$cboCondicion->add("STR_NO_VACIO", "no es vacio");
		$cboCondicion->add("STR_MAYOR", "mayor a");
		$cboCondicion->add("STR_MENOR", "menor a");

		$txt1 = new HtmlInputText($campoControl, "");
		$txt1->setSize(10);
		$aResult["control"] = $txt1->toHtml();
	}

	if (esCampoBoleano($type))
	{
		$cboCondicion->add("IGUAL", "igual a");

		$bol = new HtmlBoolean2($campoControl, 0);
		$aResult["control"] = $bol->toHtml();
	}

	if (esCampoInt($type)) 
	{
		//tipo integer (intentara armar combo con info de FK)
		if (!isset($fieldsRefs[$campo]) || ($fieldsRefs[$campo] == ""))
		{
			$cboCondicion->add("IGUAL", "igual a");
			$cboCondicion->add("DISTINTO", "distinto a");
			$cboCondicion->add("NULL", "es vacio");
			$cboCondicion->add("NOT_NULL", "no es vacio");
			$cboCondicion->add("MAYOR", "mayor a");
			$cboCondicion->add("MENOR", "menor a");

			$inp = new HtmlInputText($campoControl, "");
			$inp->setTypeInt();
			$aResult["control"] = $inp->toHtml();
		}
		else
		{
			$cboCondicion->add("IGUAL", "igual a");
			$cboCondicion->add("DISTINTO", "distinto de");
			$cboCondicion->add("NULL", "es vacio");
			$cboCondicion->add("NOT_NULL", "no es vacio");
	
			$sel = new HtmlSelector($campoControl, $fieldsRefs[$campo]["queryname"], "");
			$sel->setIgnoreFixedValue();
			$aResult["control"] = $sel->toHtml();
		}
	}

	if (esCampoFloat($type))
	{
		$cboCondicion->add("IGUAL", "igual a");
		$cboCondicion->add("DISTINTO", "distinto a");
		$cboCondicion->add("NULL", "es vacio");
		$cboCondicion->add("NOT_NULL", "no es vacio");
		$cboCondicion->add("MAYOR", "mayor a");
		$cboCondicion->add("MENOR", "menor a");

		$txtF = new HtmlInputText($campoControl, "");
		$txtF->setTypeFloat();
		$aResult["control"] = $txtF->toHtml();
	}

	//busca script y envía aparte para que sea evaluado
	$script = "";
	$textoControl = $aResult["control"];
	$aScript = explode("<script>", $textoControl);
	if (count($aScript) > 1)
	{
		$script = str_replace("</script>", "", $aScript[1]);
	}

	$aResult["jscript"] = $script;
	$aResult["condicion"] = $cboCondicion->toHtml();
	return $aResult;
}