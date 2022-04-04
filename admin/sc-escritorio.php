<?php

/*
Clase que maneja las ultimas operaciones realizadas para ser mostradas en el escritorio
*/
class ScEscritorio
{
	var $aQuerys = [];
	var $aOperaciones = [];
	var $MAX = 8;

	function __construct()
	{

	}

	/**
	 * Agrega una consulta al escritorio
	 */
	function addQuery($xquery_info, $xfilter, $xfiltername)
	{
		if (!$this->existsQuery($xquery_info["queryname"], $xfilter))
		{
			if (count($this->aQuerys) >= $this->MAX)
			{
				array_shift($this->aQuerys);
			}
			$aQuery = [];
			$aQuery["QN"] = $xquery_info["queryname"];
			$aQuery["QD"] = $xquery_info["querydescription"];
			$aQuery["ICON"] = $xquery_info["icon"];
			$aQuery["FID"] = $xfilter;
			$aQuery["FNAME"] = $xfiltername;
			$aQuery["PALABRA"] = "";
			
			$this->aQuerys[] = $aQuery;
		}
	}


	/**
	 * Agrega una operacion al escritorio
	 */
	function addOp($xopid, $xopname, $xoplink, $xopicon, $xophelp)
	{
		debug("ScEscritorio:addOp($xopid, $xopname, $xoplink, $xopicon, $xophelp)");
		if (count($this->aOperaciones) >= $this->MAX)
		{
			array_shift($this->aOperaciones);
		}
		$aOp = [];
		$aOp["NAME"] = $xopname;
		$aOp["LINK"] = $xoplink;
		$aOp["ICON"] = $xopicon;
		$aOp["HELP"] = $xophelp;
		$aOp["ID"] = $xopid;
		
		$this->aOperaciones[] = $aOp;
	}


	//arma el menu con las ultimas consultas visitadas desde el menú 
	function showQuerys($xmotrarVacio = true, $xvertical = true, $xclass = "", $xactiveQuery = "", $xstackName = "")
	{
		$str = "";
		//invierte el menú para mostrar arriba las últimas consultadas
		$auxquerys =  array_reverse($this->aQuerys);
		if ((sizeof($this->aQuerys) == 0) && $xmotrarVacio)
		{
			$str .= "\n<div class=\"$xclass\">(sin historia)</div>";
		}

		if (count($auxquerys) == 1 && !$xvertical)
			return "";

		$largoBoton = 22;
		if (count($auxquerys) >= ($this->MAX - 1))	
			$largoBoton = 16;

		foreach ($auxquerys as $i => $query)
		{
			$class = $xclass;
			if (sonIguales($query["QN"], $xactiveQuery))
			{				
				$class .= " $xclass-activo";
			}

			//ver si vamos a mostrar el último dato accedido
			if (0)
			{
				//recupera el íltimo id visto
				$lastId = (int) getSession("sc3-last-" . $query["QN"]);
				if ($lastId != 0)
				{
					$str .= "<a href=\"" . $this->urlVerUltimo($query["QN"], $lastId) . "\">";
					$str .= imgFa("fa-hand-o-right", "fa-lg", "gris") . "</a>";
				}
				else
					$str .= espacio();
			}
					
			$record = Array();
			
			$url = new HtmlUrl("sc-selitems.php");
			$url->add("query", $query["QN"]);
			$url->add("fstack", "1");
			$url->add("stackname", $xstackName);
			if ($query["FID"] != 0)
			{
				$url->add("filter", $query["FID"]);
				$url->add("filtername", $query["FNAME"]);
			}
			$url->add("palabra", $query["PALABRA"]);
			
			$op = array();
			$op["url"] = $url->toUrl();
			$op["icon"] = $query["ICON"];
			if (sonIguales($op["icon"], ""))
					$op["icon"] = "images/table.png";	

			$qdesc = $query["QD"];
			//analiza si hay filtro guardado para concatenar al nombre de la op
			if ($query["FID"] != 0)
				$qdesc .= " (" .  $query["FNAME"] . ")";
			
			if (!$xvertical)
				$qdesc = substr($qdesc, 0, $largoBoton);
						
			$op["ayuda"] = $qdesc;
			$op["nombre"] = $qdesc;

			$boton = new HtmlBotonToolbar($op, "", "", $record);
			$boton->setFlat(true);
			$boton->setInTable(false);
			$boton->setClass($class);
			$str .= $boton->toHtml();							
		}
	
		return $str;
	}


