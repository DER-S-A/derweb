<?php


/**
 * Clase con funciones comunes de fecha
 */
class Sc3FechaUtils {

	/**
	 * Traduce de mes nro a mes escrito, ene, feb, mar, abr....
	 * @param $xMes int
	 */
	public static function mesAStr($xMes)
	{
		if ($xMes == 1)
			return 'ene';
		else
		if ($xMes == 2)
			return 'feb';
		else
		if ($xMes == 3)
			return 'mar';
		else
		if ($xMes == 4)
			return 'abr';
		else
		if ($xMes == 5)
			return 'may';
		else
		if ($xMes == 6)
			return 'jun';
		else
		if ($xMes == 7)
			return 'jul';
		else
		if ($xMes == 8)
			return 'ago';
		else
		if ($xMes == 9)
			return 'sep';
		else
		if ($xMes == 10)
			return 'oct';
		else
		if ($xMes == 11)
			return 'nov';
		else
		if ($xMes == 12)
			return 'dic';
		else
			return 'mes invalido ' . $xMes;
	}
	

	/* 
	* Traduce de mes nro a mes escrito: enero, febrero, marzo...
	*/
	public static function mesAStr2($xMes)
	{
		if ($xMes == 1)
			return 'enero';
		else
		if ($xMes == 2)
			return 'febrero';
		else
		if ($xMes == 3)
			return 'marzo';
		else
		if ($xMes == 4)
			return 'abril';
		else
		if ($xMes == 5)
			return 'mayo';
		else
		if ($xMes == 6)
			return 'junio';
		else
		if ($xMes == 7)
			return 'julio';
		else
		if ($xMes == 8)
			return 'agosto';
		else
		if ($xMes == 9)
			return 'septiembre';
		else
		if ($xMes == 10)
			return 'octubre';
		else
		if ($xMes == 11)
			return 'noviembre';
		else
		if ($xMes == 12)
			return 'diciembre';
		else
			return 'mes invalido ' . $xMes;
	}


	public static function now()
	{
		return getdate();
	}
	
	
	public static function diasACastellano($xday)
	{
		$diasIngles = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		$diasSemana = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
		return str_replace($diasIngles, $diasSemana, $xday);
	}
	
	/**
	 * Retorna si tiene formato de fecha dd/mm/aaaa
	**/
	public static function strToFecha($xpalabra)
	{
		$a = explode("/", $xpalabra);
		return $a[2] . "-" . $a[1] . "-" . $a[0];	
	}


	/**
	 * Retorna Lun 15 o Lun 15/10 
	 */
	public static function diaSemana($xiddia, $xdiaMes, $xmes = 0)
	{
		$diasSemana = array('Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab');
		$rta = $diasSemana[$xiddia] . " $xdiaMes";
		if ($xmes != 0)
			$rta .= "/" . $xmes;
		return $rta;	
	}


	public static function getFechaDif($xdif)
	{
		$temp = "hace";
		if ($xdif < 0)
		{
			$temp = "en";
			$xdif = $xdif * -1;	
		}	
		$xdif = round(($xdif / 60) / 60, 0);
		if ($xdif == 0)
			return "($temp minutos)";
		if ($xdif == 1)
			return "($temp una hora)";
		if ($xdif < 15)
			return "($temp " . $xdif . " horas)";
		$xdif = round($xdif / 24, 0);
		if ($xdif == 1)
			return "(ayer)";
		if ($xdif < 29)
			return "($temp " . $xdif . " dias)";
		$xdif = round($xdif / 31, 0);
		if ($xdif == 1)
			return "($temp un mes)";
		if ($xdif < 21)
			return "($temp " . $xdif . " meses)";
		$xdif = round($xdif / 12, 0);
		return "($temp " . $xdif . " a&ntilde;os)";		
	}
	

