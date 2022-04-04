<?php 
/**
 * Texto de email con autosugest
 *
 */
class HtmlInputTextEmail
{
	var $id = "";
	var $size = "70";
	var $maxSize = "120";
	var $value = "";
	var $readonly = false;
	var $autosuggest = false;

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}

	/**
	 * Cambia para que sugiera de la agenda. Por defecto NO
	 */
	function setAutosuggest($xsuggest = true)
	{
		$this->autosuggest = $xsuggest;
	}

	function valueFromRequest()
	{
		$valor = Request($this->id);
		if (!esVacio($valor))
			$this->setValue($valor);
	}

	function getId()
	{
		return $this->id;
	}

	function getValue()
	{
		return $this->value;
	}

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	function getMaxSize()
	{
		return $this->maxSize;
	}

	function getSize()
	{
		return $this->size;
	}

	function toHtml()
	{
		if (strcmp($this->getValue(), "") == 0)
			$this->valueFromRequest();
		$result = "\n<!-- HtmlInputTextEmail -->\n";
		if ($this->readonly)
		{
			$result .= $this->getValue();
			$hid = new HtmlHidden($this->id, $this->value);
			$result .= $hid->toHtml();
		}
		else
		{
			$result .= "<input type=\"text\"";
			$result .= " size=\"" . $this->getSize() ."\"";
			$result .= " class=\"ancho100\"";
			$result .= " placeholder=\"Emails (separados por coma)\"";
			$result .= " maxlength=\"" . $this->getMaxSize() . "\"";
			$result .= " value=\"" . $this->value . "\"";
			
			$result .= " name=\"" . $this->getId() . "\" id=\"" . $this->getId() . "\"";
			$result .= " />\n";

			$result .= "\n<script type=\"text/javascript\">";

			// autosuggest
			if ($this->autosuggest)
			{
				$maxresults = 100;
				$result .= "\nvar options_" . $this->id . " = {";
				$result .= "script: function (input) { return \"sc-ajax-autosuggest.php?query=emails&limit=$maxresults&valor=\" + input; },";
				$result .= "\n	varname:\"" . $this->id . "desc\",";
				$result .= "\n	shownoresults:false,";
				$result .= "\n	maxresults:$maxresults,";
				$result .= "\n  callback: function (seleccion) {validarMails('" . $this->getId() . "');}";
				$result .= "\n};";
				$result .= "\nvar as_xml_" . $this->id . " = new bsn.AutoSuggest('". $this->id . "', options_" . $this->id . ");";
			}

			$result .= "\n document.getElementById('". $this->id . "').addEventListener(\"keyup\",  event => {
																				validarMails('". $this->id . "');});";
			$result .= "\n</script>";
		}

		return $result;
	}
}

?>