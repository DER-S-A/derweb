<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

//registra poder llamar a la funcion sc3UsuariosActivos desde javascript
$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("sc3UsuariosActivos");
sc3SaveAjaxHelper($ajaxH);


function buildMenuMobile()
{
	$sec = new SecurityManager();
	$rsmenu = $sec->getMenuSc3();
	
	$cant = $rsmenu->cant();
	
	while (!$rsmenu->EOF())
	{
		$item = $rsmenu->getValue("Item");
		$idmenu = $rsmenu->getValue("idItemMenu");
		$img = $rsmenu->getValue("icon");

		$title = $item;
		$divitem = escapeJsNombreVar($item);
		
		//si son muchos s�lo muestra �cono
		if ($cant >= 6)
			$item = substr($item, 0, 3);
		if ($cant > 10)
			$item = " ";
					
		if (esVacio($img))
			$img = "fa-folder-o";

		echo("<div class=\"w3-dropdown-click \">
				<button class=\"w3-button  boton-menu\" onclick=\"desplegarMenu('$divitem');\">
					<i class=\"fa $img fa-fw fa-2x\"></i> $item <i class=\"fa fa-caret-down\"></i>
				</button>
				<div class=\"w3-dropdown-content w3-bar-block w3-dark-grey\" id=\"$divitem\">");
		
		$rsTablas = $sec->getRsQuerys($idmenu);
		
		while (!$rsTablas->EOF())
		{
			$url = new HtmlUrl("inside-mobile.php");
			$url->add("action", "sc-selitems.php");
			$url->add("query", $rsTablas->getValue("queryname"));
			$url->add("fstack", "1");
			$url->add("todesktop", "1");
			$item = $rsTablas->getValue("querydescription");
			$icon = $rsTablas->getValue("icon");
			$idquery = $rsTablas->getValue("id");
			
			if ($icon == "")
				$icon = "images/table.png";
			$target = "contenido";

			echo("\n  <a class=\"w3-bar-item w3-button w3-mobile\" href=\"" . $url->toUrl() . "\">" . img($icon, "") . " " . htmlVisible($item) . "</a>");
			
			$rsTablas->Next();
		}		
		
		$rsTablas = $sec->getRsOperaciones($idmenu);
				
		while (!$rsTablas->EOF())
		{
			$url = new HtmlUrl("inside-mobile.php");
			$url->add("action", $rsTablas->getValue("url"));
			$url->add("fstack", "1");
			$url->add("opid", $rsTablas->getValue("id"));
		
			$item = $rsTablas->getValue("nombre");
			$icon = $rsTablas->getValue("icon");
			if ($icon == "")
				$icon = "images/table.png";

			//si abre afuera va directo al URL dado
			$target = $rsTablas->getValue("target");
			if (!esVacio($target))
			{
				$url = new HtmlUrl($rsTablas->getValue("url"));
				$url->add("fstack", "1");
				$url->add("opid", $rsTablas->getValue("id"));
			}
		
			echo("\n  <a class=\"w3-bar-item w3-button w3-mobile\" href=\"" . $url->toUrl() . "\" target=\"$target\">" . img($icon, "") . " " . htmlVisible($item) . "</a>");
		
			$rsTablas->Next();
		}
		
		$rsmenu->Next();
		
		echo("\n  </div></div>\n");
		
	}
	?>
	
<?php 	
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<?php include("include-head.php"); ?>
  
<title><?php echo(htmlentities($SITIO)); ?> - por sc3</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
	  
<script type="text/javascript">

	function abrirMenu(xitem)
	{
    	document.getElementById(xitem).style = '';
	} 
	

	function resizeIframe(obj) 
	{
		obj.style.height = ((obj.contentWindow.document.body.scrollHeight) * 1 + 25) + 'px';
	}

	/**
	 * Invoca funcion del server para determina usuarios activos
	 */
	function checkUsuariosActivos()
	{
		var params = [];
		sc3InvokeCallback('sc3UsuariosActivos', params, checkUsuariosActivos2);
	}


	function checkUsuariosActivos2()
	{
		if (xmlhttp2.readyState == 4)
		{
		    rta = xmlhttp2.responseText;
		    if (rta != "")
		    {
		    	ausr = JSON.parse(rta);
		    	
		    	divU = document.getElementById("usuariosActivos");
		    	
		    	usuariosDesc = '';
		    	i = 0;
		    	while ((i < ausr.length) && (i < 100))
		    	{
		    		if ((ausr[i].login != 'undefined') && (isNaN(ausr[i].login)))
		    			usuariosDesc = usuariosDesc + ausr[i].login + ' | ';
		    			
		    		i++;
		    	}

		    	usuarios = "<img alt=\"Usuarios conectados\" title=\"" + usuariosDesc + "\" src=\"images/sc-usuarios.png\" /> ";
		    	divU.innerHTML = usuarios + " (" + (i - 1) + ')';
		    }
		}
	}


	function sc3AbrirMenu() 
	{
	    var x = document.getElementById("myTopnav");
	    if (x.className === "topnav") 
	    {
	        x.className += " responsive";
	    } 
	    else 
	    {
	        x.className = "topnav";
	    }
	}


	function desplegarMenu(xdiv) 
	{
	    var x = document.getElementById(xdiv);
	    if (x.className.indexOf("w3-show") == -1) {
	        x.className += " w3-show";
	    } else { 
	        x.className = x.className.replace(" w3-show", "");
	    }
	}
</script>

<style type="text/css">

.topnav 
{
	overflow: hidden;
	background-color: #5f5f5f;
	background-color: #616161;
}

.topnav a 
{
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 12px;
  text-decoration: none;
  font-size: 16px;
}

.topnav a:hover 
{
  background-color: #ddd;
  color: black;
}

.active 
{
  color: white;
}

.topnav .icon 
{
  display: none;
}

@media screen and (max-width: 600px) 
{
  .topnav a:not(:first-child) {display: none;}
  .topnav a.icon {
    float: right;
    display: block;
  }
}

@media screen and (max-width: 600px) 
{
  .topnav.responsive {position: relative;}
  .topnav.responsive .icon {
    position: absolute;
    right: 0;
    top: 0;
  }
  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
}

.boton-menu
{
	color: white;
	background-color: #607d8b;
}

</style>

</head>

<body>

<div class="w3-bar w3-dark-grey">
	<a href="inside-mobile.php?action=hole.php" class="w3-bar-item w3-button  boton-menu">
		<i class="fa fa-home fa-fw fa-2x"></i>
	</a>

	<?php 
	buildMenuMobile();
	?>
	
</div> 

        <?php 
		$actionUrl = new HtmlUrl(Request("action"));    
		if (esVacio(Request("action")))
			$actionUrl->setUrl("hole.php");    
        $actionUrl->addFromRequestG();
        ?>
        <iframe style="position: absolute; width: 100%; border: none" src="<?php echo($actionUrl->toUrl());?>" class="centerplay" onload='javascript:resizeIframe(this);' ></iframe>

</body>
</html>