<?php

/**
 * Funciones de nros a letras
 * SC3 - Ing. Marcos C.
 */

function Centenas($VCentena)
{
	$Numeros[0] = "cero";
	$Numeros[1] = "uno";
	$Numeros[2] = "dos";
	$Numeros[3] = "tres";
	$Numeros[4] = "cuatro";
	$Numeros[5] = "cinco";
	$Numeros[6] = "seis";
	$Numeros[7] = "siete";
	$Numeros[8] = "ocho";
	$Numeros[9] = "nueve";
	$Numeros[10] = "diez";
	$Numeros[11] = "once";
	$Numeros[12] = "doce";
	$Numeros[13] = "trece";
	$Numeros[14] = "catorce";
	$Numeros[15] = "quince";
	$Numeros[16] = "dieciseis";
	$Numeros[17] = "diecisiete";
	$Numeros[18] = "dieciocho";
	$Numeros[19] = "diecinueve";

	$Numeros[20] = "veinte";
	$Numeros[21] = "veintiuno";
	$Numeros[22] = "veintidos";
	$Numeros[23] = "veintitres";
	$Numeros[24] = "veinticuatro";
	$Numeros[25] = "veinticinco";
	$Numeros[26] = "veintiseis";
	$Numeros[27] = "veintisiete";
	$Numeros[28] = "veintiocho";
	$Numeros[29] = "veintinueve";

	$Numeros[30] = "treinta";
	$Numeros[40] = "cuarenta";
	$Numeros[50] = "cincuenta";
	$Numeros[60] = "sesenta";
	$Numeros[70] = "setenta";
	$Numeros[80] = "ochenta";
	$Numeros[90] = "noventa";
	$Numeros[100] = "ciento";
	$Numeros[101] = "quinientos";
	$Numeros[102] = "setecientos";
	$Numeros[103] = "novecientos";
	if ($VCentena == 1) { return $Numeros[100]; }
	else if ($VCentena == 5) { return $Numeros[101];}
	else if ($VCentena == 7 ) {return ( $Numeros[102]); }
	else if ($VCentena == 9) {return ($Numeros[103]);}
	else {return $Numeros[$VCentena];}
}


function Unidades($VUnidad) 
{
	$Numeros[0] = "cero";
	$Numeros[1] = "un";
	$Numeros[2] = "dos";
	$Numeros[3] = "tres";
	$Numeros[4] = "cuatro";
	$Numeros[5] = "cinco";
	$Numeros[6] = "seis";
	$Numeros[7] = "siete";
	$Numeros[8] = "ocho";
	$Numeros[9] = "nueve";
	$Numeros[10] = "diez";
	$Numeros[11] = "once";
	$Numeros[12] = "doce";
	$Numeros[13] = "trece";
	$Numeros[14] = "catorce";
	$Numeros[15] = "quince";
	$Numeros[16] = "dieciseis";
	$Numeros[17] = "diecisiete";
	$Numeros[18] = "dieciocho";
	$Numeros[19] = "diecinueve";
	$Numeros[20] = "veinte";
	$Numeros[21] = "veintiuno";
	$Numeros[22] = "veintidos";
	$Numeros[23] = "veintitres";
	$Numeros[24] = "veinticuatro";
	$Numeros[25] = "veinticinco";
	$Numeros[26] = "veintiseis";
	$Numeros[27] = "veintisiete";
	$Numeros[28] = "veintiocho";
	$Numeros[29] = "veintinueve";
	$Numeros[30] = "treinta";
	$Numeros[40] = "cuarenta";
	$Numeros[50] = "cincuenta";
	$Numeros[60] = "sesenta";
	$Numeros[70] = "setenta";
	$Numeros[80] = "ochenta";
	$Numeros[90] = "noventa";
	$Numeros[100] = "ciento";
	$Numeros[101] = "quinientos";
	$Numeros[102] = "setecientos";
	$Numeros[103] = "novecientos";
	
	$tempo = $Numeros[$VUnidad];
	return $tempo;
}


