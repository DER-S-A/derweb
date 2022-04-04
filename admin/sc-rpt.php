<?php
/**
 * Funciones y clases de reportes de sc3.com.ar
 * @author Marcos C
 * @property: rpt
 * Fecha: dic-2016
 */



/**
 * Agrega un reporte basado en los campos de una tabla
 * @param string $xtabla
 * @param string $xtitulo
 * @param string $xpagesize
 * @param string $xapaisada
 * @param number $xfontSize
 */
function rptAgregarTabla($xtabla, $xtitulo, $xpagesize = "a4", $xapaisada = 0, $xfontSize = 9)
{
	$codigo = strtoupper($xtabla);
	$rsRpt = locateRecordWhere("sc_rpt_reportes", "codigo = '$codigo'");
	if ($rsRpt->EOF())
	{
		$font = getParameter("sc3-font-pdf", "Helvetica.afm");
		
		$values = array();
		$values['codigo'] = $codigo;
		$values['nombre'] = $xtitulo;
		$values['hoja'] = $xpagesize;
		$values['tam_fuente'] = $xfontSize;
		$values['nombre_fuente'] = $font;
		$values['apaisada'] = $xapaisada;
		$values['margen_izq'] = 1;
		$values['margen_inf'] = 1;
		$values['margen_der'] = 1;
		$values['margen_sup'] = 1;
		
		$sql = insertIntoTable2("sc_rpt_reportes", $values);
		
		$bd = new BDObject();
		$idreporte = $bd->execInsert($sql);
		
		$qinfo = getQueryObj(getQueryName($xtabla));
		$aFields = $qinfo->getFieldsDef();

		$POS_Y_INICIAL = 750;
		
		$POS_X_DIFF = 200;
		$POS_Y_DIFF = 25;
		
		$posX = 50;
		$posY = $POS_Y_INICIAL;
		$cant = 0;
		foreach ($aFields as $fieldName => $fieldDef)
		{
			$values = array();
			$values['idreporte'] = $idreporte;
			$values['texto'] = $fieldName;
			$values['es_campo'] = 1;
			$values['pagina'] = 1;
			$values['diff_fuente'] = 0;
			$values['pos_x'] = $posX;
			$values['pos_y'] = $posY;
			$values['ancho_max'] = null;
			$values['negrita'] = 0;
			$values['italica'] = 0;
			$values['color'] = '';
				
			$sql = insertIntoTable2("sc_rpt_reportes_campos", $values);
			$bd->execQuery($sql);
			
			$cant++;
			$posY -= $POS_Y_DIFF;
			if ($cant == 25)
			{
				$cant = 0;
				$posX += $POS_X_DIFF;
				$posY = $POS_Y_INICIAL;
			}
		}
	}
}

/**
 * Invocado desde JS de sc-rpt-disenar.php, graba un campo luego q fuÃ© movido
 * @param unknown $aParams
 * @return multitype:string
 */
function rptGuardarCampo($aParams)
{
	$idreporte = $aParams["idreporte"];
	$nombre = $aParams["nombre"];
	$x = $aParams["x"];
	$y = $aParams["y"];
	$pagina = $aParams["pagina"];
	if ($pagina == 0)
		$pagina = 1;
	
	$sql = "update sc_rpt_reportes_campos
			set pos_x = $x, pos_y = $y, pagina = $pagina
			where idreporte = $idreporte and texto = '$nombre'";
	
	$bd = new BDObject();
	$bd->execQuery($sql);
	
	$result = array();
	$result["RESULT"] = "Campo $nombre ($x, $y) de reporte $idreporte guardado OK en pagina $pagina.";
	return $result;
}


/**
 * Clase para cargar reportes de la definicion
 * @author Marcos C, sc3.com,ar
 * @fecha ene-2017
 */
class ScReportePdf
{
	var $mCodigo = "";
	var $mDef = array();
	var $mFieldsDef = array();
	var $mCorrimientos = array();
	var $mFontSize = 9;
	var $mDebug = 0;

	function __construct($xCodigo)
	{
		$this->mCodigo = $xCodigo;
		$this->loadDef();
	}
	
	function setDebug()
	{
		$this->mDebug = 1;
	}

