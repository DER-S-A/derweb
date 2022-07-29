<?php

//para las grillas
$ANCHO_FECHA = 65;
$ANCHO_COMPROBANTE = 170;
$ANCHO_IMPORTE = 120;



//retorna un campo escondido de un formulario
function campoEscondido($xnombre, $xvalor)
{
	return "<input type='hidden' name='" . $xnombre . "' value='" . $xvalor . "'>";
}

//retorna un campo oculta, recuperando el valor del request
function requestToForm($xnombre)
{
	return campoEscondido($xnombre, Request($xnombre));
}


//obtiene un combo con los dias, meses y años, en el dia seleccionado
function getComboFecha($xName, $xDay)
{
	$fechas = getDias($xName . "_d", $xDay["mday"]) . " " . getMeses($xName . "_m", $xDay["mon"]) . " " . getAnos($xName . "_a", $xDay["year"]);
	if (((strcmp($xDay["hours"], "0") != 0) ||
		(strcmp($xDay["minutes"], "0") != 0)))
		$fechas .= " " . getHoursCombo($xName . "_h", $xDay["hours"]) . ":"  . getMinutesCombo($xName . "_n", $xDay["minutes"]) . campoEscondido($xName . "_s", $xDay["seconds"]);
	else
		$fechas .= " " . campoEscondido($xName . "_h", $xDay["hours"]) . campoEscondido($xName . "_n", $xDay["minutes"]) . campoEscondido($xName . "_s", $xDay["seconds"]);

	return $fechas;
}

function href($xvisible, $xlink, $xtarget = "", $xid = "", $xclass = "", $xdownload = false)
{
	$download = "";
	if ($xdownload)
		$download = " download ";
	if (strcmp($xtarget, "") == 0)
		return "<a href=\"" . $xlink . "\" id=\"$xid\" class=\"" . $xclass . "\" $download >" . $xvisible . "</a>";
	else
		return "<a href=\"" . $xlink . "\" id=\"$xid\" target=\"" . $xtarget . "\" class=\"" . $xclass . "\" $download>" . $xvisible . "</a>";
}


function img($ximg, $xalt, $xwidth = "", $xid = "", $xclass = "")
{
	return "<img id=\"" . $xid . "\" src=\"" . $ximg . "\" class=\"" . $xclass . "\" border=\"0\" alt=\"" . $xalt . "\" title=\"" . $xalt . "\" width=\"" . $xwidth . "\" >";
}

/**
 * Icono FontAwsame
 * @param string $xicon
 * @param string $xsize : opciones: fa-lg / fa-2x / fa-3x / fa-4x ....
 * @return string
 */
function imgFa($xicon, $xsize = "fa-lg", $xcolor = "", $xalt = "", $xId = "")
{
	return "<i title=\"$xalt\" id=\"$xId\" class=\"fa $xcolor $xicon $xsize  fa-fw\"></i>";
}

function span($xContent, $xclass = "", $xid = "")
{
	return "<span class=\"$xclass\" id=\"$xid\">$xContent</span>";
}

function div($xContent, $xclass = "", $xId = "", $xDraggeable = false, $xonDblClick = "")
{
	//TODO: rehacer div
	$div = "<div id=\"$xId\" class=\"$xclass\"";

	if ($xDraggeable)
		$div .= " ondragstart=\"dragStart(event)\" draggable=\"true\"";

	if (!esVacio($xonDblClick)) {
		$div .= " ondblclick=\"$xonDblClick\"";
	}

	$div .= ">$xContent</div>";

	return $div;
}

function esIconFontAwesome($xicon)
{
	if (startsWith($xicon, "fa-"))
		return true;
	return false;
}

/**
 * Retorna un SPAN de class boolean si o boolean no
 */
function mostrarBooleano($xValor)
{
	if (($xValor == 1) || (sonIguales($xValor, "Si")))
		return "<span class=\"booleano si\">Si</span>";
	else
		return "<span class=\"booleano no\">No</span>";
}


function espacio()
{
	return "&nbsp;";
}

function imgSmall($ximg, $xalt)
{
	return "<img src='" . $ximg . "' border='0' alt='" . $xalt . "'  title='" . $xalt . "' width='80'>";
}


function tdc($xcontenido, $xancho)
{
	return "<td align=center valign=center width='" . $xancho . "'>" . $xcontenido . "</td>";
}


function td($xcontenido, $xancho)
{
	return "\n<td width='" . $xancho . "'>" . $xcontenido . "</td>";
}


