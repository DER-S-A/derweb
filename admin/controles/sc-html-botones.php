<?php


class HtmlBotonSmall
{
	var $murl = "";
	var $mimg = "";
	var $mayuda = "";
	var $imgId = "";
	var $botonClass = "btn-action";
	var $faSize = "fa-lg";
	var $faColor = "w3-text-green";
	
	function __construct($xurl, $ximg = "fa-cogs", $xayuda = "", $ximgId = "")
	{
		$this->murl = $xurl;
		$this->mimg = $ximg;
		$this->mayuda = $xayuda;
		$this->imgId = $ximgId;
	}
	
	function setFaSize($xsize)
	{
		$this->faSize = $xsize;
	}
	
	function setBotonExito()
	{
	    $this->botonClass = "btn-success"; 
	}
	
	function setBotonWarning()
	{
	    $this->botonClass = "btn-warning";
	}
	
	function toHtml($xmostrarTexto = false)
	{
		$title = $this->mayuda;
		$texto = "";
		if ($xmostrarTexto)
		{
			$texto = " " . $this->mayuda;
			$title = "";
		}
		
		$size = $this->faSize;	
		$icon = $this->mimg;
		$iconId = $this->imgId;
		$class = $this->botonClass;
		
		$res = "<button type=\"button\" name=\"bsubmit\" id=\"bsubmit\" class=\"btn-flat $class w3-button\" title=\"$title\" onclick=\"" . $this->murl . "\" >
							<i class=\"fa $icon fa-fw $size\" id=\"$iconId\"> </i> $texto
				</button>";
		
		return $res;
	}
}


class HtmlBotonToolbar
{
	var $moperacion;
	var $mquery;
	var $mmasterid;
	var $mrecord;
	var $mstackname;
	var $mactiva = true;
	var $mflat = false;
	var $mClass = "td_toolbar";
	var $inTable = true;
	var $mShowId = true;
	var $mEmergente = false;
	var $murl = "";

	function __construct($xoperacion, $xmquery, $xmid, $xrecord, $xstackname = "")
	{
		$this->moperacion = $xoperacion;
		$this->mquery = $xmquery;
		$this->mmasterid = $xmid;
		$this->mrecord = $xrecord;
		$this->mstackname = $xstackname;
	}

	function hideOpId()
	{
		$this->mShowId = false;
	}

	function setEmergente()
	{
		$this->mEmergente = true;
	}

	function setInTable($xintable)
	{
		$this->inTable = $xintable;
	}
	
	function setUrl($xurl)
	{
		$this->murl = $xurl;
	}

	function setActiva($xactiva)
	{
		$this->mactiva = $xactiva;
	}

	//en modo flat, no hay un enter entre el icono y el nombre de la accion
	function setFlat($xflat)
	{
		$this->mflat = $xflat;
		$this->setClass("td_toolbar_flat");
	}

	function setClass($xClass)
	{
		$this->mClass = $xClass;
	}

