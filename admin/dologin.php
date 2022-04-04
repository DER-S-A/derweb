<?php
require("funcionesSConsola.php");

setSession("dbname", $BD_DATABASE);


if (doLogin(RequestSafe("login"), RequestSafe("clave"))) {
	//borra tmp de ayer o anterior
	Sc3FileUtils::borrarArchivos("tmp/", false);

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
