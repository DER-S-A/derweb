<?php
include("funcionesSConsola.php");
checkUsuarioLogueado();

$query = RequestSafe("query");
$id = RequestInt("valor1");
$valor = Request("valor2");

if ($id > 0)
{
	if (!sonIguales($valor, ""))
	{
		setSession("selector-" . $query, $id);
	}
}
else
	setSession("selector-" . $query, "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>sc3</title>

<? include("include-head.php"); ?>

<script language="javascript">
function cerrar()
{
window.close();
}
</script>

</head>
<body onload="cerrar()">
Fijando valor <? echo($id . " - " . $valor); ?> ...
</body>
</html>