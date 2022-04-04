<?php


/*
 Combo con valores
*/
class HtmlCombo
{
	var $id = "";
	var $value = "";
	var $mOnChange = "";
	var $readonly = false;
	var $values = Array();
	var $count = 0;
	var $multiple = false;
	var $mshowvalues = false;
	var $mRequerido = false;
	var $mPattern = "";
	var $mPatternClass = "";
	var $mClass = "";
	var $mTruncSize = 80;
	
	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->setValue($xvalue);
	}

	function setClass($xClass)
	{
		$this->mClass = $xClass;
	}
	
	/**
	 * Configura que las opciones que comiencen con un patrón tendrán determinada clase
	 * @param string $xpattern
	 * @param string $xclass
	 */
	function setClassPattern($xpattern, $xclass)
	{
		$this->mPattern = $xpattern;
		$this->mPatternClass = $xclass;
	}
	
	function setTruncSize($xsize)
	{
		$this->mTruncSize = $xsize;
	}
	
	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	function setRequerido()
	{
		$this->mRequerido = true;
	}
	
	function valueFromRequest()
	{
		$this->setValue(Request($this->id));
	}

	/**
	 * Setea el modo combo multiple
	 *
	 */
	function setMultiple()
	{
		$this->multiple = true;
	}

	/**
	 * Muestra los valores formando una lista en lugar de un combo
	 */
	function setShowValues()
	{
		$this->mshowvalues = true;
	}
	
	function onchange($xonchange)
	{
		$this->mOnChange = $xonchange;
	}

	function onchangeSubmit()
	{
		$this->onchange("getElementById('form1').submit();");
	}

	function add($xkey, $xvalue, $xicon = "", $xclass = "")
	{
		$this->values[$this->count]["key"] = $xkey;
		$this->values[$this->count]["value"] = $xvalue;
		$this->values[$this->count]["icon"] = $xicon;
		$this->values[$this->count]["class"] = $xclass;
		$this->count++;
	}

	function addSeleccione($xtexto = "- Seleccione -")
	{
		$this->add("", $xtexto);
	}
	
	function addTodos()
	{
		$this->add("", " - todos - ");
	}

	/**
	* Carga el combo con el RS dado
	*/
	function cargarRs($xrs, $xidfield, $xdescfield, $xicon = "", $xdescVacio = "", $xforzarVacio = false)
	{
		if (($xrs->EOF() || $xforzarVacio) && !sonIguales($xdescVacio, ""))
			$this->add("0", $xdescVacio);

		while (!$xrs->EOF())
		{
			$class = "";
			if (!esVacio($this->mPattern) && startsWith($xrs->getValue($xdescfield), $this->mPattern))
				$class = $this->mPatternClass;
			$this->add($xrs->getValue($xidfield), $xrs->getValue($xdescfield), $xicon, $class);
			$xrs->Next();
		}
	}

	/**
	 * Carga un arreglo asumiendo que tienen key=>valor
	 *
	 * @param unknown_type $xarray
	 */
	function cargarArray($xarray)
	{
		foreach ($xarray as $indice => $valor)
		{
			$this->add($valor[0], $valor[1]);
		}
	}

	function cargarArray2($xarray, $xkey, $xdesc, $xicon = "")
	{
		foreach ($xarray as $indice => $valor)
		{
			$this->add($valor[$xkey], $valor[$xdesc], $xicon);
		}
	}


	function cargarDiasMes()
	{
		$i = 1;
		while ($i <= 28)
		{
			$this->add($i, $i);
			$i++;
		}
	}


	function setReadOnly($xreadonly = true)
	{
		$this->readonly = $xreadonly;
	}

	/*
	 Produce cada opcion del combo
	*/
	function option($xValue, $xVisible, $xicon, $xSelected, $xClass = "")
	{
		$xVisible = substr($xVisible, 0, $this->mTruncSize);
		$str = "\n  <option value=\"" . $xValue . "\"";
		if ($xSelected==1)
			$str .= " selected=\"selected\"";
		if (!sonIguales($xicon, ""))
			$str .= " class=\"imagebacked\" style=\"background-image: url(" . $xicon . ");\"";
		if (!esVacio($xClass))
			$str .= " class=\"$xClass\"";
		$str .= ">";
		$str .= $xVisible . "</option>";
		return $str;
	}


	function getDescripcionSeleccionada()
	{
		$i = 0;
		while ($i < $this->count)
		{
			if ($this->values[$i]["key"] == $this->value)
				return $this->values[$i]["value"];
			$i++;
		}
		return "";
	}

	function toHtml()
	{
		$id = $this->id;
		$res = "\n<!-- HtmlCombo -->\n";
			
		if ($this->readonly)
		{
			$i = 0;
			while ($i < $this->count)
			{
				if ($this->values[$i]["key"] == $this->value)
					$res .= $this->values[$i]["value"];
				$i++;
			}
			$hid = new HtmlHidden($id, $this->value);
			$res .= $hid->toHtml();
		}
		else
		{
			$res .= "<select name=\"". $id;
			if ($this->multiple)
				$res .= "[]\" multiple=\"multiple\" size=\"8\" width=\"210\"";
			else
				$res .= "\"";
			if (!sonIguales($this->mOnChange, ""))
				$res .= " onchange=\"" . $this->mOnChange . "\"";

			if ($this->mRequerido)
				$res.= " required ";		
			if ($this->mshowvalues)
				$res .= " size=\"5\"";
		
			$class = $this->mClass;	
			if ($this->mRequerido)
				$class .= " requerido";
			
			$res .= " class=\"$class\" ";
			$res .= " id=\"". $id . "\">";
			$i = 0;
			while ($i < $this->count)
			{
				$selected = 0;
				if (sonIguales($this->values[$i]["key"], $this->value))
					$selected = 1;

				$res .= $this->option($this->values[$i]["key"], htmlVisible($this->values[$i]["value"]), $this->values[$i]["icon"], $selected, $this->values[$i]["class"]);
				$i++;
			}
			$res .= "</select>\n";
		}
		
		/*
		if ($this->mRequerido)
			$res .= "*";
		*/
		
		return $res;
	}
}


