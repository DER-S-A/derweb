<?php

/**
 * clase para crear input para CBU
 * Autor: Ezequiel Algañaraz
 * Fecha: 15-01-2020 
 * SC3
 */ 

class HtmlCBU
{
	var $mId = "";
	var $mValor = "";
	var $mTitulo = "";
	var $mClass = "";
	var $mPlaceholder = "ingrese el cbu";
	var $mMaxlength = "26";

	function __construct($xId, $xvalor)
	{       
		$this->mValor = $xvalor;
		$this->mId = $xId;
	}

	function getMaxLenght()
	{
		return $this->mMaxlength;
	}

	function getValor()
	{
		return $this->mValor;
	}

	function getId()
	{
		return $this->mId;
	}

	function getClass()
	{
		return $this->mClass;
	}
		
	function setPlaceholder($xplaceholder)
	{
		$this->mPlaceholder = $xplaceholder;
	}
	
	function getPlaceholder()
	{
		return $this->mPlaceholder;
	}
	
	function toHtml()
	{	
		$result = "\n<input type=\"text\" size=\"26\" "; 
		$result .= " name=\"" . $this->getId() . "\"";
		$result .= " id=\"" . $this->getId() . "\" ";
		$result .= " placeholder=\"" . $this->getPlaceholder() . "\"";
		$result .= " value=\"" . $this->getValor() . "\" ";
		$result .= " maxlength=\"" . $this->getMaxLenght() . "\" ";
		$result .= " onkeyup=\"cbuValidar('". $this->getId() ."')\"/>";
		if ($this->getValor() != "")
		{
			$result .= "\n<script>";
			$result .= " cbuValidarCargado('" . $this->getId() . "');";
			$result .= " </script>"; 
		}
		return $result;
	}
}
	
?>