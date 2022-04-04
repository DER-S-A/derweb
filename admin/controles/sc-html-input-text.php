<?php


/**
 * Clase que representa un Texto
 */
class HtmlInputText
{
	var $id = "";
	var $size = "60";
	var $maxSize = "80";
	var $value = "";
	var $type = "text";
	var $step = "0.01";
	var $placeholder = "";
	var $mclass = "";

	var $onblur = "";
	var $onkeypressed = "";
	var $onkeyup = "";
	var $onclick = "";

	var $readonly = false;
	var $disable = false;
	var $requerido = false;
	var $ignoreRequest = false;
	var $isfloat = false;
	var $decimales = 2;
	var $autocomplete = "on";
	var $isinteger = false;
	var $showCalculator = true;

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}

	function setSize($xsize)
	{
		$this->size = $xsize;
	}

	function setAutocompleteOff()
	{
		$this->autocomplete = "off";
	}

	function setClass($xclass)
	{
		$this->mclass = $xclass;
	}

	function getClass()
	{
		return $this->mclass;
	}

	function setDecimales($xdecimales)
	{
		$this->decimales = $xdecimales;
	}

	function setDisable($xdisable)
	{
		$this->disable = $xdisable;
	}

	function setSizeCheque()
	{
		$this->setSize(20);
		$this->setMaxSize(20);
	}

	function setSizeComprobante()
	{
		$this->setSize(20);
		$this->setMaxSize(20);
	}

	function setRequerido()
	{
		$this->requerido = true;
	}

	function setReadOnly($xreadonly = true)
	{
		$this->readonly = $xreadonly;
	}

	function setMaxSize($xsize)
	{
		$this->maxSize = $xsize;
	}

	function getSize()
	{
		return $this->size;
	}

	function dontShowCalculator()
	{
		$this->showCalculator = false;
	}

	function setValue($xvalue)
	{
		$this->value =  str_replace(",", "", $xvalue);
	}

	function valueFromRequest($xdef = "")
	{
		if (!esVacio(Request($this->id)))
			$this->setValue(Request($this->id));
		else
			$this->setValue($xdef);
	}

	function getValue()
	{
		if ($this->isfloat) {
			return formatFloat($this->value, $this->decimales, true);
		}
		return $this->value;
	}

	function getMaxSize()
	{
		return $this->maxSize;
	}

	function setType($xtype)
	{
		$this->type = $xtype;
	}

	function setTypeNumber()
	{
		$this->setType("number");
	}

	function setTypePassword()
	{
		$this->setType("password");
		$this->setSize(20);
		$this->setMaxSize(40);
		$this->setPlaceholder("clave...");
	}

	function getOnblur()
	{
		return $this->onblur;
	}

	function setOnblur($xonblur)
	{
		$this->onblur = $xonblur;
	}

	function setOnClick($xevent)
	{
		$this->onclick = $xevent;
	}

	function setAutoselect()
	{
		$this->setOnClick("sc3SelectAll('" . $this->getId() . "');");
	}

	function setTypeInt()
	{
		$this->setSize(5);
		$this->setMaxSize(10);
		$this->setAutocompleteOff();
		$this->setOnblur("javascript:validarint(document.getElementById('" . $this->id . "').value, " . $this->id . ")");
		$this->isinteger = true;
		$this->step = "1";
	}

	function setTypeFloat($xDecimales = 2)
	{
		$this->setDecimales($xDecimales);
		$this->setSize(11);
		$this->setAutocompleteOff();
		$this->setMaxSize(30);
		$this->setOnblur("javascript:validarfloatDec('" . $this->id . "', " . $this->decimales . ")");
		$this->isfloat = true;

		if ($xDecimales == 3)
			$this->step = "0.001";
	}

	function setOnKeyPressed($xevent)
	{
		$this->onkeypressed = $xevent;
	}

	function setOnKeyUp($xevent)
	{
		$this->onkeyup = $xevent;
	}

	function getPlaceholder()
	{
		return $this->placeholder;
	}

	function setPlaceholder($xplaceholder)
	{
		$this->placeholder = $xplaceholder;
	}

	function getType()
	{
		return $this->type;
	}

	function getName()
	{
		return $this->id;
	}

	function getId()
	{
		return $this->id;
	}

	function setDefault($xvalue)
	{
		$this->valueFromRequest();
		if (strcmp($this->getValue(), "") == 0)
			$this->setValue($xvalue);
	}

	function ignoreRequest()
	{
		$this->ignoreRequest = true;
	}

	function toHtml()
	{
		if (esVacio($this->getValue()) && !$this->ignoreRequest)
			$this->valueFromRequest();
		$result = "";

		$class = " ";
		$onclickSelect = "";
		if ($this->isfloat || $this->isinteger) {
			$onclickSelect = "sc3SelectAll('" . $this->id . "');";
			$class = " input_numeric ";
		}

		if ($this->requerido)
			$class .= " requerido ";

		$step = "";
		if (sonIguales($this->type, "number"))
			$step = " step=\"" . $this->step . "\"";

		$result .= "<input type=\"" . $this->getType() . "\"";
		$result .= " value=\"" . $this->getValue() . "\"";
		$result .= " size=\"" . $this->getSize() . "\"";
		$result .= " placeholder=\"" . $this->getPlaceholder() . "\"";
		$result .= " maxlength=\"" . $this->getMaxSize() . "\"";
		$result .= " name=\"" . $this->getName() . "\" id=\"" . $this->getId() . "\"";
		$result .= " class=\"" . $this->getClass() . " $class\"";
		$result .= " autocomplete=\"" . $this->autocomplete . "\"";
		$result .= " $step";
		if ($this->readonly)
			$result .= " readonly=\"true\" ";

		$result .= " onclick=\"$onclickSelect " . $this->onclick . "\"";

		if ($this->getOnblur() != "")
			$result .= " onblur=\"" . $this->getOnblur() . "\"";
		if (!sonIguales($this->onkeypressed, ""))
			$result .= " onkeypress=\"" . $this->onkeypressed . "\"";

		if (!esVacio($this->onkeyup)) {
			if ($this->isfloat)
				$result .= " onkeyup=\"mathEvalKey('" . $this->getId() . "', event);"  . $this->onkeyup . "\"";
			else
				$result .= " onkeyup=\"" . $this->onkeyup . "\"";
		} else {
			if ($this->isfloat) {
				$result .= " onkeyup=\"mathEvalKey('" . $this->getId() . "', event)\"";
			}
		}

		if ($this->requerido) {
			$class .= " requerido ";
			$result .= " required ";
		}

		if ($this->isfloat) {
			$result .= " title=\"Ingrese valor o expresion, ej: 478*1.21 [ENTER]\"";
		}

		if ($this->disable)
			$result .= " disabled=\"true\"";

		$result .= " />";

		if ($this->requerido)
			$result .= " ";

		if ($this->isfloat) {
			if ($this->showCalculator) {
				$result .= "<span id=\"" . $this->id .  "__exp\"> </span>";
			}
		}

		return $result;
	}
}


