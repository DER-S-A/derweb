<?php


/**
 * Menú desplegable
 */
class HtmlMenu
{
	var $mqueryname = "";
	var $mid = "";
	var $mtitulo = "";
	var $aops = array(); 
	
	function __construct($xid = "", $xtitulo = "", $xquery = "")
	{
		$this->mid = $xid;
		if (esVacio($xid))
			$this->mid = "" . getClave(4);
		$this->mtitulo = $xtitulo;
		$this->mqueryname = $xquery;
	}
	
	function getId()
	{
		return $this->mid;
	}
	
	function getTitulo()
	{
		return $this->mtitulo;
	}
	
	function add($xtitulo, $xicono, $xurl, $xtarget = "")
	{
		$this->aops[] = array('titulo' => $xtitulo, 'icono' => $xicono, 'url' => $xurl, 'target' => $xtarget);
	}
	
	function toHtml()
	{
		$str = "<div>	
					<a href=\"#\" class=\"anchorclass\" title=\"Men&uacute; de herramientas\" rel=\"submenu" . $this->getId() ."[click]\"  rev=\"lr\">
							<i class=\"fa fa-cogs fa-lg fa-fw\"></i>
					</a>
				</div>";
		
		$str .= "\n\n<div id=\"submenu" . $this->getId() . "\"  class=\"anylinkcss\">";
		if (!esVacio($this->getTitulo()))
			$str .= "<div class=\"titulo_menu\">" . $this->getTitulo() . "</div>";
		$str .= "\r\n<ul>";
	
		foreach ($this->aops as $indexOp => $op)
		{
			$target = $op['target'];
			$url = $op['url'];
			$icono = $op['icono'];
			$titulo = $op['titulo'];
				
			$str .= "\n<li>";
			
			$str .= "<a href=\"" . $url . "\" target=\"$target\"";
			$str .= " title=\"$titulo\" ";
			$str .= ">";
				
			$str .= img($icono, $titulo);
			$str .= " $titulo";
			$str .= "</a>";
			
			$str .= "</li>";
		}
	
		$str .= "</ul>";
		$str .= "</div>\r\n";
	
		return $str;
	}
}

/**
 * Menú desplegable según w3school https://www.w3schools.com/howto/howto_js_dropdown.asp
 */
class HtmlMenu2
{
	var $mid = "";
	var $mTitulo = "";
	var $aops = array();
	var $mIcon = "fa-cogs fa-lg";
	var $mButtonClass = "";

	function __construct($xid = "", $xtitle = "", $xButtonClass = "")
	{
		$this->mid = $xid;
		$this->mTitulo = $xtitle;
		$this->mButtonClass = $xButtonClass;
		if (esVacio($xid))
			$this->mid = "" . getClave(4);
	}

	function setIcon($xicon)
	{
		$this->mIcon = $xicon;
	}
	
	function getId()
	{
		return $this->mid;
	}

	/**
	 * Agrega una opci�n al men�. Si Target = ventana, abre una ventana emergente
	 * @param string $xtitulo
	 * @param string $xicono
	 * @param string $xurl
	 * @param string $xtarget
	 */
	function add($xtitulo, $xicono, $xurl, $xtarget = "", $xstyle = "")
	{
		$this->aops[] = array('titulo' => $xtitulo, 'icono' => $xicono, 'url' => $xurl, 'target' => $xtarget, 'style' => $xstyle);
	}
	
	function toHtml()
	{
		/*
		  <div class="dropdown">
			  <button onclick="myFunction()" class="dropbtn">Dropdown</button>
			  <div id="myDropdown" class="dropdown-content">
			    <a href="#">Link 1</a>
			    <a href="#">Link 2</a>
			    <a href="#">Link 3</a>
			  </div>
		  </div> 
		*/
		
		$str = "\r\n<div class=\"dropdown " . $this->mButtonClass . "\">
					<button onclick=\"desplegarMenu('dropdown" . $this->mid . "');return false;\" class=\"dropbtn\" title=\"" . $this->mTitulo . "\">
						<i class=\"dropdown-icon fa " . $this->mIcon . "\"></i>
					</button>";

		$str .= "\r\n<div id=\"dropdown" . $this->mid . "\" class=\"dropdown-content\">";

		foreach ($this->aops as $indexOp => $op)
		{
			$menuItemClass = "dropdown-content-left";
			$style =  $op['style'];
			if (!esVacio($style))
				$menuItemClass = $style;
			
			$target = $op['target'];
			$url = $op['url'];
			$icono = $op['icono'];
			$titulo = $op['titulo'];

			$str .= "\r\n<span class=\"$menuItemClass\">";
			//abre en ventana emergente
			if (sonIguales($target, "ventana"))
				$str .= "<a href=\"javascript:openWindow('" . $url . "', 'opop');\">";
			else
				$str .= "<a href=\"" . $url . "\" target=\"$target\">";
	
			if (esIconFontAwesome($icono))
				$str .= " " . imgFa($icono);
			else	
				$str .= img($icono, "");
			$str .= " $titulo";
			$str .= "</a></span>";
		}

		$str .= "\r\n</div></div>\r\n";

		return $str;
	}
}

?>