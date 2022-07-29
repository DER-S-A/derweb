<?php

/**
 * Checkbox, con valores internos 1 o 0
 */
class HtmlCheckBox
{
	var $mid = "";
	var $mvalue = "0";
	var $mlabel = "";
	var $mchecked = false;
	var $mReadOnly = false;
	var $mOnClick = "";
	
	function __construct($xid, $xvalue)
	{
		$this->mid = $xid;
		$this->mvalue = $xvalue;
	}
	
	function setOnClick($xOnClickEvent)
	{
		$this->mOnClick = $xOnClickEvent;
	}
	
	function setLabel($xlabel)
	{
		$this->mlabel = $xlabel;
	}

	function setChecked($xchecked)
	{
		$this->mchecked = $xchecked;
	}
	
	function setReadOnly($xreadOnly = true)
	{
		$this->mReadOnly = $xreadOnly;
	}
	
	function toHtml($xnewLineAfter = false)
	{
		if ($this->mReadOnly)
		{
			$valor = $this->mvalue;
			if (!$this->mchecked)
				$valor = "";
			$hid = new HtmlHidden($this->mid, $valor);
			return $hid->toHtml();
		}
		
		$str = "<input name=\"" . $this->mid . "\" id=\"" . $this->mid . "\" type=\"checkbox\" value=\"" . $this->mvalue . "\"";
		if ($this->mchecked)
			$str .= " checked=\"checked\"";
		if (!esVacio($this->mOnClick))
			$str .= " onclick=\"" . $this->mOnClick . "\"";
				
		$str .= " style=\"margin:2px\" /> ";
		if (!esVacio($this->mlabel))
			$str .= "<label for=\"" . $this->mid . "\">" . $this->mlabel . "</label>";
		if ($xnewLineAfter)
			$str .= "<br />";
		return $str;
	}
}


?>