function getDias($xName, $xDia)
{
	$fecha = getdate();
	$xRes = '<select name="' . $xName . '">';
	for ($i = 1; $i <= 31; $i++) {
		if ((($xDia == -1) && ($i == $fecha["mday"])) || ($i == $xDia))
			$xRes .= _option($i, $i, 1);
		else
			$xRes .= _option($i, $i, 0);
	}
	$xRes .= '</select>';
	return $xRes;
}


function getMeses($xName, $xMes)
{
	$xRes = '<select name="' . $xName . '">';
	for ($i = 1; $i <= 12; $i++) {
		if (($i) == $xMes)
			$xRes .= _option($i, Sc3FechaUtils::mesAStr($i), 1);
		else
			$xRes .= _option($i, Sc3FechaUtils::mesAStr($i), 0);
	}
	$xRes .= '</select>';
	return $xRes;
}


function getAnos($xName, $xAno)
{
	$fecha = getdate();
	$xRes = '<select name="' . $xName . '">';
	for ($i = 2000; $i <= 2015; $i++) {
		if ((($xAno == -1) && ($i == $fecha["year"])) || ($xAno == $i))
			$xRes .= _option($i, $i, 1);
		else
			$xRes .= _option($i, $i, 0);
	}
	$xRes .= '</select>';
	return $xRes;
}


function getHoursCombo($xName, $xHour)
{
	$fecha = getdate();
	$xRes = '<select name="' . $xName . '">';
	for ($i = 0; $i <= 23; $i++) {
		if ((($xHour == -1) && ($i == $fecha["hours"])) || ($xHour == $i))
			$xRes .= _option($i, $i, 1);
		else
			$xRes .= _option($i, $i, 0);
	}
	$xRes .= '</select>';
	return $xRes;
}


function getMinutesCombo($xName, $xMinute)
{
	$fecha = getdate();
	$xRes = '<select name="' . $xName . '">';
	for ($i = 0; $i <= 59; $i++) {
		if ((($xMinute == -1) && ($i == $fecha["minutes"])) || ($xMinute == $i))
			$xRes .= _option($i, $i, 1);
		else
			$xRes .= _option($i, $i, 0);
	}
	$xRes .= '</select>';
	return $xRes;
}


function getSecondsCombo($xName, $xSecond)
{
	$fecha = getdate();
	$xRes = '<select name="' . $xName . '">';
	for ($i = 0; $i <= 59; $i++) {
		if ((($xSecond == -1) && ($i == $fecha["seconds"])) || ($xSecond == $i))
			$xRes .= _option($i, $i, 1);
		else
			$xRes .= _option($i, $i, 0);
	}
	$xRes .= '</select>';
	return $xRes;
}


function _option($xValue, $xVisible, $xSelected)
{
	if ($xSelected == 1)
		return "\n<option value=\"" . $xValue . "\" selected=\"selected\">" . $xVisible . "</option>";
	else
		return "\n<option value=\"" . $xValue . "\">" . $xVisible . "</option>";
}


function getComboResultSet($xrsSet, $xNombre, $xCampoID, $xCampoVisible, $xselected)
{
	$xRes = '<select name="' . $xNombre . '" id="' . $xNombre . '">';
	$xRes = '' . $xRes;
	while (!$xrsSet->EOF()) {
		if ($xrsSet->getValue($xCampoID) == $xselected)
			$xRes .= _option($xrsSet->getValue($xCampoID), $xrsSet->getValue($xCampoVisible), 1);
		else
			$xRes .= _option($xrsSet->getValue($xCampoID), $xrsSet->getValue($xCampoVisible), 0);
		$xrsSet->Next();
	}
	$xRes .= '</select>';
	return $xRes;
}


function getComboResultSetWithNnull($xrsSet, $xNombre, $xCampoID, $xCampoVisible, $xselected)
{
	$xRes = '<select name="' . $xNombre . '" id="' . $xNombre . '">';
	$xRes = '' . $xRes;
	if (strcmp($xselected, "null") == 0)
		$xRes .= _option("null", " ", 1);
	else
		$xRes .= _option("null", " ", 0);
	while (!$xrsSet->EOF()) {
		if (strcmp($xrsSet->getValue(0), $xselected) == 0)
			$xRes .= _option($xrsSet->getValue(0), $xrsSet->getValue(1), 1);
		else
			$xRes .= _option($xrsSet->getValue(0), $xrsSet->getValue(1), 0);
		$xrsSet->Next();
	}
	$xRes .= '</select>';

	return $xRes;
}