function Decenas($VDecena) 
{
	$Numeros[0] = "cero";
	$Numeros[1] = "uno";
	$Numeros[2] = "dos";
	$Numeros[3] = "tres";
	$Numeros[4] = "cuatro";
	$Numeros[5] = "cinco";
	$Numeros[6] = "seis";
	$Numeros[7] = "siete";
	$Numeros[8] = "ocho";
	$Numeros[9] = "nueve";
	$Numeros[10] = "diez";
	$Numeros[11] = "once";
	$Numeros[12] = "doce";
	$Numeros[13] = "trece";
	$Numeros[14] = "catorce";
	$Numeros[15] = "quince";
	$Numeros[16] = "dieciseis";
	$Numeros[17] = "diecisiete";
	$Numeros[18] = "dieciocho";
	$Numeros[19] = "diecinueve";
	$Numeros[20] = "veinte";
	$Numeros[21] = "veintiuno";
	$Numeros[22] = "veintidos";
	$Numeros[23] = "veintitres";
	$Numeros[24] = "veinticuatro";
	$Numeros[25] = "veinticinco";
	$Numeros[26] = "veintiseis";
	$Numeros[27] = "veintisiete";
	$Numeros[28] = "veintiocho";
	$Numeros[29] = "veintinueve";
	$Numeros[30] = "treinta";
	$Numeros[40] = "cuarenta";
	$Numeros[50] = "cincuenta";
	$Numeros[60] = "sesenta";
	$Numeros[70] = "setenta";
	$Numeros[80] = "ochenta";
	$Numeros[90] = "noventa";
	$Numeros[100] = "ciento";
	$Numeros[101] = "quinientos";
	$Numeros[102] = "setecientos";
	$Numeros[103] = "novecientos";
	$tempo = ($Numeros[$VDecena]);
	return $tempo;
}


