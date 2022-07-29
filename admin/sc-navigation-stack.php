<?php

/**
 * Pila de navegacion del sistema
 */
class NavigationStack
{
	var $aStack = [];
	var $filesToDelete = [];

	function __construct()
	{
		$this->flushStack();
	}
	
	/**
	 * Vacía pila de navegación
	 */
	function flushStack()
	{
		$this->aStack = [];
		
		$this->deleteTmpFiles();
	}

	function addFileToDelete($xfilename)
	{
		$this->filesToDelete[] = $xfilename;
	}

	/*
	 * Borra los pdfs generados en /tmp
	 */
	function deleteTmpFiles()
	{
		//lo hace al login
		return;

		foreach ($this->filesToDelete as $filename)
		{
			if (file_exists($filename))
			{
				$error = error_reporting(0);
				unlink($filename);
				error_reporting($error);
			}
		}
		$this->filesToDelete = Array();
	}
	
	/**
	 * Retorna la cantidad de elementos de la pila
	 * @return int
	 */
	function getCount()
	{
		return sizeof($this->aStack);
	}
	
	/**
	 * Retorna la posición del URL y el key dentro de la pila
	 * -1 si no es encontrado
	 */
	function posUrl($xurl, $xkey)
	{
		$i = 0;
		while ($i < sizeof($this->aStack))
		{
			if (($this->aStack[$i]["URL"] == $xurl) && ($this->aStack[$i]["KEY"] == $xkey))
				return $i;
			$i++;
		}
		return -1;
	}

	function addCurrentKey($xkey = "")
	{
		$this->add($_SERVER['PHP_SELF'], $xkey, $_SERVER['QUERY_STRING']);
	}

	/**
	 * Agrega un URL a la pila
	 */
	function add($xurl, $xkey, $xqs)
	{
		debug(" NavigationStack:add($xurl, $xkey, $xqs)");
		$i = $this->posUrl($xurl, $xkey);
		if ($i < 0)
		{
			$this->aStack[] = ["URL" => $xurl, "KEY" => $xkey, "QS" => $xqs];
		}
		else
		{
			//tratando de verificar si es una navegación circular, ej: Alquileres / Alquileres (2) / Propiedad (15) / Alquileres
			//y entonces dejar: Propiedad (15) / Alquileres
			if (($i == 0) && (sizeof($this->aStack) > 2))
			{
				//corre hacia atrás todos menos uno
				while (sizeof($this->aStack) > 1)
				{
					array_shift($this->aStack);
				}
								
				$this->aStack[] = ["URL" => $xurl, "KEY" => $xkey, "QS" => $xqs];
			}			
			else
			{
				//si ya existe, reemplaza el QS y desapila todo lo superior
				$this->aStack[$i]["QS"] = $this->mergeParameters($this->aStack[$i]["QS"], $xqs);
			}
		}
	}

	/**
	* Agrega el nombre de fantasia y el icono
	*/
	function addExtraInfo($xname, $xicon)
	{
		$this->aStack[sizeof($this->aStack) - 1]["NAME"] = $xname;
		$this->aStack[sizeof($this->aStack) - 1]["ICON"] = $xicon;
	}

	/**
	* Retorna el valor el parametro $xparam en el array $avalores
	*/
	function valorParametro($avalores, $xparam)
	{
		//debug("valorParametro($avalores, $xparam)");
		$i = 0;
		while ($i < count($avalores))
		{
			$pv = explode("=", $avalores[$i]);
			if (strcmp($pv[0], $xparam) == 0)
			{
				//está el parámetro, pero con valor vacío
				if (strcmp($pv[1], "") == 0)
					return "FLUSH";
				else
					return $pv[1];
			}	
			$i++;
		}
		return "";
	}

	/*
	Hace un merge de los parametros, manteniendo los viejos y "pisando"
	los nuevos valores.
	Ej: p1=3&p2=98, p2=99&p3=15 --> p1=3&p2=99&p3=15
	*/
	function mergeParameters($xactual, $xnuevo)
	{
		debug(" NavigationStack:mergeParameters($xactual, $xnuevo)");
		$aqs = [];
		
		//analisis por si hubo flush stack y el xacutal viene con la raiz, ej: sc-selitems.php?p1=......................
		$urlRaiz = "";
		$aActualPartes = explode("?", $xactual);
		if (count($aActualPartes) > 1)
		{
			$urlRaiz = $aActualPartes[0] . "?";
		}
		
		$aviejos = explode("&", $xactual);
		$anuevos = explode("&", $xnuevo);

		//$aqs = array_merge($aviejos, $anuevos);
		
		if (count($aviejos) <= 0)
			return $xnuevo;

		if (count($anuevos) <= 0)
			return $xactual;

		//de todos los parametros viejos, busca un nuevo valor
		$i = 0;
		$iqs = 0;
		while ($i < count($aviejos))
		{
			$p1 = explode("=", $aviejos[$i]);
			//si hay nuevo valor, gana ese
			$valorNuevo = $this->valorParametro($anuevos, $p1[0]);
			if (strcmp($valorNuevo, "") != 0)
			{
				if (strcmp($valorNuevo, "FLUSH") != 0)
				{
					$aqs[$iqs] = $p1[0] . "=" . $valorNuevo;
					$iqs++;
				}
			}
			else	
			{
				if (strcmp($p1[1], "") != 0)
				{
					$aqs[$iqs] = $p1[0] . "=" . $p1[1];
					$iqs++;
				}
			}	
			$i++;
		}
		
		$i = 0;
		while ($i < count($anuevos))
		{
			$p2 = explode("=", $anuevos[$i]);
			$valorViejo = $this->valorParametro($aviejos, $p2[0]);
			//no estaba en la vieja colecci�n
			if (strcmp($valorViejo, "") == 0)
			{
				if (strcmp($p2[1], "") != 0)
				{
					$aqs[$iqs] = $p2[0] . "=" . $p2[1];
					$iqs++;	
				}
			}
			$i++;
		}
		return implode("&", $aqs);	
	}