	function toHtml($xcodigoRapido = 0)
	{
		//para que se evalue la condicion del registro que espera $record["campo1"]==...
		$record = $this->mrecord;

		if ($this->mflat)
		{
			$separador = " ";
		}
		else
		{
			$separador = "<br />";
		}

		$result = "\r\n";
		if ($this->inTable)
			$result = "<td ";

		//arma url + icono con la info que posee
		if (strcmp($this->murl, "") == 0)
		{
			$opid = "";
			if (isset($this->moperacion["id"]))
				$opid = $this->moperacion["id"];
				
			$url = new HtmlUrl($this->moperacion["url"]);
			$url->add("stackname", $this->mstackname);
			
			if (!startsWith($url->toUrl(), "javascript"))
			{
				$url->add("mquery", $this->mquery);
				$url->add("mid", $this->mmasterid);
				$url->add("opid", $opid);
			}
			
			$target = "";
			if (isset($this->moperacion["target"]))
				$target = $this->moperacion["target"];
			if (strcmp($target, "") != 0)
				$target = " target=\"" . $target . "\" ";
			
			$condicion = "";
			if (isset($this->moperacion["condicion"]))
				$condicion = $this->moperacion["condicion"];
			if (strcmp($condicion, "") == 0)
				$condicion = true;
			else
				eval($condicion);

			$classDisable = "";
			if (!$condicion || !$this->mactiva)
				$classDisable = "opDisable";
				
			$idBoton = "";
			if ($xcodigoRapido > 0)
				$opid = $xcodigoRapido;

			$codMostrar = "";
			if ($this->mShowId && $opid != "")	
			{
				$idBoton = "op" . $opid;
				$codMostrar = "[$opid]";
			}

			//si no está en una tabla, el href tiene la clase
			$hRefClass = $classDisable;	
			if (!$this->inTable)
				$hRefClass .= " " . $this->mClass;
				
			if ($condicion && $this->mactiva)
			{
				if ($this->inTable)
					$result .= " class=\"$classDisable " . $this->mClass . "\">";
				
				if (!$this->mEmergente)
				{
					$result .= "<a href=\"" . $url->toUrl();
					$result .= "\"";
					$result .= " class=\"$hRefClass\"";
					$result .= $target;
				}
				else
				{
					//emergente
					$result .= "<a href=\"javascript:openWindow('" . $url->toUrl();
					$result .= "', 'opop');\"";
					$result .= " class=\"$hRefClass\"";
				}
				
				$result .= " id=\"$idBoton\" ";
				$result .= " title=\"" . htmlVisible($this->moperacion["ayuda"]) . " $codMostrar\" ";
				$result .= ">";
			}
			else
			{
				if ($this->inTable)
					$result .= " class=\"td_toolbar2\">";
				else 					
				{
					$result .= "<span class=\"$classDisable " . $this->mClass . "\">";
				}					
			}
			
			$result .= img($this->moperacion["icon"], $this->moperacion["ayuda"], "", "", "");
			$result .= $separador;
			$result .= htmlVisible($this->moperacion["nombre"]);
		
			if ($condicion && $this->mactiva)
				$result .= "</a>";
			else 
				$result .= "</span>";
		}
		else
		{
			//hay info de url externa
			if ($this->inTable)
				$result .= " class=\"" . $this->mClass . "\">";
			$result .= $this->murl;
		}
		if ($this->inTable)
			$result .= "</td>";
		return $result;
	}
}



class HtmlBotonToolbar2
{
    var $moperacion;
    var $mquery;
    var $mmasterid;
    var $mrecord;
    var $mstackname;
    var $mactiva = true;
    var $mClass = "toolboton";
    var $mEmergente = false;
    var $murl = "";
    
    function __construct($xoperacion, $xmquery, $xmid, $xrecord, $xstackname = "")
    {
        $this->moperacion = $xoperacion;
        $this->mquery = $xmquery;
        $this->mmasterid = $xmid;
        $this->mrecord = $xrecord;
        $this->mstackname = $xstackname;
    }
    
    function setEmergente()
    {
        $this->mEmergente = true;
    }
    
    function setUrl($xurl)
    {
        $this->murl = $xurl;
    }
    
    function setActiva($xactiva)
    {
        $this->mactiva = $xactiva;
    }
    
