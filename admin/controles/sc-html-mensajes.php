<?php

/**
 * Clases de mensajes, emergentes o en linea
 */

class HtmlMessageDiv
{
	var $id = "";
	var $msg = "";
	var $warn = "";
	var $error = "";
	var $ocultarSolo = true;

	function __construct($xid, $xmsg = "", $xwarn = "", $xerror = "")
	{
		$this->id = $xid;
		$this->msg = $xmsg;
		$this->warn = $xwarn;
		$this->error = $xerror;
	}
	
	function setOcultarSolo($xocultar)
	{
		$this->ocultarSolo = $xocultar;
	}
	
	function toHtml()
	{
		$res = "<div id=\"" . $this->id . "\" class=\"\"></div>";
		
		if (!esVacio($this->msg))
			$res .= "<script>sc3DisplayMsg('" . $this->id . "', '" . $this->msg . "');</script>";
		if (!esVacio($this->warn))
			$res .= "<script>sc3DisplayWarning('" . $this->id . "', '" . $this->warn . "');</script>";
		if (!esVacio($this->error))
			$res .= "<script>sc3DisplayError('" . $this->id . "', '" . $this->error . "');</script>";
		
		return $res;
	}
}



?>