	/**
	 * Carga la definicion según tabla sc_rpt_reportes y sc_rpt_reportes_campos
	 */
	function loadDef()
	{
		$rsDef = locateRecordWhere("sc_rpt_reportes", "codigo = '" . $this->mCodigo . "'", true);
		$idrep = $rsDef->getId();

		$this->mDef = $rsDef->getRow();
		$this->mFontSize = $rsDef->getValueInt("tam_fuente");

		$rsFields = locateRecordWhere("sc_rpt_reportes_campos", "idreporte = $idrep", true, "pagina");
		while (!$rsFields->EOF())
		{
			$pagina = $rsFields->getValueInt("pagina");
			$this->mFieldsDef[$pagina][] = $rsFields->getRow();
			$rsFields->Next();
		}
		
		$i = 1;
		while ($i <= 10)
		{
			$this->mCorrimientos[$i]["x"] = 0.00;
			$this->mCorrimientos[$i]["y"] = 0.00;
			$i++;
		}
		
		$rsCorrimientos = locateRecordWhere("sc_rpt_reportes_corrimientos", "idreporte = $idrep", true, "hoja");
		while (!$rsCorrimientos->EOF())
		{
			$hoja = $rsCorrimientos->getValueInt("hoja");
			$this->mCorrimientos[$hoja]["x"] = $rsCorrimientos->getValueFloat("corrimiento_x");
			$this->mCorrimientos[$hoja]["y"] = $rsCorrimientos->getValueFloat("corrimiento_y");
			$rsCorrimientos->Next();
		}
	}


	/**
	 * Crea el objeto pdf según definicion
	 * @return HtmlPdf
	 */
	function iniciarPdf()
	{
		$pdf = new HtmlPdf($this->getTitulo(), $this->getTamanio(), $this->getApaisada(), true, true, false);

		$pdf->getPdfObj()->selectFont('./pdf/fonts/' . $this->getFont());
		$pdf->setCmMargins($this->mDef["margen_sup"], $this->mDef["margen_inf"], $this->mDef["margen_izq"], $this->mDef["margen_der"]);
		return $pdf;
	}
	
	function getTitulo()
	{
		return $this->mDef["nombre"];
	}
	
	function getTamanio()
	{
		return $this->mDef["hoja"];
	}
	
	function getApaisada()
	{
		if ($this->mDef["apaisada"] == 1)
			return true;
		return false; 
	}

	function getFont()
	{
		return $this->mDef["nombre_fuente"];
	}
	