/**
 * Texto con autosugest
 *
 */
class HtmlInputTextSuggest
{
	var $id = "";
	var $msize = "27";
	var $maxSize = "80";
	var $value = "";
	var $readonly = false;
	var $onBlur = "";
	var $onFocus = "";
	var $callback = "";
	var $timeout = "2500";
	var $delay = "500";
	var $cache = "true";
	var $minchars = "2";

	var $query = "";
	//en true hace que el valor seleccionado se agrege al contenido actual
	var $appendData = false;

	var	$ocUrl = "";
	var $ocImg = "";
	var $ocHelp = "";

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}

	function valueFromRequest()
	{
		$this->setValue(Request($this->id));
	}

	function getId()
	{
		return $this->id;
	}

	function setId($xid)
	{
		$this->id = $xid;
	}

	function getValue()
	{
		return $this->value;
	}

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	function setMinchars($xminChars)
	{
		$this->minchars = $xminChars;
	}

	function setModoRapido()
	{
		$this->timeout = "5000";
		$this->delay = "200";
		$this->cache = "false";
	}

	function getMaxSize()
	{
		return $this->maxSize;
	}

	function getSize()
	{
		return $this->msize;
	}

	function setOnblur($xonblur)
	{
		$this->onBlur = $xonblur;
	}

	function getOnBlur()
	{
		return $this->onBlur;
	}

	function setSize($xsize)
	{
		$this->msize = $xsize;
	}

	function setOnFocus($xonFocus)
	{
		$this->onFocus = $xonFocus;
	}

	function getOnFocus()
	{
		return $this->onFocus;
	}

	function setQueryName($xquery)
	{
		$this->query = $xquery;
	}

	function setAppendData($xappend)
	{
		$this->appendData = $xappend;
	}

	function setCallback($xcallback)
	{
		$this->callback = $xcallback;
	}

	/**
	 * Operacion e icono para abrir una ventana flotante sobre esta
	 * @param string $xurl
	 * @param string $ximg
	 */
	function setOpenCatalog($xHelp, $xurl, $ximg)
	{
		$this->ocUrl = $xurl;
		$this->ocImg = $ximg;
		$this->ocHelp = $xHelp;
	}

	function toHtml()
	{
		if (strcmp($this->getValue(), "") == 0)
			$this->valueFromRequest();
		$result = "\n<!-- HtmlInputTextSuggest -->\n";
		$result .= "<table><tr><td>";
		if ($this->readonly) {
			$result .= $this->getValue();
			$hid = new HtmlHidden($this->id, $this->value);
			$result .= $hid->toHtml();
		} else {
			$result .= "<input type=\"text\"";
			$result .= " value=\"" . $this->getValue() . "\"";
			$result .= " size=\"" . $this->getSize() . "\"";
			$result .= " maxlength=\"" . ($this->getMaxSize() + 20) . "\"";
			$result .= " onblur=\"" . $this->getOnBlur() . "\"";
			$result .= " onfocus=\"" . $this->getOnFocus() . "\"";
			$result .= " name=\"" . $this->getId() . "\" id=\"" . $this->getId() . "\"";
			$result .= " />\n";

			if (!esVacio($this->ocUrl)) {
				$url = $this->ocUrl;
				$img = $this->ocImg;
				$help = $this->ocHelp;
				$id  = $this->getId();
				$result .= "\n <td> <a id=\"" .  $id . "_oc\" href=\"javascript:openCatalogExtra('$url', '$id','" . $id . "_id');\" title=\"$help\" class=\"rptinvisible\" tabindex=\"-1\">";
				if (esIconFontAwesome($img))
					$result .= imgFa($img, "fa-lg");
				else
					$result .= "<img src=\"$img\"/></a></td>";
			}

			$maxresults = 50;
			$append = "false";
			if ($this->appendData)
				$append = "true";

			$result .= "\n<script type=\"text/javascript\">";
			$result .= "\nvar options_" . $this->id . " = {";
			$result .= "script: function (input) { return \"sc-ajax-autosuggest.php?query=" . $this->query . "&limit=$maxresults&valor=\" + input; },";
			$result .= "\n	varname:\"" . $this->id . "desc\",";
			$result .= "\n	shownoresults:false,";
			$result .= "\n	timeout: " . $this->timeout . ",";
			$result .= "\n	delay: " . $this->delay . ",";
			$result .= "\n	cache: " . $this->cache . ",";
			$result .= "\n	minchars: " . $this->minchars . ",";
			$result .= "\n	appenddata: $append,";
			$result .= "\n	maxresults: $maxresults,";
			if (!esVacio($this->callback))
				$result .= "\n callback: " . $this->callback;
			else
				$result .= "\n callback: function (seleccion) {}";
			$result .= "\n};";
			$result .= "\nvar as_xml_" . $this->id . " = new bsn.AutoSuggest('" . $this->id . "', options_" . $this->id . ");";
			$result .= "\n</script>";
		}

		$result .= "</td></tr></table>";
		return $result;
	}
}


