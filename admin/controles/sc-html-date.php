<?php


/*
 control fecha v2
 */
class HtmlTime
{
	var $id = "";
	var $requerido = false;
	var $readonly = false;
	var $isnull = false;
	var $showDelete = true;
	var $hora = "0";
	var $minutos = "0";
	
	
	function __construct($xid, $xhora = "", $xminutos = "")
	{
		$this->id = $xid;
		if (!esVacio($xhora))
		{
			$this->hora = $xhora;
			$this->minutos = $xminutos;
		}
	}
	
	function setRequerido()
	{
		$this->requerido = true;
	}
	
	function toHtml()
	{
		$result = "";
		$result .= " <input name=\"" . $this->id . "_hora\" type=\"text\" id=\"" . $this->id . "_hora\" size=\"2\" maxlength=\"2\"  value=\"" . $this->hora . "\" title=\"Hora [0 - 23]\" autocomplete=\"off\" onclick=\"sc3SelectAll('" . $this->id . "_hora')\" />";
		$result .= ":<input name=\"" . $this->id . "_minutos\" type=\"text\" id=\"" . $this->id . "_minutos\" size=\"2\" maxlength=\"2\"  value=\"" . $this->minutos . "\" title=\"Minuto [0 - 59]\" autocomplete=\"off\" onclick=\"sc3SelectAll('" . $this->id . "_minutos')\" />";
		
		return $result;		
	}
	
}


/**
* Control fecha (3 campos separados en DD / MM / AAAA)
*/
class HtmlDate
{
	var $id = "";
	var $valueDate;
	var $requerido = false;
	var $readonly = false;
	var $onlyDate = false;
	var $isnull = false;
	var $showDelete = false;
	var $mOnKeyUpFireJs = array();

	function __construct($xid, $xvalue = "")
	{
		$this->id = $xid;
		if (sonIguales($xvalue, "null"))
		{
			$this->isnull = true;
			$this->valueDate = array(0, 0, 0, 0, 0, 0);
		}
		else
			$this->valueDate = $this->strToDate($xvalue);
		$this->setOnlyDate(false);
	}

	function setRequerido()
	{
		$this->requerido = true;
	}

	function isRequerido()
	{
		return $this->requerido;
	}

	function setOnlyDate($xonlyDate = true)
	{
		$this->onlyDate = $xonlyDate;
	}
	
	function dontShowDelete()
	{
		$this->showDelete = false;
	}

	//toma el valor del string dado
	function strToDate($xstr = "")
	{
		if (sonIguales($xstr, ""))
			$day = Sc3FechaUtils::now();
		else
			$day = getdate(toTimestamp($xstr));
		return $day;
	}

	function setReadOnly($xreadonly = true)
	{
		$this->readonly = $xreadonly;
	}

	/*
	 retorna la fecha actual en string para mostrar
	*/
	function formatFecha($xfecha, $xtime = FALSE)
	{
		$fecha = "";
		if ($this->isnull)
			return $fecha;
			
		$fecha .= $xfecha["mday"] . "/" . $xfecha["mon"] . "/" . $xfecha["year"] ;
		if ($xtime)
			$fecha .= " " . $xfecha["hours"] . ":" . $xfecha["minutes"];
		return $fecha;
	}


	/**
	 * Toma el valor del request si es que el a�o es > 0
	 */
	function valueFromRequest()
	{
		$nombre = $this->id;
		$fecha = "";
		if (RequestInt($nombre . "_a") > 0)
		{
			$fecha .= RequestInt($nombre . "_a") . "-";
			$fecha .= RequestInt($nombre . "_m") . "-";
			$fecha .= RequestInt($nombre . "_d") . " ";
			$fecha .= RequestInt($nombre . "_h") . ":";
			$fecha .= RequestInt($nombre . "_n") . ":";
			$fecha .= RequestInt($nombre . "_s");
				
			$this->valueDate = $this->strToDate($fecha);
		}
		
		//por si viene en formato DD/MM/AAAA
		if (!esVacio(Request($nombre)))
		{
			$aFecha = explode("/", Request($nombre));
			if (count($aFecha) == 3)
			{
				$this->valueDate = $this->strToDate($aFecha[2] . "-" . $aFecha[1] . "-" . $aFecha[0]);
			}
		}
	}
	

	function setAnioAnterior()
	{
		$fecha = (date("Y") - 1) . "-";
		$fecha .= date("m") . "-";
		$fecha .= date("d");
		$fecha .= " 0:0:0";

		$this->valueDate = $this->strToDate($fecha);
	}

