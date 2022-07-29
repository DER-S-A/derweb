<?php
require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");


?>
<html>

<head>
	<title>sc3</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

	<link rel="stylesheet" href="sc-core.css" type="text/css">

</head>

<body>
	<div class="td_titulo2">Actualizaci&oacute;n de versiones </div>
	<br>
	<?php


	$anio = date("Y");
	$ANIOS_UPDATE = 2;


	if ($anio - 2021 < $ANIOS_UPDATE) {
		echo ("<br>Inicio 2021...");

		//enero 2021: utf, copiar perfiles....
		sc3UpdateVersionSc3Varios2021();
	}

	if ($anio - 2022 < $ANIOS_UPDATE) {
		echo ("<br>Inicio 2022...");

		//24-ene-2022: Acceso offline y mas....
		sc3UpdateVersionSc3Varios2022();
	}

	ejecutarSps("sp/");
	echo ("<br>");
	Sc3FileUtils::borrarArchivos("tmp/");
	Sc3FileUtils::borrarArchivos("tmpcache/");
	Sc3FileUtils::borrarArchivos("scripts/tmpcache/");
	Sc3FileUtils::borrarArchivos("css/tmpcache/");

	//todas las tablas + 1
	sc3UpdateTableChecksum("*");

	//archvos movidos a /controles
	Sc3FileUtils::sc3BorrarArchivo("sc-html-input-text.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-table.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-checkbox.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-areas.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-selector.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-grid.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-pdf.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-tabs.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-menu.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-combo.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-date.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-boolean.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-barcode.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-botones.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-texteditor.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-color.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-input-file.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-graphic.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-input-text-email.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-divdatos.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-cbu.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-html-cuit.php");
	Sc3FileUtils::sc3BorrarArchivo("dtree.css");
	Sc3FileUtils::sc3BorrarArchivo("selitems.php");
	Sc3FileUtils::sc3BorrarArchivo("delitem.php");
	Sc3FileUtils::sc3BorrarArchivo("viewitem.php");
	Sc3FileUtils::sc3BorrarArchivo("menu-fast.php");
	Sc3FileUtils::sc3BorrarArchivo("pagar-mp.php");
	Sc3FileUtils::sc3BorrarArchivo("pagar-mp2.php");
	Sc3FileUtils::sc3BorrarArchivo("footer2.php");
	Sc3FileUtils::sc3BorrarArchivo("hole2.php");
	Sc3FileUtils::sc3BorrarArchivo("html.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-hasartest.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-ir-a-clandestinidad.php");
	Sc3FileUtils::sc3BorrarArchivo("openCatalog.php");
	Sc3FileUtils::sc3BorrarArchivo("ajax-autosuggest.php");
	Sc3FileUtils::sc3BorrarArchivo("css/sc3.css");
	Sc3FileUtils::sc3BorrarArchivo("css/sc3-v2-modooscuro.css");
	Sc3FileUtils::sc3BorrarArchivo("sc-menu-desk.php");
	Sc3FileUtils::sc3BorrarArchivo("sc-menu-desk2.php");
	Sc3FileUtils::sc3BorrarArchivo("app-citanova.php");
	/* 
	descomentar a fines de 2022
	Sc3FileUtils::sc3BorrarArchivo("loginerror.php");
	Sc3FileUtils::sc3BorrarArchivo("ajax-selector.php");
	Sc3FileUtils::sc3BorrarArchivo("ajax-selector2.php");
	*/

	if (!file_exists("./logs/"))
		mkdir("./logs");

	if (!file_exists("./terceros/"))
		mkdir("./terceros");

	$destino = "terceros";
	$carpeta = "ws-nusoap-0.9.5";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	$carpeta = "phpqrcode";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	$carpeta = "phpmailer";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	$carpeta = "pdf-v12-rc19";
	Sc3FileUtils::moverCarpeta($carpeta, $destino, "cezpdf");

	$carpeta = "mercadopago";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	$carpeta = "phpids-0.7";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	$carpeta = "font-awesome-4.7.0";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	$carpeta = "plchart";
	Sc3FileUtils::moverCarpeta($carpeta, $destino);

	if (!file_exists("./logs/"))
		mkdir("./logs");

	if (!file_exists("./modulos/"))
		mkdir("./modulos");

	echo ("<br>" . date("d-m-y H:i:s"));

	//no va mas
	Sc3FileUtils::rrmdir("b2evo_captcha");
	Sc3FileUtils::rrmdir("pdf");
	Sc3FileUtils::rrmdir("terceros/phpids-0.7");

	echo ("<br>" . date("d-m-y H:i:s"));

	echo ("<br><br>Limpiando cache !");
	$tc = getCache();
	$tc->flushCache();
	saveCache($tc);

	echo ("<br><br>EXITO ! t=" . Sc3FechaUtils::formatFecha(getdate()));
	?>
	<br>
</body>

</html>