function linkCerrar($anterior = 1, $xstackname = "", $xclass = "")
{
	if (esExcel())
		return "";

	if (esVacio($xstackname))
		$xstackname = Request("stackname");

	//w3-display-topright 
	if (esVacio($xclass))
		$xclass = "boton-fa-sup";

	$str = "<a id=\"linkcerrar\" class=\"$xclass\" href=\"hole.php?anterior=" . $anterior . "&stackname=$xstackname\" title=\"ESC\">
				<i class=\"fa fa-times fa-lg\"></i>
			</a>";

	return $str;
}


function linkImprimir($xclass = "")
{
	if (esExcel())
		return "";

	$str = "<a id=\"linkprint\" href=\"javascript:window.print()\" accesskey=\"i\" class=\"w3-hide-small $xclass\" >
                <i class=\"fa fa-print fa-lg\"></i>
            </a>";
	return $str;
}

/*
 Comienza un fieldset con el legend dado
*/
function startFieldset($xtitulo)
{
	$str = "\n<fieldset>";
	$str .= "<legend>" . $xtitulo . "</legend>\n";
	$str .= "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"2\">";
	return $str;
}

function endFieldset()
{
	return "\n</table></fieldset>\n";
}

/**
 * Retorna el texto entre <b> y </b>
 * @param string $xtxt
 * @return string
 */
function htmlBold($xtxt)
{
	return "<b>" . $xtxt . "</b>";
}

function htmlfont($xtxt, $xfontSize)
{
	return "<font size='$xfontSize'>" . $xtxt . "</font>";
}

function htmlError($xerror)
{
	return "<span class='td_error'>" . $xerror . "</span><br>";
}



class HtmlPeriodo
{
	var $nombreAnio = "anio";
	var $nombreMes = "mes";
	var $valueAnio = "";
	var $valueMes = "";


	function __construct($xanio, $xmes)
	{
		$this->nombreAnio = $xanio;
		$this->nombreMes = $xmes;
	}

	function setPeriodoActual()
	{
		$day = Sc3FechaUtils::now();
		$this->valueAnio = $day["year"];
		$this->valueMes = $day["mon"];
	}

	function setPeriodoAnterior()
	{
		$day = Sc3FechaUtils::now();
		$this->valueAnio = $day["year"];
		$this->valueMes = $day["mon"] - 1;
		if ($this->valueMes == 0) {
			$this->valueAnio = $day["year"] - 1;
			$this->valueMes = 1;
		}
	}

	function toHtml()
	{
		$str = "";

		$txtMes = new HtmlInputText($this->nombreMes, $this->valueMes);
		$txtMes->setTypeInt();
		$txtMes->setSize(2);
		$txtMes->setMaxSize(2);
		$str .= $txtMes->toHtml();

		$txtAnio = new HtmlInputText($this->nombreAnio, $this->valueAnio);
		$txtAnio->setTypeInt();
		$txtAnio->setSize(4);
		$txtAnio->setMaxSize(4);
		$str .= $txtAnio->toHtml();

		return $str;
	}
}


class HtmlHidden
{
	var $id = "";
	var $value = "";

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}

	function valueFromRequest()
	{
		$this->value = Request($this->id);
	}

	function toHtml()
	{
		$result = "";
		$result .= "<input type=\"hidden\"";
		$result .= " name=\"" . $this->id . "\"";
		$result .= " id=\"" . $this->id . "\"";
		$result .= " value=\"" . $this->value . "\"";
		$result .= " />";
		return $result;
	}
}


class HtmlImporte
{
	var $mCboMonedas;
	var $mTxtMonto;

	function __construct($xidmoneda, $xidmonto)
	{
		$this->mCboMonedas = new HtmlCombo($xidmoneda, "1");

		$this->mTxtMonto = new HtmlInputText($xidmonto, "0.0");
		$this->mTxtMonto->setTypeFloat();
	}

	function setDefault($xmonto)
	{
		$this->mTxtMonto->setDefault($xmonto);
	}

	function setMoneda($xidmoneda, $xreadonly = false)
	{
		$this->mCboMonedas->setValue($xidmoneda);
		$this->mCboMonedas->setReadOnly($xreadonly);
	}

	function cargarMonedas($xrs)
	{
		$this->mCboMonedas->cargarRs($xrs, "id", "moneda");
	}

