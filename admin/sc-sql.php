<?php
include("funcionesSConsola.php");
checkUsuarioLogueado();

?>
<!doctype html>
<html lang="es">
<head>
<title>SQL - por SC3</title>

<?php 
$SC3_EVITAR_PRECARGA = 1;
include("include-head.php"); 
?>

<style>

#divTablas 
{
	height: 100%;
    width: 240px; 
    POSITION: fixed;
    left: 0px;
    top: 0px;
}


#divQuery
{
	width: calc(100% - 240px);
	height: 240px;
	left: 240px;
	top: 0px;
	position: fixed;
	overflow: hidden;
}

#divResultado
{
	left: 240px;
	top: 240px;
	position: fixed;
	width: calc(100% - 240px);
	height: calc(100vh - 240px);
    background-color: #616161;
}

.noscroll
{
	overflow: hidden;
}

.nomargin
{
	margin: 0px;
}

.frames
{
	border: 0px;
	width: 100%;
	height: 100%;
}

</STYLE>


</head>
<body>
  
<div id="divTablas">
    <iframe id="iframeTablas" class="frames noscroll" name="tablas" src="sc-sql-tablas.php">
    </iframe>
</div>	

<div id="divQuery">
    <iframe name="query" id="query" class="frames nomargin" src="sc-sql-query.php">
    </iframe>
</div>
    
<div id="divResultado">	
    <iframe name="resultado" src="" class="frames">
    </iframe>
</div>	

</body>

</html>