    function setClass($xClass)
    {
        $this->mClass = $xClass;
    }
    
    
    function toHtml($xcodigoRapido = 0)
    {
        //para que se evalue la condicion del registro que espera $record["campo1"]==...
        $record = $this->mrecord;
        
        $separador = "<br />";
        $result = "\r\n";
            
        //arma url + icono con la info que posee
        if (strcmp($this->murl, "") == 0)
        {
            $opid = "";
            if (isset($this->moperacion["id"]))
                $opid = $this->moperacion["id"];
                    
            $url = new HtmlUrl($this->moperacion["url"]);
            $url->add("stackname", $this->mstackname);
            
            if (!startsWith($url->toUrl(), "javascript"))
            {
                $url->add("mquery", $this->mquery);
                $url->add("mid", $this->mmasterid);
                $url->add("opid", $opid);
            }
            
            $target = "";
            if (isset($this->moperacion["target"]))
                $target = $this->moperacion["target"];
            
            if (strcmp($target, "") != 0)
                $target = " target=\"" . $target . "\" ";
                            
            $condicion = "";
            if (isset($this->moperacion["condicion"]))
                $condicion = $this->moperacion["condicion"];

            if (strcmp($condicion, "") == 0)
                $condicion = true;
            else
                eval($condicion);
                                        
            $classDisable = "";
            if (!$condicion || !$this->mactiva)
                $classDisable = "opDisable";
                                            
            $codigoRapido = "";
            if ($xcodigoRapido > 0)
                $opid = $xcodigoRapido;
                                                
            //si no est� en una tabla, el href tiene la clase
            $class = $classDisable;
            $class .= " " . $this->mClass;

            $result .= "<div class=\"$class\">";
            
            if ($condicion && $this->mactiva)
            {
                if (!$this->mEmergente)
                {
                    $result .= "<a href=\"" . $url->toUrl();
                    $result .= "\"";
                    $result .= $target;
                }
                else
                {
                    //emergente
                    $result .= "<a href=\"javascript:openWindow('" . $url->toUrl();
                    $result .= "', 'opop');\"";
                }
                                                                    
                $result .= " id=\"op" . $opid . "\" ";
                $result .= " title=\"" . htmlVisible($this->moperacion["ayuda"]) . "\" ";
                $result .= ">";
            }
            else
                $result .= "<span>";
                                                        
            $result .= img($this->moperacion["icon"], $this->moperacion["ayuda"], "", "", "");
            $result .= $separador;
            $result .= htmlVisible($this->moperacion["nombre"]);
            
            if ($condicion && $this->mactiva)
                $result .= "</a>";
            else
                $result .= "</span>";
        }
        else
        {
            //hay info de url externa
            $result .= $this->murl;
        }
        $result .= "</div>";
        return $result;
    }
}

class HtmlBotonOkCancel
{
	var $showOk = true;
	var $showCancel = true;
	var $enviar = 1;
	var $f8 = 1;
	var $cancelLink = "hole.php?anterior=0";
	var $label = "Aceptar";
	var $beforeCancel = "";
	
	function __construct($xshowOk = true, $xshowCancel = true)
	{
		$this->showOk = $xshowOk;
		$this->showCancel = $xshowCancel;
	}

	function setLabelSiguiente()
	{
		$this->label = "Siguiente";
	}

	/**
	 * Que se ejecuta antes de cerrar la ventana actual
	 */
	function setBeforeCancel($xBeforeCancel)
	{
		$this->beforeCancel = $xBeforeCancel;
	}

	function setLabel($xlabel)
	{
	    $this->label = $xlabel;
	}
	
	function setF8($xshowF8)
	{
		$this->f8 = $xshowF8;
	}
	
	function setEnviar($xenviar)
	{
		$this->enviar = $xenviar;
	}

	function setCancelLink($xlink)
	{
		$this->cancelLink = $xlink;
	}
	
	function toHtml($xerror = "")
	{
		$enviar = $this->enviar;
		$result = "\n<input name=\"enviar\" type=\"hidden\" id=\"enviar\" value=\"$enviar\" />";
		if ($this->showOk && esVacio($xerror))
		{
			$result .= "\n<button type=\"button\" name=\"bsubmit\" id=\"bsubmit\" class=\"btn btn-success\" onclick=\"return submitForm();\" title=\"[F8] - Guardar cambios\">
							<i class=\"fa fa-check fa-fw fa-lg\"> </i> Aceptar
						</button>";
		}
		if ($this->showCancel)
		{
			$urlCancel = new HtmlUrl($this->cancelLink);
			$stackname = Request("stackname");
			if (!esVacio($stackname))
				$urlCancel->add("stackname", $stackname);

			$beforeJs = "";
			if (!esVacio($this->beforeCancel))
				$beforeJs = $this->beforeCancel . ";";

			$result .= "\n<button type=\"button\" name=\"bcancelar\" class=\"btn btn-warning\" onclick=\"" . $beforeJs . "javascript:document.location='" . $urlCancel->toUrl() ."'\" >
							<i class=\"fa fa-undo fa-fw fa-lg\"> </i> Cancelar
						</button>";
		}
		return $result;
	}
}


?>