	function addCurrent()
	{
		$this->addCurrentKey("");
	}

	/**
	 * Retorna si la pila está vacía
	 */
	function vacia()
	{
		if (sizeof($this->aStack) > 0)
			return false;
		else	
			return true;	
	}
	
	function getUrlTope()
	{
		debug(" NavigationStack:getUrlTope()");
		
		if (!$this->vacia())
		{
			$tope = sizeof($this->aStack) - 1;
			return $this->aStack[$tope]["URL"] . "?" .$this->aStack[$tope]["QS"];
		}
		return "";
	}

	function getKeyTope()
	{
		debug(" NavigationStack:getKeyTope()");
		
		if (!$this->vacia())
		{
			$tope = sizeof($this->aStack) - 1;
			return $this->aStack[$tope]["KEY"];
		}
		return "";
	}
	
	/**
	 * Saca el útimo de la pila
	 */
	function desapilar()
	{
		if (!$this->vacia())
			array_pop($this->aStack);
	}

	function debug()
	{
		$i = 0;
		echo("<br>NavigationStack() -> stack trace:");
		while ($i < sizeof($this->aStack))
		{
			echo("<br>$i) " . $this->aStack[$i]["KEY"] . " > " . $this->aStack[$i]["URL"] . "?" .$this->aStack[$i]["QS"]);
			$i++;
		}
		echo("<br>");
	}
	

	/**
	* Navega al tope de la pila
	*/
	function gotoTope()
	{
		debug(" NavigationStack:gotoTope()");
		
		//evita el eterno ciclo de ir al tope
		if (strcmp(Request("fmerge"), "1") != 0)
		{
			$loc = $this->getUrlTope();
			$loc = $this->mergeParameters($loc, "fmerge=1");
			debug(" NavigationStack:gotoTope(): $loc");
			header("Location:" . $loc);
			exit;
		}	
	}
	
	/**
	* Muestra la pila para orientarse y regresar
	*/
	function showNavigation($xminCount = 2)
	{
		if (sizeof($this->aStack) < $xminCount)
			return "";
		$i = 0;
		$menu = "<table class=\"tabla_stack\" align=\"left\"><tr>";
		$menu .= "<td class=\"td_stack\"><a href=\"hole.php?fstack=1\"><i class=\"fa fa-desktop fa-lg\"></i></a></td>";
		while ($i < sizeof($this->aStack))
		{
			$class = "td_stack";
			if ($i < sizeof($this->aStack) - 1)
					$class = " w3-hide-small ";
			$menu .= "<td class=\"td_stack\" width=\"5\">" . img("images/separador.gif", "") . "</td>";
			$menu .= "<td class=\"$class\">";
			
			$nombre = "";
			if (isset($this->aStack[$i]["NAME"]))
				$nombre = $this->aStack[$i]["NAME"];
			if ($i < (sizeof($this->aStack) - 1))
                $nombre = ""; 
			
			$url = $this->aStack[$i]["URL"] . "?" .$this->aStack[$i]["QS"];
			$urld = img($this->aStack[$i]["ICON"], $this->aStack[$i]["NAME"]) . " ";
			if ($i > 0 || $xminCount == 0)
				$urld .= $nombre;
			if ($i == sizeof($this->aStack) - 1)
				$menu .= $urld;
			else	
				$menu .= href($urld, $url);
			$menu .= "</td>";
			$i++;
		}
		$menu .= "</tr></table>";
		return $menu;
	}

}

function initStack($xstackname = "")
{
	$stack = new NavigationStack();
	saveStack($stack, $xstackname);
}

/**
 * Recupera el stack de navegacion
 * @param $xstackname Opcional, nombre de la pila. Vacia para todos salvo que est� navegando en solapa aparte
 * @return NavigationStack
 */
function getStack($xstackname = "")
{
	if (!isset($_SESSION["_NS" . $xstackname])) 
	{
		initStack($xstackname); 
	}	
	
	$stack = new NavigationStack();
	$stack = unserialize($_SESSION["_NS" . $xstackname]);
	return $stack;		
}

function saveStack($xstack, $xstackname = "")
{
	$_SESSION["_NS" . $xstackname] = serialize($xstack);
}

//Lo tipico de cualquier pagina apilable
function stackIt($xstackname = "")
{
	$stack = getStack($xstackname);
	$stack->addCurrent();
	saveStack($stack, $xstackname);
}


//Si estA el parametro fstack=1, vacia la pila
if (RequestInt("fstack") == 1)
{
	$stackN = Request("stackname");
	$stack = getStack($stackN);
	$stack->flushStack();
	saveStack($stack, $stackN);
}
