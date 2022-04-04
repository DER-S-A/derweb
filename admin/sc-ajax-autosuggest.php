<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$filtername = "";
$aquery = explode("|", Request("query"));
$query = $aquery[0];
if (sizeof($aquery) > 1)
	$filtername = $aquery[1];
$filterWhere = "";
$mfield = Request("mfield");
$mid = RequestInt("mid");

$desc = Request("valor");

$rcontrol1 = Request("control1");
$extendedFilter = getSession($rcontrol1 . "-eqf");

$result = $EMPTY_SELECTOR;
if (sonIguales($desc, "") || sonIguales($query, ""))
{
	return $result;
}	

if (sonIguales($query, "emails"))
{
	$rs = new BDObject();
	
	//, concat(nombre, ' (usuario)') as info
	$sql = "select distinct id, 
				email as descripcion 
			from sc_usuarios  
			where habilitado = 1
				and (email like '%" . $desc . "%')"; 
		
	$sql .= " union all ";

	// 	concat(nombre, ' (', case when es_cliente = 1 then 'cliente' when es_proveedor = 1 then 'proveedor' when es_empleado = 1 then 'empleado' else 'contacto' end, ')') as info 
	$sql = "select distinct id, 
				concat(email, case when ifnull(email2, '') <> '' then concat(', ', email2) else '' end) as descripcion  
			from gen_personas 
			where email is not null and email <> '' and 
				(email like '%" . $desc . "%' or email2 like '%" . $desc . "%')"; 
}
elseif (sonIguales($query, "dir_contacto"))
{
	require("app-ven.php");
	$rs = new BDObject();
	$sql = venContactosSql($desc);
}
elseif (sonIguales($query, "articulos_delivery"))
{
	require("app-ven.php");
	$rs = new BDObject();
	$sql = venArticulosSuggest($desc);
}
elseif (sonIguales($query, "delivery_entrega"))
{
	require("app-ven.php");
	$rs = new BDObject();
	$sql = venEntregaSuggest($desc);
}
elseif (sonIguales($query, "dir_contacto2"))
{
	require("app-ven2.php");
	$rs = new BDObject();
	$sql = venContactosSql2($desc);
}
elseif (sonIguales($query, "articulos_delivery2"))
{
	require("app-ven2.php");
	$rs = new BDObject();
	$sql = venArticulosSuggest2($desc);
}
elseif (sonIguales($query, "delivery_entrega2"))
{
	require("app-ven2.php");
	$rs = new BDObject();
	$sql = venEntregaSuggest2($desc);
}
else
{
	$tc = getCache();
	if ($tc->existsQueryObj($query))
		$qinfo = $tc->getQueryObj($query);
	else
	{
		$query_info = $tc->getQueryInfo($query);
		$qinfo = new ScQueryInfo($query_info, true);
		$tc->saveQueryObj($query, $qinfo);
	}
	saveCache($tc);
	
	if (!sonIguales($filtername, ""))
	{
		$filterWhere = $qinfo->getFilterWhere($filtername); 
	}
	
	$rs = new BDObject();
	$sql = "select " .  $qinfo->getKeyField() . " as id, " . $qinfo->getComboField() . " as descripcion, concat('id: ', " . $qinfo->getKeyField() . ") as info";
	$sql .= " from " .  $qinfo->getQueryTable() . " t1 ";
	$sql .= " where ";
	
	$whereStr = $qinfo->getQueryWhere();
	if (!sonIguales($filterWhere, ""))
		$whereStr = $filterWhere;
	
	if (!sonIguales($whereStr, ""))
		$sql .= $whereStr . " and ";
		
	$sql .= $qinfo->getComboField() . " like '%" . $desc . "%'"; 
	
	//si hay campo master tambien filtra por el
	//si hay filtro adicional (con una expresi�n particular, es usa el $mid en ella)
	if (esVacio($extendedFilter))
	{
		if (!sonIguales($mfield, ""))
			$sql .= " and t1.$mfield = $mid";
	}
	else
	{
		$sql .= " and " . str_replace(":$mfield", $mid, $extendedFilter);
	}
}	

$sql .= " limit 50";
$rs->execQuery($sql); 		

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache"); 
header("Content-Type: text/xml");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><results>";

$muestraInfoId = getParameterInt("sc3-selector-muestraid", "0");
$idinfo = "";
while (!$rs->EOF())
{
	if ($muestraInfoId != 0)
		$idinfo = $rs->getValue("info"); 
	echo "\n<rs id=\"" . $rs->getValue("id") . "\" info=\"$idinfo\"><![CDATA[" . sinCaracteresEspeciales($rs->getValue("descripcion")) ."]]></rs>";
	$rs->Next();
}
echo "</results>";
?>