	/** 
	* Formatea una fecha y la retorna en un DIV
	*/
	public static function formatFecha($xfecha, $xmostrarHora = true, $xcompactar = false)
	{
		$xfecha = $xfecha;
		$fecha = "<div title=\"";

		$tieneHora = false;
		if (((strcmp($xfecha["hours"], "0") != 0) || 
				(strcmp($xfecha["minutes"], "0") != 0)))
			$tieneHora = true;

		$hoy = time();
		if ($xcompactar && $xmostrarHora && $tieneHora)
			$lapso = 10 * 60 * 60;
		else
			$lapso = 0;	

		$fecha2 = mktime($xfecha["hours"], $xfecha["minutes"], 0, $xfecha["mon"], $xfecha["mday"], $xfecha["year"]);

		$diasSemana = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
		$titulo = $diasSemana[date('N', $fecha2)] . " ";
		$difDias = ($hoy - $fecha2);
		$titulo .= Sc3FechaUtils::getFechaDif($difDias);
		$fecha .= $titulo . "\">";	
		
		$separador = getParameter("sc3-separador-fecha", "/");
		if (abs($hoy - $fecha2) < $lapso)
			$fecha .= "<b>";
		else
		{
			$fecha .= $xfecha["mday"] . $separador . $xfecha["mon"] . $separador . $xfecha["year"];
		}
			
		if (((strcmp($xfecha["hours"], "0") != 0) || 
				(strcmp($xfecha["minutes"], "0") != 0)) && $xmostrarHora)
		{
			$fecha .= " " . str_pad($xfecha["hours"], 2, "0", STR_PAD_LEFT); 
			$fecha .= ":"; 
			$fecha .= str_pad($xfecha["minutes"], 2, "0", STR_PAD_LEFT); 
			if (!$xcompactar)
			{
				$fecha .= ":";
				$fecha .= str_pad($xfecha["seconds"], 2, "0", STR_PAD_LEFT); 
			}	
		}
		
		if (abs($hoy - $fecha2) < $lapso)
			$fecha .= "</b>";

		$fecha .= "</div>";
		return $fecha;		
	}

	/**
	 * Muestra una fecha en formato de hora, ej: 15:32
	 * @param array $xfecha
	 * @return string
	 */
	public static function formatHora($xfecha)
	{
		$fecha = " " . str_pad($xfecha["hours"], 2, "0", STR_PAD_LEFT);
		$fecha .= ":";
		$fecha .= str_pad($xfecha["minutes"], 2, "0", STR_PAD_LEFT);

		return $fecha;
	}


	/**
	 * Retrocede en el tiempo 3 meses (de hoy a: 18-jul-2021)
	 */
	public static function fechaHace3Meses()
	{
		$mes = date("m");
		$anio = date("Y");
		if ($mes == 3)
		{
			$mes = 12;
			$anio--;
		}
		else
			if ($mes == 2)
			{
				$mes = 11;
				$anio--;
			}
		else
			if ($mes == 1)
			{
				$mes = 10;
				$anio--;
			}
		else
			$mes = $mes - 3;
		
		$fecha = $anio . "-";
		$fecha .= str_pad($mes, 2, "0", STR_PAD_LEFT) . "-01";
		$fecha .= " 00:00:00";

		return $fecha;
	}


	/**
	* Retorna la fecha actual en string para mostrar
	*/
	public static function formatFecha2($xfecha, $xtime = true, $xyear = true)
	{
		$fecha = "";
		$fecha .= $xfecha["mday"] . "/" . $xfecha["mon"];
		if ($xyear)
			$fecha .= "/" . $xfecha["year"];
		if ($xtime)
			$fecha .= " " . $xfecha["hours"] . ":" . str_pad($xfecha["minutes"], 2, "0", STR_PAD_LEFT);
		return $fecha;		
	}