	/**
	 * Arma menú
	 * @param string $xmotrarVacio
	 * @param string $xvertical
	 * @param string $xmostrarUlt
	 * @param string $xclass
	 * @param string $xskipQuery
	 * @return string
	 */
	function showMenu($xicon = "fa-cogs", $xskipQuery = "", $xButtonClass = "")
	{
		//invierte el menú para mostrar arriba las últimas consultadas
		$auxquerys =  array_reverse($this->aQuerys);
		if (count($this->aQuerys) <= 1)
		{
			return "";
		}

		$menu = new HtmlMenu2("historia", "Mi Historial", $xButtonClass);
		$menu->setIcon($xicon);
	
		//para no repetir query (ni con filtros)
		$aQuerys = array();
		
		foreach ($auxquerys as $i => $query)
		{
			$queryname = $query["QN"];
			
			if (!sonIguales($queryname, $xskipQuery) && !in_array($queryname, $aQuerys))
			{
				$aQuerys[] = $queryname;
				
				$url = new HtmlUrl("sc-selitems.php");
				$url->add("query", $queryname);
				$url->add("fstack", "1");
				if ($query["FID"] != 0)
				{
					$url->add("filter", $query["FID"]);
					$url->add("filtername", $query["FNAME"]);
				}
	
				$op = array();
				$op["icon"] = $query["ICON"];
				if (sonIguales($op["icon"], ""))
					$op["icon"] = "images/table.png";
	
				$qdesc = $query["QD"];
				//analiza si hay filtro guardado para concatenar al nombre de la op
				if ($query["FID"] != 0)
					$qdesc .= " (" .  $query["FNAME"] . ")";
	
				$qdesc = substr($qdesc, 0, 35);
				$menu->add($qdesc, $op["icon"], $url->toUrl());
				
				//recupera el �ltimo id visto
				$lastId = (int) getSession("sc3-last-" . $queryname);
				if ($lastId != 0)
				{
					$url1 = new HtmlUrl($this->urlVerUltimo($queryname, $lastId));
					$menu->add(substr(getSession("sc3-last-" . $queryname . "-desc"), 0, 18), "fa-hand-o-right", $url1->toUrl(), "", "dropdown-content-submenu"); 
				}
			}
		}

		return $menu->toHtml();
	}

	
	
	function urlVerUltimo($xquery, $xid)
	{
		$result = "sc-viewitem.php";
		$result .= "?query=$xquery&registrovalor=$xid&fstack=1";
		return $result;
	}
	
	/**
	 * Muestra los favoritos
	 * @param BDObject $xrs
	 * @return string
	 */
	function showFavoritos($xrs)
	{
		$str = "";
		//invierte el menu para mostrar arriba las ultimas consultadas
		if ($xrs->EOF())
		{
			return "";
		}

		while (!$xrs->EOF())
		{
			$str .= "\n<div class=\"boton\">";

			$tipo = $xrs->getValue("tipo");
			if (sonIguales($tipo, "Q"))
			{
				$record = Array();
			
				$url = new HtmlUrl("sc-selitems.php");
				$url->add("query", $xrs->getValue("queryname"));
				$url->add("todesktop", "1");
				$valor2 = explode("--", $xrs->getValue("valor2"));
				if ((int) $valor2[0] != 0)
				{
					$url->add("filter", $valor2[0]);
					$url->add("filtername", $valor2[1]);
				}
				
				$op = array();
				$op["url"] = $url->toUrl();
				$op["icon"] = $xrs->getValue("icon");
				if (sonIguales($op["icon"], ""))
						$op["icon"] = "images/table.png";	

				$qdesc = $xrs->getValue("querydescription");
				//analiza si hay filtro guardado para concatenar al nombre de la op
				if ($valor2[0] != 0)
					$qdesc .= " (" .  $valor2[1] . ")";

				$op["ayuda"] = $qdesc;
				$op["nombre"] = $qdesc;

				$boton = new HtmlBotonToolbar($op, "", "", $record);
				$boton->setInTable(false);
				$boton->setClass("boton");
				$str .= $boton->toHtml();

				$cache = getCache();
				$qinfo = getQueryObj($xrs->getValue("queryname"));
				$cant = $qinfo->getRecordCountFilter($valor2[0]);
				$str .= "<br><font size=\"4\"><b>$cant</b></font>";
			}
			else
			{
				$url = new HtmlUrl($xrs->getValue("queryname"));
				$url->add("opid", $xrs->getValueInt("id"));
				$item = $xrs->getValue("querydescription");
				$icon = $xrs->getValue("icon");
				$target = $xrs->getValue("target");
				if (esVacio($target))
				{
					$target = "contenido";
					$url->add("fstack", "1");
				}
				
				$stackname = "op_" . lcfirst(escapeJsNombreVar($item));	

				$str .= href(img($icon, $item) . " " . $item, $url->toUrl(), $target, "", "td_toolbarIzq");
			}
			$str .= "</div>";
			$xrs->Next();
		}
	
		return $str;
	}
	
	/**
	 * determina si existe el query en el escritorio
	 */
	function existsQuery($xquery, $xfilter)
	{
		foreach ($this->aQuerys as $i => $query)
		{
			if (sonIguales($query["QN"], $xquery) && $xfilter == $query["FID"])
				return true;	
		}
	
		return false;
	}

	/**
	 * Agrega una palabra de búsqueda al query dado
	 */
	function setQuerySearch($xquery, $xfilter, $xpalabra)
	{
		foreach ($this->aQuerys as $i => $query)
		{
			if (sonIguales($query["QN"], $xquery) && $xfilter == $query["FID"])
			{
				$this->aQuerys[$i]["PALABRA"] = $xpalabra;
				return true;
			}
		}

	}

	
} //fin clase




function initEscritorio()
{
	$desk = new ScEscritorio();
	saveEscritorio($desk);
}

/**
 * Retorna el objeto ScEscritorio actual
 *
 * @return ScEscritorio
 */
function getEscritorio()
{
	if (!isset($_SESSION["_DESK"])) 
	{
		initEscritorio(); 
	}	
	
	$desk = new ScEscritorio();
	$desk = unserialize($_SESSION["_DESK"]);
	return $desk;		
}

function saveEscritorio($xdesk)
{
	$_SESSION["_DESK"] = serialize($xdesk);
}

?>