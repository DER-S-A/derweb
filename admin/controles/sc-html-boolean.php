<?php


/*
 Combo con los valores Si|No
*/
class HtmlBoolean
{
	var $id = "";
	var $value = "1";
	var $readonly = false;
	var $mautosubmit = false;
	var $monchange = "";
	var $mRequerido = false;

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}

	function setReadOnly($xreadonly)
	{
		$this->readonly = $xreadonly;
	}

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}
	
	function setRequerido()
	{
		$this->mRequerido = true;
	}

	/*
	 Toma el valor por defecto, salvo que se encuentre el valor en el request
	*/
	function setDefault($xvalue)
	{
		$this->valueFromRequest();
		if (strcmp($this->value, "") == 0)
			$this->value = $xvalue;
	}

	function onchangeSubmit()
	{
		$this->mautosubmit = true;
	}

	function onchange($xonchange)
	{
		$this->monchange = $xonchange;
	}

	function valueFromRequest()
	{
		$this->setValue(Request($this->id));
	}

	function toHtml()
	{
		$id = $this->id;
		$res = "\n<!-- HtmlBoolean -->";
		if ($this->value == "")
			$this->value = 0;
			
		if ($this->readonly)
		{
			if ($this->value == 1)
				$res .= "Si";
			else
				$res .= "No";

			$hid = new HtmlHidden($id, $this->value);
			$res .= $hid->toHtml();
		}
		else
		{
			$combo = new HtmlCombo($id, $this->value);
			$combo->add("1", "Si");
			$combo->add("0", "No");
			if ($this->mautosubmit)
				$combo->onchangeSubmit();
			if (!esVacio($this->monchange))
				$combo->onchange($this->monchange);

			if ($this->mRequerido)
				$combo->setRequerido();
				
			$res .= $combo->toHtml();
		}
		return $res;
	}
}

/**
 * Clase booleana con icon fa
 * TODO: pendiente, agregarle la etiqueta
 * @author marccos
 */
class HtmlBoolean2
{
	var $id = "";
	var $value = "1";
	var $readonly = false;
	var $mautosubmit = false;
	var $monchange = "";
	var $mRequerido = false;
	
	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->value = $xvalue;
	}
	
	function setReadOnly($xreadonly)
	{
		$this->readonly = $xreadonly;
	}
	
	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}
	
	function setRequerido()
	{
		$this->mRequerido = true;
	}
	
	/*
	 Toma el valor por defecto, salvo que se encuentre el valor en el request
	 */
	function setDefault($xvalue)
	{
		$this->valueFromRequest();
		if (strcmp($this->value, "") == 0)
			$this->value = $xvalue;
	}
	
	function onchangeSubmit()
	{
		$this->mautosubmit = true;
	}
	
	function onchange($xonchange)
	{
		$this->monchange = $xonchange;
	}
	
	function valueFromRequest()
	{
		$this->setValue(Request($this->id));
	}
	
	function toHtml()
	{
		$id = $this->id;
		$res = "\n<!-- HtmlBoolean2 -->";
		
		if ($this->value == "")
			$this->value = 0;
			
		$jscript = "sc3CambiarBoolean('$id', 'boton$id');" . $this->monchange;
		if ($this->readonly)
			$jscript = "";
		
		$title = "No";
		if ($this->value == 1)
		{
			$icono = "fa fa-toggle-on  fa-2x fa-fw w3-text-green";
			$title = "Si";
		}
		else
			$icono = "fa fa-toggle-off fa-2x fa-fw w3-text-grey";
					
		$res .= "\r\n<i id=\"boton$id\" onclick=\"$jscript\" class=\"$icono\" title=\"$title\"></i>";	
		$hid = new HtmlHidden($id, $this->value);
		$res .= $hid->toHtml();
		
		return $res;
	}
}



?>