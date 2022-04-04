<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

header('Content-type: text/html; charset=UTF-8');

$tabla = RequestSafe("tabla");
$autoincrement = sonIguales(Request("id"), "AUTOINCREMENT");
$todos = RequestInt("all");

$id = RequestInt("id");
$field = RequestSafe("field");
$keyField = RequestSafe("keyfield");
if (sonIguales($keyField, ""))
	$keyField = "id";
	
if (sonIguales($field, "clave"))
	return "";

if ($autoincrement)
{
	$rs = getRs("SHOW TABLE STATUS LIKE '$tabla'");
	$id = $rs->getValueInt("Auto_increment");
	echo($id);
	return;
}

//invocado para que devuelva toda la tabla 
if ($todos == 1)
{
    echo(sc3ArrayJasonTabla($tabla, "id", false));
    return;
}


//si no viene el ID, busca por otro campo y retorna ID
if ($id == 0)
{
	//solicita ROW pero con id en cero
	if (startsWith($field, "ROW"))
	{
		$row = array();
		$row["AJAX_RESULT"] = 0;
		$rowJson = json_encode($row);
			
		echo($rowJson);
	}
	else 
	{
		$valor = RequestSafe("valor");
		$condicion = "$field = '$valor'";
			
		//analiza si viene otro campo con otro valor
		$field2 = RequestSafe("field2");
		$valor2 = RequestSafe("valor2");
		
		if (!esVacio($field2) && !esVacio($valor2))
			$condicion .= " and $field2 = '$valor2'";
			
		$rs = locateRecordWhere($tabla, $condicion);
		if ($rs->EOF())
			echo("0");
		echo($rs->getValue($keyField));
	}
}
else 
{
	$condicion = "$keyField = $id";
	
	if (sonIguales($field, "ROW_WHERE"))
		$condicion = Request("where");
			
	$rs = locateRecordWhere($tabla, $condicion, true);
	if ($rs->EOF())
	{
		if (!startsWith($field, "ROW"))
			echo("");
		else 
		{
			$row = array();
			$row["AJAX_RESULT"] = 0;
			$rowJson = json_encode($row);
			echo($rowJson);
		}	
	}
	else
	{
		if (!startsWith($field, "ROW"))
			echo($rs->getValue($field));
		else
		{
			//retorna todo el row en un arreglo json
			$row = $rs->getRow();
			$row["AJAX_RESULT"] = 1;

			//saca acentos y la ñ: convertidos con utf8_encode()
			$row2 = sinCaracteresEspecialesArray($row);

			$rowJson = json_encode($row2);

			echo($rowJson);	
		}
	}
}
