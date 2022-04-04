<?php

/**
 * Clase con color picker
 */
class HtmlColor
{
	var $mid = "color1";
	var $mvalue = "#336699";

	function __construct($xid, $xvalue = "")
	{
		$this->mid = $xid;
		$this->mvalue = $xvalue;
	}

	function toHtml()
	{
		$str = "<input onchange=\"colorEnBlanco('" . $this->mid . "_color', '" . $this->mid ."')\" onload=\"colorEnBlanco('" . $this->mid . "_color', '" . $this->mid ."')\" name=\"" . $this->mid . "\" id=\"" . $this->mid . "\" type=\"color\" value=\"" . $this->mvalue . "\" /> ";
		$str.= "\r\n<select onchange=\"colorCambio('" . $this->mid . "_color','" . $this->mid ."')\" id=\"" . $this->mid . "_color\">
					<option value=\"\"> </option>
					<option style=\"color:#f1c40f;background-color:#f1c40f\" value=\"#f1c40f\">Amarillo</option>
					<option style=\"color:#e84118;background-color:#e84118\" value=\"#e84118\">Rojo</option>
					<option style=\"color:#44bd32;background-color:#44bd32\" value=\"#44bd32\">Verde</option>
					<option style=\"color:#0652DD;background-color:#0652DD\" value=\"#0652DD\">Azul</option>
					<option style=\"color:#D980FA;background-color:#D980FA\" value=\"#D980FA\">Lila</option>
					<option style=\"color:#b2bec3;background-color:#b2bec3\" value=\"#b2bec3\">Gris</option>
					<option style=\"color:#192a56;background-color:#192a56\" value=\"#192a56\">Marino</option>
					<option style=\"color:#ef5777;background-color:#ef5777\" value=\"#ef5777\">Rosa</option>
					<option style=\"color:#00d8d6;background-color:#00d8d6\" value=\"#00d8d6\">Agua</option>
					<option style=\"color:#ffa801;background-color:#ffa801\" value=\"#ffa801\">Naranja</option>
				</select>" ;
		
		return $str;
	}
}


?>