	function soloPesos()
	{
		$this->mCboMonedas->setValue(1);
		$this->mCboMonedas->setReadOnly(true);
	}

	function setReadOnly()
	{
		$this->mCboMonedas->setReadOnly(true);
		$this->mTxtMonto->setReadOnly(true);
	}

	function getTxtMonto()
	{
		return $this->mTxtMonto;
	}

	function getCboMonedas()
	{
		return $this->mCboMonedas;
	}

	function setRequerido()
	{
		$this->mTxtMonto->setRequerido();
	}

	function toHtml()
	{
		$str = $this->mCboMonedas->toHtml();
		$str .= $this->mTxtMonto->toHtml();
		return $str;
	}
}




class HtmlUrl
{
	var $url = "";
	var $parametros = [];
	var $extra = "";
	var $count = 0;

	function __construct($xurl)
	{
		$this->url = $xurl;
		//$this->add("session", session_id());
	}

	function add($xparametro, $xvalor)
	{
		$this->parametros[$this->count]["p"] = $xparametro;
		$this->parametros[$this->count]["v"] = $xvalor;
		$this->count++;
	}

	/**
	 * Toma todos los par�metros del GET y los agrega como propios
	 */
	function addFromRequestG()
	{
		foreach ($_GET as $p => $v) {
			$this->add($p, $v);
		}
	}

	function resetParametros()
	{
		$this->parametros = array();
	}

	function setUrl($xurl)
	{
		$this->url = $xurl;
	}

	function addExtra($xextra)
	{
		$this->extra .= $xextra;
	}

	function toUrl()
	{
		$res = $this->url;

		$encParams = array();
		if (strContiene($res, "?")) {
			$urlParts = explode("?", $res);
			$encParams[] = $urlParts[1];
		}

		$i = 0;
		while ($i < $this->count) {
			if (isset($this->parametros[$i]["v"]) &&  ($this->parametros[$i]["v"] != "")) {
				if (($res == "") || strpos($res, "?") === FALSE)
					$res .= "?";
				else
					$res .= "&";

				$res .= $this->parametros[$i]["p"];
				$res .= "=";
				$res .= urlencode($this->parametros[$i]["v"]);

				$encParams[] = $this->parametros[$i]["p"] . "=" . urlencode($this->parametros[$i]["v"]);
			}
			$i++;
		}

		$encriptaUrl = getParameterInt("sc3-encripta-url", 0);
		//reemplaza todos los parametros por un encriptado
		if ($encriptaUrl) {
			$aurl = explode("?", $res);
			if (count($aurl) > 1) {
				$enc = new Sc3Encriptador();
				$res = $aurl[0] . "?p=" . $enc->encryptUrl($aurl[1]);
			}
		}
		return $res;
	}
}



class FormValidator
{
	var $reqFields = [];
	var $count = 0;
	var $queryname = "";

	function __construct()
	{
	}

	function setQueryName($xqueryname)
	{
		$this->queryname = $xqueryname;
	}

	function add($xid, $xNombreCampo)
	{
		$this->reqFields[$this->count]["id"] = $xid;
		$this->reqFields[$this->count]["desc"] = $xNombreCampo;
		$this->reqFields[$this->count]["req"] = 1;
		$this->count++;
	}

	function addAlternativo($xid, $xNombreCampo)
	{
		$this->reqFields[$this->count]["id"] = $xid;
		$this->reqFields[$this->count]["desc"] = $xNombreCampo;
		$this->reqFields[$this->count]["req"] = 0;
		$this->count++;
	}

	function toScript()
	{
		$res = " function validar()\n{";
		$i = 0;
		while ($i < $this->count) {
			if ($this->reqFields[$i]["req"] == 1) {

				$res .= "\nif (document.getElementById('" . $this->reqFields[$i]["id"] . "').value == '')";
				$res .= "\n{";
				$res .= "\n  showWarn2('Ingrese un valor en el campo \'" . $this->reqFields[$i]["desc"] . "\'');";
				$res .= "\n  alert('Ingrese un valor en el campo \'" . $this->reqFields[$i]["desc"] . "\'');";
				$res .= "\n  document.getElementById('" . $this->reqFields[$i]["id"] . "').focus();";
				$res .= "\n  return false;";
				$res .= "\n}";
			} else {
				//pregunta si está seguro
				$res .= "\nif (document.getElementById('" . $this->reqFields[$i]["id"] . "').value == '')";
				$res .= "\n{";
				$res .= "\n  if (!confirm('Esta seguro de no ingresar un valor en el campo \'" . $this->reqFields[$i]["desc"] . "\' ?'))";
				$res .= "\n  {";
				$res .= "\n    document.getElementById('" . $this->reqFields[$i]["id"] . "').focus();";
				$res .= "\n    return false;";
				$res .= "\n  }";
				$res .= "\n}";
			}

			$i++;
		}

		//analiza si existe una funcion queryname_validar() con codigo extra al control de datos requeridos
		if (!sonIguales($this->queryname, ""))
			$res .= "\n return sc3fireValidar('" . $this->queryname . "');\n}";
		else
			$res .= "\n return true;\n}";
		return $res;
	}
}

