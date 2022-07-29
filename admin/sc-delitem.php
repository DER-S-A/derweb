<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = Request("query");
$rregistrovalor = RequestInt("registrovalor");

//si hay master query
$rmquery = Request("mquery");
$rmid = RequestInt("mid");
$rmfield = Request("mfield");

//una pila con otro nombre indica que est� en una solapa con su propia pila
$stackname = Request("stackname");

$mantener = RequestInt("mantener");

//busca la definicion y el obj en la cache
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else {
	$qinfo = new ScQueryInfo($query_info);
	$tc->saveQueryObj($rquery, $qinfo);
}
saveCache($tc);
//.......fin recuperar qinfo

//siempre que se actualiza o edita algun dato se borra la cache
$fileCache = new ScFileCache();
$fileCache->clear();

//analiza si tiene una foto e intenta borrar el archivo
$campoFoto = $qinfo->getCampoFoto();
$foto = "";
if (!sonIguales($campoFoto, "")) {
	$rs = locateRecordId($qinfo->getQueryTable(), $rregistrovalor, $qinfo->getKeyField());
	$foto = $rs->getValue($campoFoto);
}

//recupera el registro tal cual estaba
$sql = "select * 
		from " . $qinfo->getQueryTable() .
	" where " . $qinfo->getKeyField() . "=" . $rregistrovalor;

$rsPpal = new BDObject();
$rsPpal->execQuery($sql);
$row = $rsPpal->getRow();

// 1- borra registro en cuestion
$str = "delete from " . $qinfo->getQueryTable();
$str .= " where " . $qinfo->getKeyField() . "=" . $rregistrovalor;

$rsPpal->execQuery($str);

// 2 - borra dato adjunto (si hay)
$str = "delete from sc_adjuntos 
		where idquery = " . $qinfo->getQueryId();
$str .= " and iddato = " . $rregistrovalor;

$rsPpal->execQuery($str);

//actualiza checksum
sc3UpdateTableChecksum($qinfo->getQueryTable(), $rsPpal);


$op = "ip: " . getRemoteIp();

// 3 - borra archivo referencido
if (!sonIguales($foto, "")) {
	$op .= " (archivo borrado $foto)";
	unlink($UPLOAD_PATH_SHORT . "/" . $foto);
}

//analiza el tablaOnInsert()
//carga los modulos app-*.php que encuentre por si encuentra la funcion
sc3LoadModules();
$triggerFunction = $qinfo->getQueryTable() . "OnDelete";
if (function_exists($triggerFunction)) {
	eval($triggerFunction . '($row);');
}

logOp("BORRADO", $qinfo->getQueryName(), $rregistrovalor, $op);
?>
<html>

<head>

	<?php include("include-head.php"); ?>

</head>

<body onLoad="document.getElementById('form1').submit()">

	<div class="info-update">

		Borrando de <b><?php echo ($qinfo->getQueryDescription()); ?></b>... <br>
		<br>
		<?php echo ("El dato " . $rregistrovalor . " ha sido borrado.") ?>

	</div>

	<?php
	$anterior = 1;
	if ($mantener == 1)
		$anterior = 0;

	$action = "hole.php";
	if ($qinfo->isDebil())
		$action = "sc-showgrid.php";
	?>
	<form action="<?php echo ($action); ?>" name="form1" id="form1">

		<input type="hidden" name="anterior" value="<?php echo ($anterior); ?>" />
		<input type="hidden" name="query" value="<?php echo ($rquery); ?>" />
		<input type="hidden" name="stackname" value="<?php echo ($stackname);  ?>" />
		<input type="hidden" name="mquery" value="<?php echo ($rmquery); ?>" />
		<input type="hidden" name="mid" value="<?php echo ($rmid);  ?>" />
		<input type="hidden" name="mfield" value="<?php echo ($rmfield);  ?>" />

	</form>
</body>

</html>