	function setMesAnteriorInicio()
	{
		$mes = date("m");
		$anio = date("Y");
		if ($mes == 1)
		{
			$mes = 12;
			$anio--;
		}
		else 
			$mes--;
		
		$fecha = $anio . "-";
		$fecha .= $mes . "-1";
		$fecha .= " 0:0:0";
	
		$this->valueDate = $this->strToDate($fecha);
	}
	
	
	function setInicioMes()
	{
		$mes = date("m");
		$anio = date("Y");		
		$fecha = $anio . "-";
		$fecha .= $mes . "-1";
		$fecha .= " 0:0:0";
	
		$this->valueDate = $this->strToDate($fecha);
	}
	

	function setMeses3Anteriores()
	{
		$this->valueDate = $this->strToDate( Sc3FechaUtils::fechaHace3Meses());
	}
	
	
	/**
	 * Pone fecha en 1-ene de este a�o
	 */
	function setInicioAnio()
	{
		$fecha = date("Y") . "-1-1";
		$fecha .= " 0:0:0";
		
		$this->valueDate = $this->strToDate($fecha);
	}

	
	/**
	 * Pone fecha en 1-ene de este a�o
	 */
	function setInicioAnioAnterior()
	{
		$fecha = (date("Y") - 1) . "-1-1";
		$fecha .= " 0:0:0";
	
		$this->valueDate = $this->strToDate($fecha);
	}
	
	/**
	 * Principios de mes
	*/
	function setFechaDesde()
	{
		$fecha = date("Y") . "-";
		$fecha .= date("m") . "-";
		$fecha .= "1";
		$fecha .= " 0:0:0";

		$this->valueDate = $this->strToDate($fecha);
	}

	function getCamposHora0()
	{
		$fechas = campoEscondido($this->id . "_h", "0");
		$fechas .= campoEscondido($this->id . "_n", "0");
		$fechas .= campoEscondido($this->id . "_s", "0");
		return $fechas;
	}

	//obtiene un combo con los dias, meses y a�os, en el dia seleccionado
	function getCamposHiden($xName, $xDay)
	{
		$fechas = "";
		if ($this->onlyDate)
		{
			$xDay["hours"] = "0";
			$xDay["minutes"] = "0";
			$xDay["seconds"] = "0";
		}

		$fechas .= campoEscondido($xName . "_a", $xDay["year"]);
		$fechas .= campoEscondido($xName . "_m", $xDay["mon"]);
		$fechas .= campoEscondido($xName . "_d", $xDay["mday"]);
		$fechas .= campoEscondido($xName . "_h", $xDay["hours"]);
		$fechas .= campoEscondido($xName . "_n", $xDay["minutes"]);
		$fechas .= campoEscondido($xName . "_s", $xDay["seconds"]);
		return $fechas;
	}

	/*
	 retorna el valor del request para ser usado en el SQL
	*/
	function getRequestToSql()
	{
		$nombre = $this->id;

		//analiza formato nuevo 2020-04-01
		if (RequestInt($nombre . "_a") == 0)
		{
			$fechaReq = Request($nombre);
			if (esVacio($fechaReq))
				return "'2015-05-05 00:00'";

			$aFecha = explode("-", $fechaReq);
			if (count($aFecha) == 3)
				return comillasSql($fechaReq);
		}

		$result = "'";
		$result .= RequestInt($nombre . "_a") . "-";
		$result .= str_pad(RequestInt($nombre . "_m"), 2, "0", STR_PAD_LEFT) . "-";
		$result .= str_pad(RequestInt($nombre . "_d"), 2, "0", STR_PAD_LEFT) . " ";
		
		$result .= RequestInt($nombre . "_h") . ":";
		$result .= RequestInt($nombre . "_n") . ":";
		$result .= RequestInt($nombre . "_s") . "'";
		return $result;
	}

	function getRequestToSql2359()
	{
		$nombre = $this->id;
		
		if (RequestInt($nombre . "_a") == 0)
		{
			$fechaReq = Request($nombre);
			if (esVacio($fechaReq))
				return "'2025-05-05 00:00'";

			$aFecha = explode("-", $fechaReq);
			if (count($aFecha) == 3)
				return comillasSql($fechaReq . " 23:59");
		}
		
		$result = "'";
		$result .= RequestInt($nombre . "_a") . "-";
		$result .= str_pad(RequestInt($nombre . "_m"), 2, "0", STR_PAD_LEFT) . "-";
		$result .= str_pad(RequestInt($nombre . "_d"), 2, "0", STR_PAD_LEFT) . " 23:59'";
		return $result;
	}

