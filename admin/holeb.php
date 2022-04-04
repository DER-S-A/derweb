<?php 
require("funcionesSConsola.php"); 
checkUsuarioLogueado();

//analiza si hay anterior porque si no lo hay no usa la cache 
//esto es porque al invocar al goOn() no se manda el parametro, por lo que viene de presionar el [ Aceptar ] 
$ant = Request("anterior");
$useCache = true;
if (sonIguales($ant, ""))
{
	$useCache = false;
}

$anterior = RequestInt("anterior");
//nuevos para los reportes
$mid = RequestInt("mid");
$opid = RequestInt("opid");
$mensaje = getMensaje();
$warning = getWarning();
$loc = "";

$fileCache = new ScFileCache();

//Si hay opid es que tiene que ejecutar una operacion, no desapila
if (($opid == 0) && esVacio($mensaje) && esVacio($warning))
{
	//recupera tope de pila, 
	$stack = getStack();
	$loc = $stack->getUrlTope();
	$stackKey = $stack->getKeyTope();
	
	//si encuentra el archivo grabado en archivo, lo retorna
	$cachefile = $fileCache->fileExists($stack->getCount());	
	if (!sonIguales($cachefile, "") && ($anterior != 1) && $useCache)
	{
		//echo("Ant: $ant");
		readfile($cachefile);
		exit;
	}	
	
	$stack->desapilar();
	if ($anterior == "1")
	{
		$loc = $stack->getUrlTope();
		$stackKey = $stack->getKeyTope();
	}		
	saveStack($stack);
}	

//deriva si hay algo en el stack y no hay pedido de ejecutar una operacion
if ((strcmp($loc, "") != 0) && ($opid == 0) && esVacio($mensaje) && esVacio($warning))
{
	$cachefile = $fileCache->fileExists($stack->getCount());	
	if (!sonIguales($cachefile, "") && $useCache)
	{
		 readfile($cachefile);
	     exit;
	}	
	else
	{
		header("Location:" . $loc);
		exit;  
	}	
}


?>
<!DOCTYPE html>
<html lang="es">
  <head>

	  <?php include("include-headb.php"); ?>
  
      <title><?php echo($SITIO);?></title>
    
  </head>

<body>

    <div class="container">

    	<div class="divEscritorio small">

    			<form action="sc-buscar.php" method="get">
					<table width="100%" border="0" cellspacing="5" cellpadding="5">
						<tr>
							<td align="center">
							
							        <input name="word" type="text" id="word" class="form-control" placeholder="ej: gonzales, cliente gonzales, cheque 2398765, etc." required autofocus>
        
							        <button class="btn btn-lg btn-primary btn-block btn-success" type="submit">Buscar</button>
							</td>
						</tr>
					</table>
				</form>
    	
    	</div>

    	<div class="divEscritorio small">
    	
				<iframe src="app-citanova.php"
				      width="310" height="50" scrolling="no" frameborder="0" transparency>
			    </iframe>

		</div>

    	<div class="divEscritorio big">
		
		  	 
		  	 <table width="310" border="0" cellspacing="1" cellpadding="4">
		        <tr>
		          <td class="tabla_menu" align="center">Mis favoritos</td>
		          <td class="tabla_menu" width="10" align="center" title="cantidad">#</td>
		        </tr>
				  <?php
			  		//recupera los querys que tiene acceso el usuario
					$desk = getEscritorio();
					$sec = new SecurityManager();
					$rs = $sec->getRsQuerysEnEscritorio();
					echo($desk->showFavoritos($rs));
				  ?>
		      </table>
		
		</div>

    	<div class="divEscritorio big">
		      
			      <table width="310" border="0" cellspacing="2" cellpadding="2">
			        <tr>
			          <td class="tabla_menu" align="center">Notas </td>
			          <td class="tabla_menu" width="10" align="center"><img src="images/scnotas.png" title="Notas" border="0" /></td>
			        </tr>
			        <tr>
			          <td width="50%" colspan="2" align="center" valign="top" class="td_dato">
						<?php
						$bd = $sec->getRsMisNotas();
						
						echo("<div style=\"width:310px; height:200px; overflow: scroll;overflow-x: hidden\"><table cellspacing=\"2\" cellpadding=\"2\" width=\"290\">");
						while (!$bd->EOF())
						{
							$notaColor = $bd->getValue("color");
							$notaNota = $bd->getValue("nota");
							$notaUser = $bd->getValue("login");
							$notaUrl = "<a href=\"sc-viewitem.php?query=" . $bd->getValue("queryname") . "&registrovalor=" . $bd->getValue("iddato") . "&fstack=1\">";
							$notaImg = "<img src=\"./" . $bd->getValue("icon") . "\" border=\"0\" />";
							echo("<tr><td bgcolor=\"$notaColor\" align=\"left\">$notaUrl $notaImg <b>$notaUser</b>: $notaNota</a></td></tr>");
							$bd->Next();
						}
						echo("</table></div>");
						?>
			          </td>
			        </tr>
			      </table>    
		
		</div>

    	<div class="divEscritorio big">
			      
		       <table width="310" border="0" cellspacing="1" cellpadding="4">
		        <tr>
		          <td class="tabla_menu" align="center">&Uacute;ltimos datos consultados </td>
		          <td class="tabla_menu" width="10" align="center"><img src="images/operaciones.png" title="Ver ultimo dato" border="0" /></td>
		        </tr>
				  <?php
				  //agrega las filas de tabla con los ultimos querys realizados
				  $desk = getEscritorio();
				  echo($desk->showQuerys(false));		  
				  ?>
		      </table>
		      
		</div>

    	<div class="divEscritorio big">
		      
			  <a href="http://www.sc3.com.ar/" target="_blank">
			  <br />
			  <img src="images/sc3-logo.png" alt="Visitar www.sc3.com.ar"  border="0"/></a>

		</div>

		
<?php 
$showFooter = false;
include("footerb.php");
?>
</body>
</html>
