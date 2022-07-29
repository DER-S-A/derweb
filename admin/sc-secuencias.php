<?php

//Autor: Marcos Casamayor
//Fecha: 10-ene-2010
//Funciones de secuencia y numeradores


class ScSecuencia
{
	var $mnombre = "";
	var $mid = "";
	var $mproximo = 1;
	var $mnueva = false;
	var $mcompletarCeros = 1;
	
	/**
	 * Crea el objeto secuencia. Si no lo encuentra en la BD por nombre, la crea arrancando de 1
	 *
	 * @param string $xnombre
	 * @return ScSecuencia
	 */
	function __construct($xnombre, $xcompletarCeros = 1)
	{
		$this->mnombre = $xnombre;
		$this->mcompletarCeros = $xcompletarCeros;
		$rs = locateRecordWhere("sc_secuencias", "nombre = '$xnombre'");
		if ($rs->EOF())
		{
			//crea secuencia, mantiene id
			$values = array();
			$values["proximo_numero"] = $this->mproximo;
			$values["nombre"] = $xnombre;
			$values["completar_ceros"] = $this->mcompletarCeros;
			$sql = insertIntoTable("qscsecuencias", $values);			
			
			$this->mid =  $rs->execInsert($sql);
			$this->mnueva = true;
		}
		else
		{
			$this->mid = $rs->getValue("id");
			$this->mproximo = $rs->getValue("proximo_numero");
			$this->mcompletarCeros = $rs->getValue("completar_ceros");
		}
	}
	
	/**
	 * Retorna si acaba de insertarse en la BD
	 * @return unknown
	 */
	function isNueva()
	{
		return $this->mnueva;
	}
	
	/**
	 * Retorna el proximo, formateado segun "completar ceros"
	 * Ej: getProximoFormateado(5): 0005-00000345"
	 */
	function getProximoFormateado($xprefijo = 1)
	{
		if ($this->mcompletarCeros)
		{
			return str_pad($xprefijo, 4, "0", STR_PAD_LEFT) . "-" . str_pad($this->mproximo, 8, "0", STR_PAD_LEFT);
		}
		else
		{
			return $this->mproximo;
		}
	}
	
	function getProximo($xcantCeros = 0)
	{
		if ($xcantCeros == 0)
			return $this->mproximo;
		
		return str_pad($this->mproximo, $xcantCeros, "0", STR_PAD_LEFT);
	}
	
	function incrementar()
	{
		$this->mproximo++;
	}
	
	function incrementarYGrabar($xbd)
	{
		$this->mproximo++;
		//todo: verificar que no se incremento en este instante !
		$sql = "update sc_secuencias 
				set proximo_numero = proximo_numero + 1 
				where id = " . $this->mid;
		$xbd->execQuery($sql);
	}

	function grabar($xbd)
	{
		$sql = "update sc_secuencias 
				set proximo_numero = " . $this->mproximo . " 
				where id = " . $this->mid;
		$xbd->execQuery($sql);
	}
	
	function setearProximo($xproximo)
	{
		$this->mproximo = $xproximo;
		$sql = "update sc_secuencias 
				set proximo_numero = $xproximo 
				where id = " . $this->mid;
		$bd = new BDObject();
		$bd->execQuery($sql);
	}
}



?>