	function getRequestToDateFormat()
	{
		$nombre = $this->id;
	
		$result = "";
		$result .= RequestInt($nombre . "_a") . "-";
		$result .= str_pad(RequestInt($nombre . "_m"), 2, "0", STR_PAD_LEFT) . "-";
		$result .= str_pad(RequestInt($nombre . "_d"), 2, "0", STR_PAD_LEFT);
		return $result;
	}
	
	function getRequestToSql0000()
	{
		$nombre = $this->id;
		//analiza si viene en formato fecha_a fecha_m y fecha_d
		if (RequestInt($nombre . "_a") == 0)
		{
			$fechaReq = Request($nombre);
			if (esVacio($fechaReq))
				return "'2015-05-05 00:00'";

			$aFecha = explode("-", $fechaReq);
			if (count($aFecha) == 3)
				return comillasSql($fechaReq);
		}

		$result = "'";
		$result .= RequestInt($nombre . "_a") . "-";
		$result .= str_pad(RequestInt($nombre . "_m"), 2, "0", STR_PAD_LEFT) . "-";
		$result .= str_pad(RequestInt($nombre . "_d"), 2, "0", STR_PAD_LEFT) . " 00:00'";
		
		return $result;
	}

	function getAnio()
	{
		$nombre = $this->id;
		return RequestInt($nombre . "_a");
	}

	function getMes()
	{
		$nombre = $this->id;
		return RequestInt($nombre . "_m");
	}

	function getDia()
	{
		$nombre = $this->id;
		return RequestInt($nombre . "_d");
	}

	function toPdf()
	{
		$dia = $this->valueDate;
		return $this->formatFecha($dia);
	}

	function setOnKeyUpFireJs($xquery)
	{
		$this->mOnKeyUpFireJs['query'] = $xquery;
	}
	
	//traduce a html
	function toHtml()
	{
		$result = "\n";
		$nombre = $this->id;
		$dia = $this->valueDate;
		if ($this->readonly)
		{
			$result .= Sc3FechaUtils::formatFecha($dia, !$this->onlyDate);
			$result .= $this->getCamposHiden($nombre, $this->valueDate);
		}
		else
		{
			if (!$this->isnull)
			{
				$ano = $this->valueDate["year"];
				$mes = $this->valueDate["mon"];
				$diam = $this->valueDate["mday"];
				$hora = $this->valueDate["hours"];
				$minutos = $this->valueDate["minutes"];
			}
			else
			{
				$ano = "";
				$mes = "";
				$diam = "";
				$hora = "";
				$minutos = "";
			}
				
			$calName = "cal" . $this->id;
			$calId = $this->id;
			$result .= "\n	<SCRIPT LANGUAGE=\"JavaScript\" ID=\"js" . $this->id . "\">";
			$result .= "\n	var $calName = new CalendarPopup();";
			$result .= "\n	$calName.setMonthNames('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');";
			$result .= "\n	$calName.setDayHeaders('D','L','M','M','J','V','S');";
			$result .= "\n	$calName.setTodayText('Hoy');";
			$result .= "\n  $calName.setReturnFunction(\"setMultipleValues" . $this->id . "\"); ";
			$result .= "\n  	function setMultipleValues" . $this->id . "(y,m,d) { ";
			$result .= "\n  	     document.getElementById('" . $this->id . "_a').value = y; ";
			$result .= "\n  	     document.getElementById('" . $this->id . "_m').value = m; ";
			$result .= "\n  	     document.getElementById('" . $this->id . "_d').value = d; ";
			$result .= "\n  	        }";
			$result .= "\n	</SCRIPT>";
			
	
			$class = "";
			if ($this->requerido)
				$class = "requerido";
			
			$result .= "\n<input name=\"" . $this->id . "_d\" type=\"text\" id=\"" . $this->id . "_d\" size=\"2\" maxlength=\"2\"  value=\"" . $diam . "\" title=\"Dia [1 - 31]\" autocomplete=\"off\" ";
			if (isset($this->mOnKeyUpFireJs['query']))
			{
				$result .= " onkeyup=\"sc3firejs('" . $this->mOnKeyUpFireJs['query'] . "', '" . $this->id . "_d', 'onkeyup', event)\"";
			}
			$result .= " onclick=\"sc3SelectAll('" . $this->id . "_d')\" class=\"$class\" />";
			
			
			$result .= "/<input name=\"" . $this->id . "_m\" type=\"text\" id=\"" . $this->id . "_m\" size=\"2\" maxlength=\"2\"  value=\"" . $mes . "\" title=\"Mes [1 - 12]\" autocomplete=\"off\" ";
			if (isset($this->mOnKeyUpFireJs['query']))
			{
				$result .= " onkeyup=\"sc3firejs('" . $this->mOnKeyUpFireJs['query'] . "', '" . $this->id . "_m', 'onkeyup', event)\"";
			}
			$result .= " onclick=\"sc3SelectAll('" . $this->id . "_m')\" class=\"$class\" />";
			
			
			$result .= "/<input name=\"" . $this->id . "_a\" type=\"text\" id=\"" . $this->id . "_a\" size=\"4\" maxlength=\"4\"  value=\"" . $ano . "\" title=\"A&ntilde;o [2000 - 2020]\" autocomplete=\"off\" ";
			if (isset($this->mOnKeyUpFireJs['query']))
			{
				$result .= " onkeyup=\"sc3firejs('" . $this->mOnKeyUpFireJs['query'] . "', '" . $this->id . "_a', 'onkeyup', event)\"";
			}
			$result .= " onclick=\"sc3SelectAll('" . $this->id . "_a')\" class=\"$class\" />";

			
			if (!$this->onlyDate)
			{
				$result .= " <input name=\"" . $this->id . "_h\" type=\"text\" id=\"" . $this->id . "_h\" size=\"2\" maxlength=\"2\"  value=\"" . $hora . "\" title=\"Hora [0 - 23]\" autocomplete=\"off\" />";
				$result .= ":<input name=\"" . $this->id . "_n\" type=\"text\" id=\"" . $this->id . "_n\" size=\"2\" maxlength=\"2\"  value=\"" . $minutos . "\" title=\"Minuto [0 - 59]\" autocomplete=\"off\" />";
			}
			else
			{
				$result .= $this->getCamposHora0();
			}
				
			$result .= "\n <a href=\"#\" onClick=\"$calName.showCalendar('anchor$calName'); return false;\"
								name=\"anchor$calName\" id=\"anchor$calName\" tabindex=\"-1\" title=\"Seleccionar fecha\">
							<i class=\"fa fa-calendar fa-fw fa-lg boton-fa-control\"></i>
						</a>";

			if ($this->showDelete)
			{
				$result .= "\n <a href=\"#\" onClick=\"setMultipleValues" . $this->id . "('','',''); return false;\" tabindex=\"-1\" title=\"Vaciar fecha\">
								<i class=\"fa fa-calendar-o fa-fw fa-lg boton-fa-control\"></i>
							</a>";
			}
				
			if ($this->isRequerido())
				$result .= " ";
			$result .= "";
		}
		$result .= "\n";

		return $result;
	}
}


