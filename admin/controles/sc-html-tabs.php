<?php 


/**
 * Arma los tabs
 *
 */
class HtmlTabber
{
	var $mcrearTabla = 0;

	function setCrearTabla($xcrearTabla)
	{
		$this->mcrearTabla = $xcrearTabla;
	}

	/*
	 comienza un Tab
	*/
	function startTabs()
	{
		$str = "";
		if ($this->mcrearTabla)
		{
			$str .= "<tr><td colspan=\"2\">";
			//crea un ID unico para que se guarde el cookie con el tab seleccionado
			$str .= "\n<div class=\"tabber\" id=\"" . getPageId() . "\">";
		}
		else
			$str .= "\n<div class=\"tabber\" id=\"" . getPageId() . "\">";
		return $str;
	}

	function endTabs()
	{
		$str = "";
		if ($this->mcrearTabla)
		{
			$str .= "</div></td></tr>";
		}
		else
			$str .= "\n</div>\n";
		return $str;
	}

	/*
	 Comienza una solapa con un titulo dado y un icono
	*/
	function startSolapa($xtitulo, $xicono = "")
	{
		$str = "";
		$str .= "\n<div class=\"tabbertab\">";
		$str .= "<h2>";
		if (!sonIguales($xicono, ""))
			$str .= "<img border=\"0\" src=\"$xicono\" /> ";
		$str .= htmlVisible($xtitulo) . "</h2>\n";
		if ($this->mcrearTabla)
			$str .= "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
		return $str;
	}

	function endSolapa()
	{
		if ($this->mcrearTabla)
			return "\n</table></div>\n";
		return "\n</div>\n";
	}


	function writeHeadToDocument()
	{
		return "<script>
				var tabberOptions = {manualStartup:true};
				</script>
				<script>
				document.write('<style type=\"text/css\">.tabber{display:none;}<\/style>');
				</script>";
	}

	function writeToDocument()
	{
		return "<script>
				tabberAutomatic(tabberOptions);
				</script>";
	}
}


/**
 * Tabs reslizados con divs, siguiendo w3school
 */
class HtmlTabs2
{
    var $mSolapas = array();
    var $mid = "";
	var $mAltura = "";
	//si se puede convertir cuando la pantalla es grande
	var $mConvertible = false;
    
    function __construct()
    {
        $this->mid = "t" . rand(200, 999);
	}
	
	function setConvertible($xconvert = true)
	{
		$this->mConvertible = $xconvert;
	}

    function getID()
    {
        return $this->mid;
    }
    
	function setId($xid)
	{
		$this->mid = $xid;
	}

    function agregarSolapa($xEtiqueta, $xIcono, $xContenido, $xConPadding = true)
    {
        $solapa = array($xEtiqueta, $xIcono, $xContenido, $xConPadding);
        $this->mSolapas[] = $solapa;
    }

    function setHeight($xAltura)
    {
        $this->mAltura = $xAltura;
    }

    function getHeight()
    {
        return $this->mAltura;
    }
    
    function toHtml()
    {
		$estiloBotones = "ocultar-grande";
		$estiloTituloInterior = "mostrar-grande";
		$estiloSolapas = "solapa-convertible";
		//si no es convertible, siempre será solapa
		if (!$this->mConvertible)
		{
			$estiloBotones = "";
			$estiloTituloInterior = "oculto";
			$estiloSolapas = "";
		}

        $result = "\n<div class=\"w3-container sc3-tabs\" id=\"" . $this->getID() . "\">";
        $result .= "<div class=\"w3-bar $estiloBotones\">";
        $icono = "";
        for ($i = 0; $i < count($this->mSolapas); $i++) 
        {
            if ($i == 0)
            {
                $clase = "solapaActiva";
            }
            else
            {
                $clase = "";
            }

			$icono = $this->mSolapas[$i][1];
            if ($icono != "")
            {  
                if (esIconFontAwesome($icono))
                {
                	$icono ="<i class=\"fa fa-fw fa-lg " . $icono . "\"></i>";
				}
				else
                {
                	$icono = "<img src=\"" . $icono . "\">";
                }
            }

			$result .= "\n<a class=\"w3-bar-item botonSolapa botonSolapa" . $this->getID() . " $clase \" onclick=\"openSolapa(event, '" . $this->getID() ."', '" . $i . "');\" > $icono " . $this->mSolapas[$i][0] . "</a>";
		}
		
        $result .= "</div>";
		$padding = "";
		
		for ($j = 0; $j < count($this->mSolapas); $j++) 
		{
			$id = $this->getID() . $j;

			$padding = ""; 
            if ($this->mSolapas[$j][3] == true)
                $padding = "w3-padding";

			$display = "";
            if ($j != 0)
				$display = "display:none;";

			$result .= "\n<div class=\"$padding w3-white solapa $estiloSolapas solapa" . $this->getID() . "\" id=\"" . $id ."\" style=\"$display height:". $this->getHeight() ."\">";
			$result .= "<div class=\"tabs-titulo $estiloTituloInterior\">";
			$result .= $this->mSolapas[$j][0];
			$result .= "</div>";
			$result .= $this->mSolapas[$j][2];
			$result .= "</div>";
		}
		
        $result .= "</div>";
        return $result;
    }
    
}



?>