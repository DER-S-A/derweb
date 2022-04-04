<?php


/**
 * Retorna un nombre apropiado para un archivo CSV dado una tabla o un título
 * @param string $xtable
 * @return string
 */
function sc3CsvFilename($xtable, $xbackup = 1)
{
	$xtable = strtolower(sinCaracteresEspecialesNiEspacios($xtable));
	$fecha = date("Ymd-his");
	if ($xbackup)
	{
		$file = $xtable . "-" . $fecha . ".csv";
		$result = path() . "/backups/$file";
	}
	else
	{
		$file = $xtable . "-" . getCurrentUser() . "-" . $fecha . ".csv";
		$result = path() . "/tmp/$file";
	}	
	return $result;
}


function sc3CsvSaveArray($xfilename, $xaheaders, $xdata)
{
	$fp = fopen($xfilename, 'w');
	
	if (is_array($xaheaders) && sizeof($xaheaders) > 0)
		fwrite($fp, implode(";", $xaheaders) . PHP_EOL);
		
	foreach ($xdata as $row) 
	{
		if (is_array($row))
		{
			//quita los ENTER y <b>
			foreach($row as $campo => $valor)
			{
				$valor = str_replace("\r\n", " ", $valor);
				$valor = str_replace("<b>", " ", $valor);
				$valor = str_replace("</b>", " ", $valor);
				$valor = str_replace(";", " ", $valor);
				
				$valor = trim(sinCaracteresEspeciales($valor));
				
				$row[$campo] = $valor;
				
				//si es un valor con el simbolo pesos, se lo quita
				if (esCampoConMoneda($valor))
				{
					$valores = explode(" ", $valor);
					$valor = $valores[1];
				}

				//TODO: VER el separador de miles del usuario !!!!
				$separadorMiles = getParameter("sc3-separador-miles-" . getCurrentUser(), "");
				$separadorDecimales = getParameter("sc3-separador-decimales-" . getCurrentUser(), ".");
				$valor = str_replace($separadorMiles, "", $valor);
					
				$row[$campo] = $valor;
				
				/*
				//cuando hay miles, la coma "," no da en is_numeric()
				$separadorMiles = getParameter("sc3-separador-miles", ",");
				$separadorDecimales = getParameter("sc3-separador-decimales", ".");
					
				if (is_numeric(str_replace($separadorDecimales, "", str_replace($separadorMiles, "", $valor))))
				{
					$valor = str_replace($separadorMiles, "", $valor);
					$valor = str_replace($separadorDecimales, ".", $valor);
				}
				
				if (is_numeric($valor))
					$row[$campo] = formatFloat($valor * 1.00, 2, true);
				*/
			}
			
			fwrite($fp, implode(";", $row) . PHP_EOL);
		}
	}
		
	fclose($fp);
}	


function sc3CsvSaveRs($xfilename, $xaheaders, $xrs, $xlimit = 100000)
{
	$fp = fopen($xfilename, 'w');
	
	if (is_array($xaheaders) && sizeof($xaheaders) > 0)
		fwrite($fp, implode(";", $xaheaders) . PHP_EOL);

	$i = 0;
	while (!$xrs->EOF() && $i <= $xlimit)
	{
		$row = $xrs->getRow();
		
		//quita los ENTER
		foreach($row as $campo => $valor)
		{
			$valor = str_replace("\r\n", " ", $valor);
			$valor = str_replace("<b>", " ", $valor);
			$valor = str_replace("</b>", " ", $valor);
			$valor = str_replace(";", "", $valor);
			$valor = str_replace(",", "", $valor);
				
			$row[$campo] = sinCaracteresEspeciales($valor);
			if (is_numeric($valor))
				$row[$campo] = round($valor * 1.00, 2);
		}
		
		fwrite($fp, implode(";", $row) . PHP_EOL);
		$xrs->Next();
		$i++;
	}

	fclose($fp);
}


?>