/**
 * Rango de fechas con combo de fechas usuales.
 * Mes pasado, mes actual, ultimo año, etc
 */
class HtmlDateRange
{
	var $id = "";
	var $value;
	var $requerido = false;
	var $fechaDesde = "";
	var $fechaHasta = "";

	const MES_ACTUAL = "mes_actual";
	const ULTIMO_MES = "ultimo_mes";
	const ULTIMOS_3_MESES = "ultimos_tres_meses";
	const PROXIMO_MES = "proximo_mes";
	const ULTIMO_ANIO = "ultimo_anio";
	const MES_PASADO = "mes_pasado";
	const ANIO_PASADO = "anio_pasado";
	const AYER = "ayer";
	const HOY = "hoy";
	const ULTIMOS_6_MESES = "ultimos_6_meses";
	
	function __construct($xid, $xvalue = "")
	{
		$this->id = $xid;
		$this->value = $xvalue;
		$this->valueFromRequest();
	}

	function strToDate($xstr = "")
	{
		if (sonIguales($xstr, ""))
			$day = Sc3FechaUtils::now();
		else
			$day = getdate(toTimestamp($xstr));
		return $day;
	}

	function formatFecha($xfecha)
	{
		$fecha = "";
		if (esvacio($xfecha))
			return "";
			
		$fecha = getdate(toTimestamp($xfecha));
		return $fecha["mday"] . "/" . $fecha["mon"] . "/" . $fecha["year"];
	}

	function toPdf()
	{
		$dia = $this->fechaDesde;
		$dia2 = $this->fechaHasta;
		return $this->formatFecha($dia) . " al " . $this->formatFecha($dia2);
	}

	function valueFromRequest()
	{
		$nombre = $this->id . "_desde";
		$fecha = "";
		if (Request($nombre) != "")
		{
			$fecha = Request($nombre);
				
			$this->fechaDesde = $fecha;
			$this->value = "otro";
		}

		$nombre = $this->id . "_hasta";
		$fecha = "";
		if (Request($nombre) != "")
		{
			$fecha = Request($nombre);
				
			$this->fechaHasta = $fecha;
			$this->value = "otro";
		}

		$nombre = $this->id . "_combo";
		$valCombo = Request($nombre);
		if (!esVacio($valCombo))
			$this->value = $valCombo;
	}


