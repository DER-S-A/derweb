<?php

/**
 * Clase para cargar un comprobante externo.
 * Arma cuatro controles con el TIPO (FC/NC/ND, etc), TALON (A/B/C/X)
 * y muestra el campo completo, ej: FC-A-00004-9282
 */
class HtmlFactura
{
	var $mId = "";
	var $mTipofactura = "";
	var $mPuntoVenta = "";
	var $mTalon = "";
	var $mNroFact = "";
	var $mRequerido = false;
	var $mValue = "";
	var $mOnChange = "";

	/**
	 * El valor puede venir separado por "-", ej: FC-A-00004-9282
	 */
	function __construct($xId, $xvalue = "")
	{
		$this->mId = $xId;
		if (!esVacio($xvalue)) {
			$this->mValue = $xvalue;
			$aFactura = explode("-", $xvalue);
			$this->mTipofactura = $aFactura[0];
			if (count($aFactura) > 1)
				$this->mTalon = $aFactura[1];
			if (count($aFactura) > 2)
				$this->mPuntoVenta = $aFactura[2];
			if (count($aFactura) > 3)
				$this->mNroFact = $aFactura[3];
		}
	}

	function setRequerido($xreq = true)
	{
		$this->mRequerido = $xreq;
	}

	function getId()
	{
		return $this->mId;
	}

	function setTipoFactura($xTf)
	{
		$this->mTipofactura = $xTf;
	}

	function getTipoFactura()
	{
		return $this->mTipofactura;
	}

	function setPuntoVenta($xPv)
	{
		$this->mPuntoVenta = $xPv;
	}

	function getPuntoVenta()
	{
		return $this->mPuntoVenta;
	}

	function setTalon($xT)
	{
		$this->mTalon = $xT;
	}

	function getTalon()
	{
		return $this->mTalon;
	}

	function setNroFact($xT)
	{
		$this->mNroFact = $xT;
	}

	function getNroFact()
	{
		return $this->mNroFact;
	}

	/**
	 * Evento de cambios en el combo
	 */
	function onchange($xonchange)
	{
		$this->mOnChange = $xonchange;
	}

	function toHtml()
	{
		$cboTipo = new HtmlCombo($this->getId() . "_tipo", $this->getTipoFactura());
		$cboTipo->add("", "");
		$cboTipo->add("FC", "FC");
		$cboTipo->add("RC", "RC");
		$cboTipo->add("ND", "ND");
		$cboTipo->add("NC", "NC");
		$cboTipo->add("TK", "Ticket");
		$cboTipo->add("RE", "Remito");
		$cboTipo->add("INV", "Invoice");
		$cboTipo->add("-", "s/comp");
		$cboTipo->onchange("facturaCargar('" . $this->getId() . "', 'tipo');" . $this->mOnChange);
		if ($this->mRequerido) {
			$cboTipo->setRequerido();
		}

		$txtlinea = new HtmlInputText($this->getId() . "_punto_venta", $this->getPuntoVenta());
		$txtlinea->setOnKeyUp("facturaCargar('" . $this->getId() . "', 'pv');" . $this->mOnChange);
		$txtlinea->setSize(4);

		$cboTalon = new HtmlCombo($this->getId() . "_talon", $this->getTalon());
		$cboTalon->add("", "");
		$cboTalon->add("A", "A");
		$cboTalon->add("B", "B");
		$cboTalon->add("C", "C");
		$cboTalon->add("M", "M");
		$cboTalon->add("R", "R");
		$cboTalon->add("X", "X");
		$cboTalon->onchange("facturaCargar('" . $this->getId() . "', 'talon');" . $this->mOnChange);

		$txtfactura = new HtmlInputText($this->getId() . "_comprobante", $this->getNroFact());
		$txtfactura->setSize(6);
		$txtfactura->setMaxSize(8);
		$txtfactura->setOnKeyUp("facturaCargar('" . $this->getId() . "', 'nro');" . $this->mOnChange);

		if (esVacio($this->mValue)) {
			$txtfactura->ignoreRequest();
			$txtlinea->ignoreRequest();
		}

		$hidden = new HtmlHidden($this->getId(), $this->mValue);

		$valor = "";
		if (!esVacio($this->mTipofactura))
			$valor = $this->mTipofactura . "-" . $this->mTalon . "-" . $this->mPuntoVenta . "-" . $this->mNroFact;
		$span = "<span class=\"span-detalle\" id=\"" . $this->getId() . "_span\">$valor</span>";

		$res = $cboTipo->toHtml() . $cboTalon->toHtml() . $txtlinea->toHtml() . $txtfactura->toHtml() . $span . $hidden->toHtml();
		return $res;
	}
}