function NumerosALetras($Numero)
{
	debug("NumerosALetras($Numero)");
	$Decimales = 0;
	//$Numero = intval($Numero);
	$letras = "";
	while ($Numero != 0)
	{
		// '*---> Validación si se pasa de 100 millones
		if ($Numero >= 1000000000) 
		{
			$letras = "Error en Conversion a Letras";
			$Numero = 0;
			$Decimales = 0;
		}
	
		// '*---> Centenas de Millón
		if (($Numero < 1000000000) && ($Numero >= 100000000))
		{
			if ((intval($Numero / 100000000) == 1) && (($Numero - (intval($Numero / 100000000) * 100000000)) < 1000000))
			{
				$letras .= (string) "cien millones ";
			}
			else 
			{
				$letras = $letras . Centenas(intval($Numero / 100000000));
				if ((intval($Numero / 100000000) <> 1) && (intval($Numero / 100000000) <> 5) && (intval($Numero / 100000000) <> 7) && (intval($Numero / 100000000) <> 9)) 
				{
					$letras .= (string) "cientos ";
				}
				else 
				{
					$letras .= (string) " ";
				}
			}
			$Numero = $Numero - (intval($Numero / 100000000) * 100000000);
		}
	
		// '*---> Decenas de Millón
		if (($Numero < 100000000) && ($Numero >= 10000000)) 
		{
			if (intval($Numero / 1000000) < 31) 
			{
				$tempo = Decenas(intval($Numero / 1000000));
				$letras .= (string) $tempo;
				$letras .= (string) " millones ";
				$Numero = $Numero - (intval($Numero / 1000000) * 1000000);
			}
			else 
			{
				$letras = $letras . Decenas(intval($Numero / 10000000) * 10);
				$Numero = $Numero - (intval($Numero / 10000000) * 10000000);
				if ($Numero > 1000000) 
				{
					$letras .= $letras . " y ";
				}
			}
		}
	
		// '*---> Unidades de Millón
		if (($Numero < 10000000) && ($Numero >= 1000000)) 
		{
			$tempo= (intval($Numero / 1000000));
			if ($tempo == 1) 
			{
				$letras .= (string) " un millon ";
			}
			else 
			{
				$tempo= Unidades(intval($Numero / 1000000));
				$letras .= (string) $tempo;
				$letras .= (string) " millones ";
			}
			$Numero = $Numero - (intval($Numero / 1000000) * 1000000);
		}
	
		// '*---> Centenas de Millar
		if (($Numero < 1000000) && ($Numero >= 100000)) 
		{
			$tempo=(intval($Numero / 100000));
			$tempo2=($Numero - ($tempo * 100000));
			if (($tempo == 1) && ($tempo2 < 1000)) 
			{
				$letras .= (string) "cien mil ";
			}
			else 
			{
				$tempo = Centenas(intval($Numero / 100000));
				$letras .= (string) $tempo;
				$tempo = (intval($Numero / 100000));
				if (($tempo <> 1) && ($tempo <> 5) && ($tempo <> 7) && ($tempo <> 9)) 
				{
					$letras .= (string) "cientos ";
				}
				else 
				{
					$letras .= (string) " ";
				}
			}
			$Numero = $Numero - (intval($Numero / 100000) * 100000);
		}
	
		// '*---> Decenas de Millar
		if (($Numero < 100000) && ($Numero >= 10000)) 
		{
			$tempo= (intval($Numero / 1000));
			if ($tempo < 31) 
			{
				$tempo = Decenas(intval($Numero / 1000));
				$letras .= (string) $tempo;
				$letras .= (string) " mil ";
				$Numero = $Numero - (intval($Numero / 1000) * 1000);
			}
			else 
			{
				$tempo = Decenas(intval($Numero / 10000) * 10);
				$letras .= (string) $tempo;
				$Numero = $Numero - (intval(($Numero / 10000)) * 10000);
				if ($Numero > 1000) 
				{
					$letras .= (string) " y ";
				}
				else 
				{
					$letras .= (string) " mil ";
				}
			}
		}
	
	
		// '*---> Unidades de Millar
		if (($Numero < 10000) && ($Numero >= 1000)) 
		{
			$tempo = (intval($Numero / 1000));
			if ($tempo == 1) 
			{
				$letras .= (string) "un";
			}
			else 
			{
				$tempo = Unidades(intval($Numero / 1000));
				$letras .= (string) $tempo;
			}
			$letras .= (string) " mil ";
			$Numero = $Numero - (intval($Numero / 1000) * 1000);
		}
		
		// '*---> Centenas
		if (($Numero < 1000) && ($Numero > 99)) 
		{
			//estaba antes (MC)
			//if ((intval($Numero / 100) == 1) && (($Numero - (intval($Numero / 100) * 100)) < 1)) 
			if ($Numero == 100)
			{
				$letras .= "cien ";
			}
			else 
			{
				$temp=(intval($Numero / 100));
				$l2=Centenas($temp);
				$letras .= (string) $l2;
				if ((intval($Numero / 100) <> 1) && (intval($Numero / 100) <> 5) && (intval($Numero / 100) <> 7) && (intval($Numero / 100) <> 9)) 
				{
					$letras .= "cientos ";
				}
				else 
				{
					$letras .= (string) " ";
				}
			}
		
			$Numero = $Numero - (intval($Numero / 100) * 100);
		}
	
	// '*---> Decenas
	if (($Numero < 100) && ($Numero > 9)) 
	{
		if ($Numero <= 29) 
		{
			$tempo = Decenas(intval($Numero));
			$letras .= $tempo;
			$Numero = $Numero - intval($Numero);
		}
		else 
		{
			$tempo = Decenas(intval(($Numero / 10)) * 10);
			$letras .= (string) $tempo;
			$Numero = $Numero - (intval(($Numero / 10)) * 10);
			if ($Numero > 0.99) 
			{
				$letras .= (string) " y ";
			}
		}
	}
	
	// '*---> Unidades
	if (($Numero < 10) && ($Numero > 0.99)) 
	{
		$tempo = Unidades(intval($Numero));
		$letras .= (string) $tempo;
		
		$Numero = $Numero - intval($Numero);
	}
	
	
	// '*---> Decimales
	if ($Decimales > 0) 
	{
	
	}
	else 
	{
		if (($letras <> "Error en Conversion a Letras") && (strlen(Trim($letras)) > 0)) 
		{
			$letras .= (string) " ";
		}
	}
	return $letras;
	}
}

function testNumbers()
{
	//favor de teclear a mano la cantidad numerica a convertir y asignarla a $tt
	$tt = 13011.21;
	
	$tt = $tt+0.009;
	$Numero = intval($tt);
	$Decimales = $tt - intval($tt);
	$Decimales= $Decimales*100;
	$Decimales= intval($Decimales);
	$x=NumerosALetras($Numero);
	echo ($x);
	if ($Decimales > 0)
	{
		$y = NumerosALetras($Decimales);
		echo (" pesos con ");
		echo ($y);
		echo (" centavos");
	}
	else 
	{
		echo ("cero centavos");
	}
}

?>