	function toHtml()
	{
		$combo = new HtmlCombo($this->id . "_combo", $this->value);
		$combo->setClass("rango_fechas");
		$combo->add("mes_actual", "Mes actual");
		$combo->add("ultimo_mes", "Mes &uacute;ltimo");
		$combo->add("mes_pasado", "Mes pasado");
		$combo->add("proximo_mes", "Mes pr&oacute;ximo");
		$combo->add("ultimos_tres_meses", "Ultimos 3 meses");
		$combo->add("ultimos_6_meses", "Ultimos 6 meses");
		$combo->add("ultimo_anio", "A&ntilde;o &uacute;ltimo");
		$combo->add("anio_pasado", "A&ntilde;o pasado");
		$combo->add("ultima_decada", "Todo");
		$combo->add("ayer", "Ayer");
		$combo->add("hoy", "Hoy");
		$combo->add("otro", "Otro");

		$combo->onchange("cambiarIntervalos('" . $this->id . "_desde','" . $this->id . "_hasta','" . $this->id . "_combo')");

		$result = "<input class=\"fecha\" value=\"" . $this->fechaDesde . "\" required=\"required\" 
						type=\"date\" id=\"" . $this->id . "_desde\" name=\"" . $this->id . "_desde\" 
						onchange=\"comparaFechaDesde('" . $this->id . "_desde', '" . $this->id . "_hasta', '" . $this->id . "_combo')\" >";
		$result .= "<input class=\"fecha\" value=\"" . $this->fechaHasta . "\" required=\"required\" 
						type=\"date\" id=\"" . $this->id . "_hasta\" name=\"" . $this->id . "_hasta\" 
						onchange=\"comparaFechaHasta('" . $this->id . "_desde','" . $this->id . "_hasta', '" . $this->id . "_combo')\" >"; 
		$result .= $combo->toHtml();
		$result .= "<script>
						 document.addEventListener('load', cambiarIntervalos('" . $this->id . "_desde', '" . $this->id . "_hasta','" . $this->id . "_combo'));
						 
						 </script>";
		return $result;
	}
}


/**
* Control fecha (unico campo)
*/
class HtmlDate2
{
	var $id = "";
	var $valueDate;
	var $requerido = false;
	var $readonly = false;
	var $isnull = false;
	var $showDelete = false;
	var $mOnKeyUpFireJs = array();
	
	const HOY = "hoy";
	
	
	// para evitar malos tipeos (seteado en el constructor)
	var $mMinDate = "2010-01-01";
	var $mMaxDate = "2030-12-31";

	function __construct($xid, $xvalue = "")
	{
		$this->id = $xid;
		if (sonIguales($xvalue, "null"))
		{
			$this->isnull = true;
			$this->valueDate = "";
		}
		else
			$this->valueDate = $xvalue;

		if (esVacio($xvalue))	
		{
			$this->valueDate = date("Y-m-d");
		}

		//rango valido inicial: +/- 10 años
		$this->mMinDate = (date("Y") - 10) . "-01-01";
		$this->mMaxDate = (date("Y") + 10) . "-12-31";
	}

	function setFechaFinAnio()
	{
		$this->valueDate = date("Y") . "-12-31";
	}

	function setFechaLejanaAtras()
	{
		$this->valueDate = "2010-01-01";
	}

	function setFechaLejanaAdelante()
	{
		$this->valueDate = "2050-01-01";
	}

	/**
	 * Si hay valor en el request, gana
	 */
	function valueFromRequest()
	{
		$nombre = $this->id;
		$fecha = "";
		$valor = Request($nombre);
		if (!esVacio($valor))
		{
			$this->valueDate = $valor;
		}
	}


	function setFechaInicioAnio()
	{
		$this->valueDate = date("Y") . "-01-01";
	}

	function toPdf()
	{
		return Sc3FechaUtils::fechaSqlAFechaLegible($this->valueDate);
	}

	function toHtml()
	{
		$req = "";
		if ($this->requerido)
			$req = "required=\"required\"";

		$result = "<input class=\"fecha\" value=\"" . $this->valueDate . "\" $req 
						type=\"date\" id=\"" . $this->id . "\" name=\"" . $this->id . "\" 
						onchange=\"\" 
						min=\"" . $this->mMinDate . "\" max=\"" . $this->mMaxDate . "\">";

		return $result;
	}
}


?>