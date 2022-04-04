<?php
//previene que PHP mande los headers de caché
session_cache_limiter(FALSE);
header("Cache-Control: private, max-age=10, must-revalidate"); 

require("funcionesSConsola.php");

//armar etag con: query - order - busqueda - checksum

$params = "";
if (isset($_SERVER['QUERY_STRING']))
	$params = $_SERVER['QUERY_STRING'];

$last_modified_time = time();
//incluye los parámetros pero obliga a refrezcar cada minuto al cambar el etag
$etag = md5($params) . date("-Hi");

header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT"); 
header("Etag: $etag"); 

if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time) || 
	(isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag)) 
{ 
		header("HTTP/1.1 304 Not Modified"); 
		exit; 
} 

?>
<!doctype html>
<html lang="es">
<head>

<title> 
Test CACHE - por SC3
</title>
</head>
<body>
	<?php
		echo(date("y-m-d h:i:s") . " - $last_modified_time - Etag:  $etag <br>");
	?>
</body>
</html>