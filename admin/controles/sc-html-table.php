<?php

/**
 * Clase para armar tablas en PHP
 * @autor: Marcos C
 */
class HtmlTable
{
	var $mtitulos = array();
	var $mtitulo = "";
	var $mfooter = array();
	var $mfilas = array();
	var $mwidth = "";
	var $mDefCellAlign = "right";
	var $mcols = array();
	var $useFilters = false;
	var $mclass = "data_table";
	var $mcurrentRowColor = "#e5e5e5";
	var $mPureBootstrap = false;
	var $mcantCols = 0;
	var $mStyle = "";
	
	function __construct()
	{
		$this->setWithM();		
	}
	
	function getCantCols()
	{
		return $this->mcantCols;
	}
	
	function setPureBootstrap()
	{
		$this->mPureBootstrap = true;
		$this->setWithFree();
	}
	
	function setDefCellAlign($xalign)
	{
		$this->mDefCellAlign = $xalign;
	}
	
	function setStyle($xstyle)
	{
		$this->mStyle = $xstyle;
	}
	
	function setWithFree()
	{
		$this->mwidth = "";
	}
	
	function setWithM()
	{
		$this->mwidth = "660";
	}
	
	function setTitulo($xtitulo)
	{
		$this->mtitulo = $xtitulo;
	}
	
	function setClass($xclass)
	{
		$this->mclass = $xclass;
	}
	
	function setClassGrillaDatos($xextraClass = "")
	{
		$this->setClass("sc3-grilla-datos w3-striped w3-hoverable $xextraClass");
	}
	
	function setUseFilters($xuseFilters = true)
	{
		$this->useFilters = $xuseFilters;
	}
	
	function setWithS()
	{
		$this->mwidth = "480";
	}
	
	function setWithL()
	{
		$this->mwidth = "850";
	}
	
	function setWithAll()
	{
		$this->mwidth = "99.7%";
	}
	
	function setValignTop()
	{
		$this->mvalign = "top";
	}
	
	function setCurrentRowColor($xcolor)
	{
		$this->mcurrentRowColor = $xcolor;
	}
	
	function setSizeF()
	{
		$this->mwidth = "";
	}
	
	function setTitulos($xatitulos)
	{
		$cantTitulos = 0;
		foreach ($xatitulos as $titulo)
		{
			$this->mtitulos[] = str_replace("_", "<br>", $titulo);
			$cantTitulos++;
			
			//agranda cantidad de columnas
			if ($cantTitulos > $this->mcantCols)
				$this->mcantCols = $cantTitulos;
		}
	}
	
	function setFooter($xafooter)
	{
		$this->mfooter = $xafooter;
	}
	
	function setColsStyle($xcols)
	{
		$this->mcols = $xcols;
	}
	
	function addFila($xafila)
	{
		//si tiene mas columnas agranda cantidad de columnas
		if (is_array($xafila) && count($xafila) > $this->mcantCols)
			$this->mcantCols = count($xafila);
		
		$this->mfilas[] = $xafila;
	}
	
	function setFilas($xaFilas)
	{
		$this->mfilas = $xaFilas;
	}
	
	function count()
	{
		return count($this->mfilas);
	}

	/**
	 * Agrega un separador tipo divisor de grupo
	 * @param unknown $xtitulo
	 */
	function addSeparador($xtitulo)
	{
		$celdaTit = array("class"=>"grid_grupo0", "align"=>"left", "valor"=>$xtitulo, "colspan"=>$this->mcantCols);
		$rowTit = array();
		$rowTit[] = $celdaTit;
		$this->addFila($rowTit);
	}
	
	function toHtml()
	{
		$res = "\n<table class=\"" . $this->mclass . "\" width=\"". $this->mwidth . "\" style=\"" . $this->mStyle . "\">";
		
		$res .= "<thead>";
		if (!esVacio($this->mtitulo))
		{
			$res .= "<tr><th class=\"grid_title\" align=\"center\" colspan=\"" . $this->mcantCols . "\">" . $this->mtitulo . "</th></tr>";
		}
		
		if (count($this->mtitulos) > 0)
		{
			$res .= "<tr>";
			$before = "<th align=\"center\">";
			$after = "</th>";
			$res .= $before .  implode($after . $before, $this->mtitulos) . $after;
			
			if ($this->useFilters)
			{
				$res .= "</tr><tr class=\"grid_filter\">";
				foreach ($this->mtitulos as $tit)
				{
					$res .= "<th align=\"center\" class=\"grid_filter\">";
					$res .= "<input name=\"filter\" id=\"filter\" size=\"4\" onkeyup=\"Table.filter(this,this)\" title=\"Ingrese un valor a filtrar\" />";
					$res .= "</th>";
				}	 
			}
			
			$res .= "</tr>";
		}
		
		$res .= "</thead>";
		$res .= "<tbody>";

		$currentRowColor = $this->mcurrentRowColor;
		foreach ($this->mfilas as $fila) 
		{
			$res .= "\r\n<tr";
			
			$res .= " >";
			foreach ($fila as $i=>$celda)
			{
				$align = $this->mDefCellAlign;
				$valign = "top";
				$colspan = "";
				$width = "";
				$class = "";
				$tdId = "";
				$valorCelda = $celda;
				if (is_array($celda))
				{
					if (isset($celda["align"]))
						$align = $celda["align"];
					if (isset($celda["valign"]))
						$valign = $celda["valign"];
					if (isset($celda["id"]))
						$tdId = $celda["id"];
							
					$valorCelda = $celda["valor"];
					
					$colspan = "";
					if (isset($celda["colspan"]))
						$colspan = $celda["colspan"];
					if (!esVacio($colspan))
						$colspan = " colspan=\"$colspan\" ";
					
					$class = "";
					if (isset($celda["class"]))
						$class = $celda["class"];
					if (!esVacio($class))
						$class = " class=\"$class\" ";
					
				}
				elseif (isset($this->mcols[$i]["align"]))
				{
					$align = $this->mcols[$i]["align"];
				}
				if (isset($this->mcols[$i]["width"]))
				{
					$width = " width=\"" . $this->mcols[$i]["width"] . "\"";
				}
				
				$res .= "\r\n\t<td align=\"$align\" valign=\"$valign\" id=\"$tdId\" $width $colspan $class>$valorCelda</td>";
			}
			$res .= "</tr>";
		}
		
		$res .= "</tbody>";
		$res .= "<tfoot>";
		
		$before = "<th align=\"right\">";
		$after = "</th>";
		if (sizeof($this->mfooter) > 0)
			$res .= $before .  implode($after . $before, $this->mfooter) . $after;
		else
		{
			$res .= "";
		} 	      	
		$res .= "</tfoot>";
		$res .= "</table>";
		return $res;
	}
}


?>