function linkImg($xurl, $xicon, $xtexto, $xtarget = "")
{
	$res = "<a href=\"" . $xurl . "\" title=\"$xtexto\" target=\"$xtarget\">";
	$res .= img($xicon, $xtexto) . " " . $xtexto;
	$res .= "</a>";
	return $res;
}

function linkImgFa($xurl, $xicon, $xtexto, $xsize = "fa-2x", $xtarget = "", $xclass = "")
{
	$res = "<a class=\"$xclass\" href=\"" . $xurl . "\" title=\"$xtexto\" target=\"$xtarget\">";
	$res .= imgFa($xicon, $xsize) . " " . $xtexto;
	$res .= "</a>";
	return $res;
}


//CONSTANTES de anchos de tablas y celdas (agregar a sc-ofucscar...)
$TABLA = 500;
$TABLA_2 = $TABLA / 2;
$TABLA_3_1 = ($TABLA) / 3;
$TABLA_3_2 = $TABLA - $TABLA_3_1;
$TABLA_4_1 = ($TABLA) / 4;





/*
 Permite seleccionar entre el formato usual o excel
*/
class HtmlFormato
{
	var $mid = "format";
	var $mvalue = "";
	var $mautosubmit = false;
	var $mxlsname = "resultado";

	function __construct()
	{
	}

	function setAutoSubmit()
	{
		$this->mautosubmit = true;
	}

	function setXlsName($xxls)
	{
		$this->mxlsname = str_replace(" ", "-", $xxls) . "-" . date("d-m-y");
	}

	function toHtml()
	{
		if (!esExcel()) {
			$c = new HtmlCombo($this->mid, $this->mvalue);
			$c->add("", "pagina");
			$c->add("excel", "excel");

			$h = new HtmlHidden("xlsname", $this->mxlsname);

			if ($this->mautosubmit)
				$c->onchangeSubmit();
			return $c->toHtml() . $h->toHtml();
		}
		return "";
	}
}



/**
 * Arma un combo con el tipo de gráfico a crear
 *
 */
class HtmlGraphicType
{
	var $mid = "gtype";
	var $mvalue = "column_3d";

	function __construct($xid)
	{
		$this->mid = $xid;
		$this->valueFromRequest();
	}

	function valueFromRequest()
	{
		$this->setValue(Request($this->mid));
	}

	function setValue($xvalue)
	{
		$this->mvalue = $xvalue;
	}

	function toHtml()
	{
		$c = new HtmlCombo($this->mid, $this->mvalue);
		$c->add("column_3d", "Barras (3d)");
		$c->add("pie_3d", "Torta (3d)");
		return $c->toHtml();
	}
}


class HtmlHelp
{
	var $mtooltip = "";

	function __construct($xtooltip)
	{
		$this->mtooltip = $xtooltip;
	}

	function toHtml()
	{
		return imgFa("fa-life-ring", "fa-lg", "boton-fa-control", $this->mtooltip);
	}
}



/**
 * Clase que representa un par de una etiqueta: valor
 */
class HtmlEtiquetaValor
{
	var $mEtiqueta = "";
	var $mValor = "";
	var $mClass = "";

	function __construct($xetiqueta, $xvalor)
	{
		$this->mEtiqueta = $xetiqueta;
		$this->mValor = $xvalor;
	}

	function setAncho100()
	{
		$this->mClass = "ancho100";
	}

	function toHtml()
	{
		$clase = $this->mClass;

		return "<div class=\"informacion $clase\">
					<div class=\"info-etiqueta\">" . $this->mEtiqueta . "</div>
					<div class=\"info-dato\">" . $this->mValor . "
					</div>
				</div>";
	}
}