	/**
	 * Al PDF dado le agrega una hoja de la definicion con sus datos
	 * @param HtmlPdf $xpdf
	 * @param BDObject $xrs
	 * @param number $xHoja
	 */
	function agregarHoja($xpdf, $xrs, $xHoja = 1)
	{
		$corrX = $this->mCorrimientos[$xHoja]["x"];
		$corrY = $this->mCorrimientos[$xHoja]["y"];
		
		if (isset($this->mFieldsDef[$xHoja]))
		{
			foreach ($this->mFieldsDef[$xHoja] as $hoja => $fieldDef)
			{
				$texto = $fieldDef["texto"];
				
				//es un campo del RS, caso contrario es un texto literal
				if ($fieldDef["es_campo"] == 1)
				{
					if ($this->mDebug == 1)
						$texto = "$texto: " . $xrs->getValue($fieldDef["texto"]);
					else
						$texto = $xrs->getValue($fieldDef["texto"]);
				}

				$tamanio = $this->mFontSize + $fieldDef["diff_fuente"];
				$anchoMax = $fieldDef["ancho_max"] * 1.0;
				$completarCon = $fieldDef["completar_con"];
				$expresionEval = $fieldDef["expresion_eval"];
				if (!esVacio($expresionEval))
				{
					$record = $xrs->getRow();
					eval($expresionEval);
				}

				$aTexto = explode("\r\n", $texto);
				$aTexto2 = array();
				$i = 0;
				while ($i < count($aTexto))
				{
					$linea = $aTexto[$i];
					if ($anchoMax > 0)
					{
						$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($tamanio, $this->sinCaracteresEspeciales2($linea));
						
						if ($anchoImpresion <= $anchoMax)
						{
							//si no es una línea vacía la completa con $completarCon
							if (strlen($linea) > 2 && !esVacio($completarCon))
							{		
								//agrega relleno hasta q llegue al max	
								while ($anchoImpresion < $anchoMax)	
								{
									$linea .= $completarCon;
									$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($tamanio, $this->sinCaracteresEspeciales2($linea));
								}
							}
							
							if ($fieldDef["negrita"] == 1)
								$linea = htmlBold($linea);
							
							if ($this->mDebug == 1)
								$aTexto2[] = "$anchoImpresion $linea";
							else 
								$aTexto2[] = $linea;
						}
						else
						{
							//busca cuantas palabras entran en el ancho indicado
							$linea = str_replace("  ", " ", $linea);
							$linea = str_replace("  ", " ", $linea);
							$aPalabras = explode(" ", $linea);
							if (count($aPalabras) > 0)
							{
								$tmpFrase = $aPalabras[0];
								
								$j = 1;
								while ($j <= count($aPalabras))
								{
									$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($tamanio, $this->sinCaracteresEspeciales2($tmpFrase));

									while ($j <= count($aPalabras) && ($anchoImpresion <= $anchoMax))
									{
										$fraseFinal = $tmpFrase;
										if (isset($aPalabras[$j]))
											$tmpFrase .= " " . $aPalabras[$j];
										
										$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($tamanio, $this->sinCaracteresEspeciales2($tmpFrase));
										
										//encontro la palabra que se va del ancho límite
										if ($anchoImpresion > $anchoMax)
										{
											$tmpFrase = $aPalabras[$j];
										}
										$j++;
									}
									
									//completa o justifica
									if (endsWith($fraseFinal, "." . $completarCon) && !esVacio($completarCon))
										$aTexto2[] = "" . $this->completarCon($xpdf, $fraseFinal, $tamanio, $anchoMax, $completarCon);
									else
										$aTexto2[] = "" . $this->justificar($xpdf, $fraseFinal, $tamanio, $anchoMax);
								}							
							}
							else 
								$aTexto2[] = $linea;
						}
					}
					else
						$aTexto2[] = $linea;

					$i++;
				}
				
				//imprime, ya está todo procesado
				$i = 0;
				while ($i < count($aTexto2))
				{
					$linea = pdfVisible($aTexto2[$i], true);
					$xpdf->getPdfObj()->addText($fieldDef["pos_x"] + $corrX, $fieldDef["pos_y"] - ($i * ($tamanio + 1)) + $corrY, $tamanio, $linea);
					$i++;
				}
				
			}
		}
	}

	
	/**
	 * Agrega espacios si es que se puede
	 * @param HtmlPdf $xpdf
	 * @param unknown $xlinea
	 * @param unknown $xanchoMax
	 */
	function justificar($xpdf, $xlinea, $xtamanio, $xanchoMax)
	{
		$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($xtamanio, $this->sinCaracteresEspeciales2($xlinea)) * 1.00;
		$anchoEspacio = $xpdf->getPdfObj()->getTextWidth($xtamanio, " ") * 1.00;
		
		//Si al menos entran dos espacios, los agrega 
		if ($anchoImpresion < ($xanchoMax - ($anchoEspacio)))
		{
			$cantEspacios = round(($xanchoMax - $anchoImpresion) / $anchoEspacio, 0);
			$cantEspaciosAgregados = $cantEspacios;

			$relleno = "  ";
				
			$linea = "";
			$aPalabras = explode(" ", $xlinea);
			$cantEspaciosExistentes = count($aPalabras) - 1;
			if ($cantEspacios > ($cantEspaciosExistentes * 3))
				$relleno = "   ";
			
			$i = 0;
			while ($i < count($aPalabras))
			{
				if ($i == 0)
					$linea = $aPalabras[$i];
				else
				{
					//agrega de a dos espacios, el original y el agregado
					if ($cantEspacios > 0)
					{
						$linea .= $relleno . $aPalabras[$i];
						$cantEspacios--;
					}
					else
						$linea .= " " . $aPalabras[$i];
				}
				$i++;
			}
			
		}
		else
			$linea = $xlinea;
		
		if ($this->mDebug == 1)
			$linea = "$anchoImpresion $cantEspaciosAgregados $linea";
		return $linea;
	}

	
	/**
	 * Completa una linea hasta el ancho máximo
	 * @param HtmlPdf $xpdf
	 * @param string $xlinea
	 * @param int $xtamanio
	 * @param int $xanchoMax
	 * @param string $xCompletarCon
	 * @return string
	 */
	function completarCon($xpdf, $xlinea, $xtamanio, $xanchoMax, $xCompletarCon)
	{
		$linea = $xlinea;
		if (esVacio($xCompletarCon))
			return $linea;
		
		$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($xtamanio, $this->sinCaracteresEspeciales2($linea));
		
		//agrega relleno hasta q llegue al max
		while ($anchoImpresion < $xanchoMax)
		{
			$linea .= $xCompletarCon;
			$anchoImpresion = $xpdf->getPdfObj()->getTextWidth($xtamanio, $this->sinCaracteresEspeciales2($linea));
		}
		
		return $linea;
	}
	
	/**
	 * Quita caracteres raros, porque calcula mal el ancho de la impresion el PDF
	 * @param string $xstr
	 * @return string
	 */
	function sinCaracteresEspeciales2($xstr)
	{
		$sacar = array("á", "Á", "é", "í", "ó", "ú", "ñ", "Ñ", chr(164), chr(165), "°", ")", "(");
		$poner = array("a", "A", "e", "i", "o", "u", "n", "N", "n", "n", " ", " ", " ");
		
		$str = str_replace($sacar, $poner, $xstr);
		return $str;
	}
	
	
}






?>