class HtmlInputTextDirContacto extends HtmlInputTextSuggest
{
	function __construct($xid, $xvalue)
	{
		$this->setId($xid);
		$this->setValue($xvalue);
		$this->setQueryName("dir_contacto");
		$this->setCallback("function (seleccion) {document.getElementById('" . $this->id . "_id').value = seleccion['id'];}");
	}
}


class HtmlInputTextDirContacto2 extends HtmlInputTextSuggest
{
	function __construct($xid, $xvalue)
	{
		$this->setId($xid);
		$this->setValue($xvalue);
		$this->setQueryName("dir_contacto2");
		$this->setCallback("function (seleccion) {document.getElementById('" . $this->id . "_id').value = seleccion['id'];}");
	}
}



class HtmlInputTextarea
{
	var $id = "";
	var $cols = "60";
	var $rows = "5";
	var $value = "";
	var $readonly = false;
	var $placeholder = "";
	var $ignoreRequest = false;
	var $voz = false;

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}

	function ignoreRequest()
	{
		$this->ignoreRequest = true;
	}

	function getValue()
	{
		return $this->value;
	}

	function getPlaceholder()
	{
		return $this->placeholder;
	}

	function setPlaceholder($xobs)
	{
		$this->placeholder = $xobs;
	}

	function setValue($xvalue)
	{
		return $this->value = $xvalue;
	}

	function valueFromRequest()
	{
		if (!esVacio(Request($this->id)))
			$this->setValue(Request($this->id));
	}

	function setDefault($xvalue)
	{
		$this->valueFromRequest();
		if (strcmp($this->getValue(), "") == 0)
			$this->setValue($xvalue);
	}

	function getName()
	{
		return $this->id;
	}

	function getId()
	{
		return $this->id;
	}

	function setRowsCols($xrows, $xcols)
	{
		$this->rows = $xrows;
		$this->cols = $xcols;
	}

	function getRows()
	{
		return $this->rows;
	}

	function setRows2()
	{
		$this->rows = 2;
	}

	function getCols()
	{
		return $this->cols;
	}

	function setCols($xcols)
	{
		$this->cols = $xcols;
	}

	function setSizeSmall()
	{
		$this->rows = "2";
	}

	function setSizeSingleLine($xcols = 80)
	{
		$this->rows = "1";
		$this->cols = $xcols;
	}

	function setSizeBig()
	{
		$this->rows = "15";
		$this->cols = "80";
	}

	function setVoz($valor)
	{
		$this->voz = $valor;
	}

	function getVoz()
	{
		return $this->voz;
	}

	function toHtml()
	{
		if (esVacio($this->getValue(), "") && !$this->ignoreRequest)
			$this->valueFromRequest();
		$result = "\n";
		if ($this->readonly) {
			$result .= $this->getValue();
			$hid = new HtmlHidden($this->id, $this->value);
			$result .= $hid->toHtml();
		} else {
			$result .= "<textarea";
			$result .= " rows=\"" . $this->getRows() . "\"";
			$result .= " cols=\"" . $this->getCols() . "\"";
			$result .= " placeholder=\"" . $this->getPlaceholder() . "\"";
			$result .= " name=\"" . $this->getName() . "\" id=\"" . $this->getId() . "\"";
			$result .= " />\n";
			$result .=  $this->getValue();
			$result .= "</textarea>";
		}

		if ($this->voz && !$this->readonly) {
			$result = '<div style="display: flex">' . $result . '
							<a onclick="iniciarVoz(\'' . $this->getId() . '\')" id="' . $this->getId() . 'iconoMicrofono" class="icono-microfono">
								<i class="fa fa-microphone fa-lg" style="   "></i>
							</a>
						</div>';
		}
		return $result;
	}
}

