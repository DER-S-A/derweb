<?php

/**
 * Vieja clase, con url a una imagen de sc-barcode.php
 * @author marcos
 */
class HtmlBarcode2
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

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	function toHtml()
	{
		$result = "\n<!-- HtmlBarcode -->";
		$result .= "<table><tr><td>";
		$result .= "<img ";
		$result .=" name=\"" . $this->id . "\"";
		$result .=" id=\"" . $this->id . "\"";
		$result .=" alt=\"" . $this->value . "\"";
		$result .=" title=\"" . $this->value . "\"";
		$result .=" src=\"sc-barcode.php?barcode=" . $this->value . "\"";
		$result .=" />\n";
		$result .="</td></tr>";
		$result .="<tr><td align=\"center\">" . $this->value . "</td></tr></table>";
		return $result;
	}
}

/**
 * Códigos de barra con Javascript
 * Utiliza libreria JsBarcode.all.min.js de http://lindell.me/JsBarcode/
 * @author marcos
 * Fecha: 5-mar-2017
 */
class HtmlBarcode
{
	var $id = "";
	var $value = "";
	var $format = "";
	var $width = 2;
	var $height = 60;
	var $displayValue = "true";

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}
	
	function dontDisplayValue()
	{
		$this->displayValue = "false";
	}

	function valueFromRequest()
	{
		$this->value = Request($this->id);
	}

	function setWidth3()
	{
		$this->width = 3;
	}
	
	function setWidth1()
	{
		$this->width = 1;
	}
	
	
	function setHeight($xheight)
	{
		$this->height = $xheight;
	}
	
	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	/**
	 * Alternativas:
		CODE128
		EAN / UPC
		CODE39
		ITF-14
		MSI
		Pharmacode
		Codabar
	 * @param string $xfont
	 */
	function setFormat($xformat)
	{
		$this->format = $xformat;
	}
	
	function toHtml()
	{
		$result = "\n<!-- HtmlBarcode -->";
		$result .= '<svg id="' . $this->id . '"></svg>
					<script src="scripts/JsBarcode.all.min.js"></script>
					<script type="text/javascript">
					JsBarcode("#' . $this->id . '", "' . $this->value . '",  
								{
								  format: "' . $this->format . '",
								  height: ' . $this->height . ',
								  width: ' . $this->width . ',		
								  displayValue: ' . $this->displayValue . '
								}
							);					
					</script>
					<br />';
		
		return $result;
	}
}


?>