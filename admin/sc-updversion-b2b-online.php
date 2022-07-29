<?php
require("funcionesSConsola.php");
require("sc-updversion-utils.php");
require("sc-updversion-sc3.php");
require("sc-updversion-b2b.php");
require("sc-updversion-wsp.php");


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

	// jul-2021: relacion entre empresas e indice 
	sc3UpdateVersionB2BSalimos();

	//11-ago-2021: primera version mensajes WhatsApp
	sc3UpdateVersionWspSalimos();

	//abr-2022: CIF
	sc3UpdateVersionB2B2022();

	//24-ene-2022: Acceso offline y mas....
	sc3UpdateVersionSc3Varios2022();

	ejecutarSps("sp/");
	echo ("<br>");
	Sc3FileUtils::borrarArchivos("tmp/");
	Sc3FileUtils::borrarArchivos("tmpcache/");
	Sc3FileUtils::borrarArchivos("scripts/tmpcache/");
	Sc3FileUtils::borrarArchivos("css/tmpcache/");

	//todas las tablas + 1
	sc3UpdateTableChecksum("*");

	if (!file_exists("./logs/"))
		mkdir("./logs");

	if (!file_exists("./terceros/"))
		mkdir("./terceros");

	if (!file_exists("./logs/"))
		mkdir("./logs");

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