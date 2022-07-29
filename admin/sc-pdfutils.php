<?php


/**
 * Agrega un registro al PDF dado
 * @param HtmlPdf $pdf
 * @param ScQueryInfo $qinfo
 * @param string $rsql
 * @param int $mid
 * @param string $xaDetalles
 */

function sc3PdfRowToPDF($pdf, $qinfo, $rsql, $mid, $xaSkipFields, $xaSkipDetails, $xConDetalles = true)
{
	$fk_cache = [];

	$grupos = getGruposArray($qinfo->getFieldsDef());

	//arma el  con el sql de la consulta
	$rsPpal = new BDObject();
	$rsPpal->execQuery($rsql);
	$record = $rsPpal->getRow();

	//recorre los grupos de datos
	foreach ($grupos as $grupoActual) {
		$tabla = [];

		$i = 0;
		while ($i < $rsPpal->cantF()) {
			$nombreCampo = $rsPpal->getFieldName($i);
			if (!in_array($nombreCampo, $xaSkipFields)) {
				$nombreCampo2 = "<b>" . pdfVisible($qinfo->getFieldCaption($nombreCampo)) . "</b>:";

				$pos = strpos($nombreCampo, "_fk");
				if ($pos === FALSE) {
					$tipoCampo = $rsPpal->getFieldType($i);

					//TODO: get data align !!!!
					$fieldGroup = $qinfo->getFieldGrupo($nombreCampo);
					if (sonIguales($grupoActual, $fieldGroup)
							|| (sonIguales("", $fieldGroup) && sonIguales("Datos", $grupoActual))) {
						$valorsel = pdfVisible($rsPpal->getValue($i));
						if (!sonIguales($valorsel, "null") && !esVacio($valorsel)) {
							
							//Tipo texto
							if (esCampoStr($tipoCampo))	{
								if (strcmp($nombreCampo, "clave") == 0)
									$tabla[] = array($nombreCampo2, "**********");
								else {
									//Analiza si el campo es destinado para fotos
									if ($qinfo->isFileField($nombreCampo)) 	//Analiza si el campo es destinado para fotos
										//TODO: adjuntar foto
										//echo(img(getImagesPath() . $valorsel, $valorsel));
										$tabla[] = array($nombreCampo2, $valorsel);
									else
										$tabla[] = array($nombreCampo2, $valorsel);
								}
							} else
							if (esCampoMemo($tipoCampo))  //tipo MEMO
							{
								$strAux = $valorsel;
								$strAux = str_replace("\n", "<br>", $strAux);
								$tabla[] = array($nombreCampo2, $strAux);
							} else
							if (esCampoBoleano($tipoCampo)) //tipo booleano
							{
								if ($valorsel == 1)
									$tabla[] = array($nombreCampo2, "Si");
								else
									$tabla[] = array($nombreCampo2, "No");
							} else
							if (esCampoInt($tipoCampo)) //tipo integer (intentara buscar info de FK)
							{
								$tabla[] = array($nombreCampo2, pdfVisible(getFKValue2($nombreCampo, $valorsel, $qinfo->getFieldsRef(), $fk_cache, false, $record)));
							} else
							if (esCampoFecha($tipoCampo)) //tipo fecha
							{
								$fechaReg = getdate(toTimestamp($rsPpal->getValue($i)));
								$tabla[] = array($nombreCampo2, Sc3FechaUtils::formatFecha2($fechaReg, false));
							} else
							if (esCampoFloat($tipoCampo)) {
								$tabla[] = array($nombreCampo2, formatFloat($valorsel));
							} else	//datos no contemplados
								$tabla[] = array($nombreCampo2, $valorsel);
						}
					}
				}
			}
			$i++;
		}

		$colsOp = array(0 => array("width" => 90, "justification" => "left"));
		if (count($tabla) > 0) {
			$pdf->addTable($tabla, pdfVisible($grupoActual), 0, $colsOp);
			$pdf->addHorizontalLine(1);
		}
	}

	$aTablaOtros = array();
	//busca datos relacionados ------------------------------------------
	$rsLinks = getSqlLinks1($qinfo->getQueryName(), $mid);
	if (!$rsLinks->EOF()) {
		$id = 1;
		while (!$rsLinks->EOF()) {
			$link2QryDesc = $rsLinks->getValue("querydescription");
			$link2Qry = $rsLinks->getValue("queryname");
			$link2Id = $rsLinks->getValueInt("id2");

			$selL = new HtmlSelector("link$id", $link2Qry, $link2Id);
			$selL->setReadOnly();
			$selL->toHtml();

			$aTablaOtros[] = array("<b>$link2QryDesc:</b>", $selL->getDescripcion());

			$rsLinks->Next();
			$id++;
		}

		$colsOp = array(0 => array("width" => 90, "justification" => "left"));
		$pdf->addTable($aTablaOtros, "Otros datos", 0, $colsOp);
	}
	$rsLinks->close();

	//DETALLES
	if ($xConDetalles) {

		$sec = new SecurityManager();
		$rs = $sec->getRsOperacionesRelacionadas($qinfo->getQueryId(), 1);

		while (!$rs->EOF()) {
			$detQuery = $rs->getValue("queryname");
			$detMfield = $rs->getValue("mfield");
			$detQinfo = getQueryObj($detQuery);
			$detIcon = $rs->getValue("icon");
			$detNombre = $rs->getValue("querydescription");

			$i = 1;

			$tabla = array();
			$titulos = array();
			//arma el  con el sql de la consulta
			$sql = $detQinfo->getQuerySql2("", "", "", "", "", $detQuery, $mid, $detMfield, "");

			$rsPpal = new BDObject();
			$rsPpal->execQuery($sql);

			if (!$rsPpal->EOF()) {
				while ($i < $rsPpal->cantF()) {
					$fieldname = $rsPpal->getFieldName($i);
					$pos = strpos($fieldname, "_fk");
					$tipoCampo = $rsPpal->getFieldType($i);
					if (!sonIguales($fieldname, $detMfield) && ($pos === FALSE)) {
						$titulos[] = "<b>" . $detQinfo->getFieldCaption($fieldname) . "</b>";
					}
					$i++;
				}

				$tabla[] = $titulos;

				//recorre registros
				while (!$rsPpal->EOF()) {
					$i = 1;

					$registro = array();
					$record = $rsPpal->getRow();

					//recorre columnas
					while ($i < $rsPpal->cantF()) {
						$fieldname = $rsPpal->getFieldName($i);
						$pos = strpos($fieldname, "_fk");
						$tipoCampo = $rsPpal->getFieldType($i);

						if (!sonIguales($fieldname, $detMfield) && ($pos === FALSE)) {
							$valorsel = pdfVisible($rsPpal->getValue($i));
							if ((strcmp($valorsel, "") == 0) || (strcmp($valorsel, "null") == 0))
								$registro[] = "";
							else
								if (esCampoStr($tipoCampo)) //Tipo texto
							{
								if (strcmp($nombreCampo, "clave") == 0)
									$registro[] = "*******";
								else {
									$registro[] = $valorsel;
								}
							} else
								if (esCampoMemo($tipoCampo))  //tipo MEMO
							{
								$strAux = $valorsel;
								$strAux = str_replace("\n", "<br>", $strAux);
								$registro[] = $strAux;
							} else
								if (esCampoBoleano($tipoCampo)) //tipo booleano
							{
								if ($valorsel == 1)
									$registro[] = "Si";
								else
									$registro[] = "No";
							} else
								if (esCampoInt($tipoCampo)) //tipo integer (intentar� buscar info de FK)
							{
								$registro[] = pdfVisible(getFKValue2($fieldname, $valorsel, $detQinfo->getFieldsRef(), $fk_cache, false, $record));
							} else
								if (esCampoFecha($tipoCampo)) //tipo fecha
							{
								$fechaReg = getdate(toTimestamp($rsPpal->getValue($i)));
								$registro[] = Sc3FechaUtils::formatFecha2($fechaReg, false);
							} else
								if (esCampoFloat($tipoCampo)) {
								$registro[] = formatFloat($valorsel);
							} else	//datos no contemplados
								$registro[] = $valorsel;
						}

						$i++;
					}

					$tabla[] = $registro;
					$rsPpal->Next();
				}

				$pdf->addTable($tabla, $detNombre, 0, "", 7);
			}

			$rs->Next();
		}

		$rs->close();
	}

	return $pdf;
}
