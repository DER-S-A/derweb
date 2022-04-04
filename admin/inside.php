<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

if (!isset($DESARROLLADOR_NOMBRE))
	$DESARROLLADOR_NOMBRE = "SC3 Sistemas";

?>
<!doctype html>
<html lang="es">
<head>
<title><?php echo(htmlentities($SITIO) . " por " . $DESARROLLADOR_NOMBRE); ?></title>

<meta charset="ISO-8859-1"> 
 
<link rel="shortcut icon" href="ico/logo.ico" type="image/x-icon" />

<style>

#divMenu 
{
    left: 0px;
    top: 0px;
	height: 100%;
    WIDTH: 240px; 
    POSITION: fixed;
}


#divContenido
{
	left: 240px;
	top: 0px;
	position: fixed;
	width: calc(100vw - 240px);
	height: 100%;
}

.frames
{
	border: 0px;
	width: 100%;
	height: 100%;
}

.noscroll
{
	overflow: hidden;
}

.nomargin
{
	margin: 0px;
}

</STYLE>

</head>
<body>

	<div id="divMenu">
		<iframe name="indice" id="indice" class="frames nomargin" src="sc-menu-panel.php?cols=1&innercols=1">
		</iframe>
	</div>
		
	<div id="divContenido">	
		<iframe name="contenido" id="contenido" src="hole.php" class="frames">
		</iframe>
	</div>	

</body>
</html>