	/**
	 * Retorna la fecha en el formato dado
	 * AAAAMMDD, AAAA-MM-DD, DD/MM/AAAA
	 */
	public static function formatFechaFormato($xfecha, $xformato = "AAAAMMDD")
	{
		if (sonIguales($xformato, "AAAAMMDD"))
		{
			$fecha = $xfecha["year"];
			if ($xfecha["mon"] < 10)
				$fecha .= "0" . $xfecha["mon"];
			else
				$fecha .= $xfecha["mon"];
	
			if ($xfecha["mday"] < 10)
				$fecha .= "0" . $xfecha["mday"];
			else
				$fecha .= $xfecha["mday"];
	
			return $fecha;
		}
	
		if (sonIguales($xformato, "AAAA-MM-DD"))
		{
			$fecha = $xfecha["year"];
			if ($xfecha["mon"] < 10)
				$fecha .= "-0" . $xfecha["mon"];
			else
				$fecha .= "-" . $xfecha["mon"];
	
			if ($xfecha["mday"] < 10)
				$fecha .= "-0" . $xfecha["mday"];
			else
				$fecha .= "-" . $xfecha["mday"];
	
			return $fecha;
		}
	
		if (sonIguales($xformato, "DD/MM/AAAA"))
		{
			if ($xfecha["mday"] < 10)
				$fecha = "0" . $xfecha["mday"];
			else
				$fecha = $xfecha["mday"];
			$fecha = $fecha . "/";	
				
			if ($xfecha["mon"] < 10)
				$fecha .= "0" . $xfecha["mon"];
			else
				$fecha .= $xfecha["mon"];
				
			$fecha = $fecha . "/";
			$fecha .= $xfecha["year"];
			
			return $fecha;
		}
		
		return $xformato;
	}

	/**
	* retorna la fecha de hoy en formato para el SQL
	* @param boolean $xTime00 En true si va con 00:00 sin� 23:59 
	* @return string
	*/
	public static function formatFechaHoySql($xTime00 = true)
	{
		$fecha = date("Y") . "-" . str_pad(date("m"), 2, "0", STR_PAD_LEFT) . "-" . str_pad(date("d"), 2, "0", STR_PAD_LEFT);
		if ($xTime00)
			$fecha .= " 00:00";
		else
			$fecha .= " 23:59";
		return $fecha;
	}
   

	/**
	 * Retorna la fecha actual en string para mostrar en grilla
	 */
	public static function formatFechaGrid($xfecha)
	{
		$fecha = "";

		$hoy = getdate(time());
		if ($hoy["year"] == $xfecha["year"] && $hoy["mon"] == $xfecha["mon"]  && $hoy["mday"] == $xfecha["mday"])
		{
			if (($xfecha["hours"] == 0) && ($xfecha["minutes"] == 0))
				$fecha .= "<div title=\"Hoy\"><b>" . $xfecha["mday"] . "/" . $xfecha["mon"] . "/" . $xfecha["year"] . "</b></div>";
			else	
				$fecha .= "<div title=\"Hoy\"><b>" . $xfecha["hours"] . ":" . str_pad($xfecha["minutes"], 2, "0", STR_PAD_LEFT) . "</b></div>";
		}
		else
			$fecha .= $xfecha["mday"] . "/" . $xfecha["mon"] . "/" . $xfecha["year"];
		return $fecha;
	}


	/**
	 * Arma texto único por hora para incluir en URLS de scripts y css, asi evita cach� de una hr a otra
	 * @return string
	 */
	public static function formatFechaUrl()
	{
		$hoy = getdate(time());
		$min = round($hoy["minutes"] * 1 / 10);
		$fecha = $hoy["year"] . "" . $hoy["mon"] . "" . $hoy["mday"] . $hoy["hours"] . $min;
		return $fecha;
	}


	/**
	 * Convierte 15/10/2018 a 2018-10-15
	 * @param string $xfecha
	 */
	public static function fechaFechaLegibleASql($xfecha)
	{
		$aFecha = explode("/", $xfecha);
		if (count($aFecha) < 3)
			return "";
		return $aFecha[2] . "-" . $aFecha[1] . "-" . $aFecha[0];
	}

	/**
	 * Convierte 2017-10-01 a 01/10/2017
	 * @param string $xfecha
	 */
	public static function fechaSqlAFechaLegible($xfecha)
	{
		$aFecha = explode("-", $xfecha);
		if (count($aFecha) < 3)
			return "";
		return $aFecha[2] . "/" . $aFecha[1] . "/" . $aFecha[0];
	}


	public static function formatFechaHoy2($xtime = true)
	{
		$hoy = getdate(time());
		return Sc3FechaUtils::formatFecha2($hoy, $xtime);
	}

 
	public static function formatFechaHoy()
	{
		$hoy = getdate(time());

		$fecha = "";
		$fecha .= $hoy["mday"] . " de ";
		$fecha .= Sc3FechaUtils::mesAStr2($hoy["mon"]) . " de ";
		$fecha .= $hoy["year"];
		return $fecha;		
	}