class HtmlComboMes
{
	var $mid = "";
	var $mvalue = "";
	var $mtodos = false;
	var $mSubmit = false;
	var $mTruncSize = 10;

	function __construct($xid, $xvalue)
	{
		$this->mid = $xid;
		$this->mvalue = $xvalue;
	}

	function addTodos()
	{
		$this->mtodos = true;
	}

	function setDefaultCurrent()
	{
		$dia = getdate(time());
		if (sonIguales($this->mvalue, ""))
			$this->mvalue = $dia["mon"];
	}

	function onchangeSubmit()
	{
		$this->mSubmit = true;
	}
	
	function setSizeSmall()
	{
		$this->mTruncSize = 3;
	}
	
	function toHtml()
	{
		$dia = getdate(time());
		$mes = $dia["mon"];
		if ($this->mtodos)
			$mes = 0;
		if (strcmp($this->mvalue, "") != 0)
			$mes = $this->mvalue;
			
		$c = new HtmlCombo($this->mid, $mes);
		
		$c->setTruncSize($this->mTruncSize);
		if ($this->mtodos)
			$c->add(0, "-todos-");
		if ($this->mSubmit)
			$c->onchangeSubmit();
			
		$c->add(1, "enero");
		$c->add(2, "febrero");
		$c->add(3, "marzo");
		$c->add(4, "abril");
		$c->add(5, "mayo");
		$c->add(6, "junio");
		$c->add(7, "julio");
		$c->add(8, "agosto");
		$c->add(9, "septiembre");
		$c->add(10, "octubre");
		$c->add(11, "noviembre");
		$c->add(12, "diciembre");
		return $c->toHtml();
	}
}


class HtmlComboAno
{
	var $mid = "";
	var $mvalue = "";
	var $mtodos = false;
	var $mSubmit = false;

	function __construct($xid, $xvalue)
	{
		$this->mid = $xid;
		$this->mvalue = $xvalue;
	}

	function addTodos()
	{
		$this->mtodos = true;
	}

	function onchangeSubmit()
	{
		$this->mSubmit = true;		
	}
	
	function setDefaultCurrent()
	{
		$dia = getdate(time());
		if (sonIguales($this->mvalue, ""))
			$this->mvalue = $dia["year"];
	}

	function toHtml()
	{
		$dia = getdate(time());
		$ano = $dia["year"];
		if ($this->mtodos)
			$ano = 0;
		if (strcmp($this->mvalue, "") != 0)
			$ano = $this->mvalue;
			
		$c = new HtmlCombo($this->mid, $ano);
		if ($this->mtodos)
			$c->add(0, "-todos-");
		if ($this->mSubmit)
			$c->onchangeSubmit();
		
		$i = $dia["year"] - 8;
		while ($i < $dia["year"] + 3)
		{
			if ($i == $dia["year"])
				$c->add($i, "$i", "", "option_destacado");
			else
				$c->add($i, $i);
			$i++;
		}

		return $c->toHtml();
	}
}


?>