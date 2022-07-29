<?php
require("funcionesSConsola.php");

setSession("dbname", $BD_DATABASE);

if (doLogin(RequestSafe("login"), RequestSafe("clave"))) {
	//borra tmp de mas de 7 días
	Sc3FileUtils::borrarArchivos("tmp/", false);
	Sc3FileUtils::borrarArchivos("log/", false, 60);
	Sc3FileUtils::borrarArchivos("errores/", false, 60);

	if (usuarioLogueado()) {
		$menuB = "";
		if (isMobileAgent() || (sonIguales($menuB, "SUPERIOR")))
			header("location:insideb.php");
		else
			header("location:inside.php");
		exit;
	} else
		checkUsuarioLogueado();
} else {
	checkUsuarioLogueado();
}