	/**
	 * Retorna la fecha de ayer
	 * @param boolean $xTime00 en True devuelve con 00:00 y con false en 23:59
	 * @return string
	 */
	public static function formatFechaAyerSql($xTime00 = true)
	{
		$date = date("Y-m-d");
		$fecha = date("Y-m-d", strtotime("-1 day", strtotime($date)));
		
		if ($xTime00)
			$fecha .= " 00:00";
		else
			$fecha .= " 23:59";
		return $fecha;
	}


	/**
	 * retorna la fecha actual en string, comenzando con el anio
	 * Se usa para el path de los archivos que se suben
	 */
	public static function formatFechaPath()
	{
		$hoy = getdate(time());
		$mes = (int)$hoy["mon"];
		
		$fecha = "";
		$fecha .= $hoy["year"];
		
		if ($mes < 5)
			$fecha .= "1";
		elseif ($mes < 9)
			$fecha .= "2";
		else
			$fecha .= "3";

		return $fecha;		
	}
	
	
	public static function formatFechaFromRsToRs($xvalor, $xtime = false, $xdate = true)
	{
		$hoy = getdate(toTimestamp($xvalor));
		$fecha = "'";
		if ($xdate)
		$fecha .= $hoy["year"] . "-" . str_pad($hoy["mon"], 2, "0", STR_PAD_LEFT) . "-" . str_pad($hoy["mday"], 2, "0", STR_PAD_LEFT);
		if ($xtime)
		$fecha .= " " . $hoy["hours"] . ":" . str_pad($hoy["minutes"], 2, "0", STR_PAD_LEFT);
		$fecha .= "'";
		return $fecha;
	}
	
	
	/**
	 * Retorna la fecha actual en string, comenzando en format AAAA-MM-DD
	 * Se usa para el backup y para evitar que los archivos .JS sean guardados en la cache del explorador
	 */
	public static function formatFecha3($xtime = true)
	{
		$hoy = getdate(time());
		
		$fecha = "";
		$fecha .= $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"];
		if ($xtime)
		$fecha .= "_" . $hoy["hours"] . "" . $hoy["minutes"];
		return $fecha;		
	}

	
	/**
	 * De un valor en un campo del RS, retorna formateado en español, formato DD/MM/AAAA
	 * con la Hora en formato HH:MM si es requerido
	 */
	public static function formatFechaFromRs($xvalor, $xtime = false, $xdate = true)
	{
		if (esVacio($xvalor))
			return "";
		$hoy = getdate(toTimestamp($xvalor));
		$fecha = "";
		if ($xdate)
			$fecha .= $hoy["mday"] . "/" . $hoy["mon"] . "/" . $hoy["year"];
		if ($xtime)
			$fecha .= " " . $hoy["hours"] . ":" . str_pad($hoy["minutes"], 2, "0", STR_PAD_LEFT);;
		return $fecha;		
	}


//FIN CLASE
}



function toTimestamp($str)
{
	if ((strcmp($str, "") == 0) || (strcmp($str, "null") == 0))
		return time();

	$fechahora = explode(" ", $str);
	if (count($fechahora) == 0)
		return mktime (0, 0, 0, 0, 0, 0);
		
	$fecha = explode("-", $fechahora[0]);	
	if (count($fechahora) == 1)
	{
		return mktime (0, 0, 0, $fecha[1], $fecha[2], $fecha[0]);
	}
	else
	{
		$hora = explode(":", $fechahora[1]);
		$seg = 0;
		if (isset($hora[2]))
			$seg = $hora[2];
		return mktime ($hora[0], $hora[1], $seg, $fecha[1], $fecha[2], $fecha[0]);
	}
}


function esFechaPasada($xvalor)
{
	$fecha = getdate(toTimestamp($xvalor));
	$hoy = getdate();
	
	if ((int)$fecha["year"] < (int)$hoy["year"])
		return true;
	if (((int)$fecha["year"] == (int)$hoy["year"]) && ((int)$fecha["mon"] < (int)$hoy["mon"]))
		return true;
	if (((int)$fecha["year"] == (int)$hoy["year"]) && ((int)$fecha["mon"] == (int)$hoy["mon"]) && ((int)$fecha["mday"] <= (int)$hoy["mday"]))
		return true;

	return false;
}

