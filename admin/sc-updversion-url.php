<?php
//Urls - acortador
//por Victor G.
//feb-2021

//2-feb-2021: salimos con los 
function scUpdateVersionUrlSalimos()
{
	$bd = new BDObject();

	$tabla = "url_urls";
	$query = getQueryName($tabla);
	$grupo = "";
	$menu = "Urls";
	$perfil = "Urls";
	if (!$bd->existeTabla($tabla)) {

		sc3AgregarMenu($menu, 200, "fa fa-link");
		sc3AgregarPerfil($menu);

		echo ("<br>creando tabla $tabla...");

		$SQL = "CREATE TABLE `$tabla`
		(
			`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			usuario VARCHAR(60) not null,
			url VARCHAR(1000) not null,
			url_key VARCHAR(20) not null,
			fecha DATETIME not null,
			entradas INT not null,
			PRIMARY KEY (`id`),
			UNIQUE INDEX `uq_$tabla`(`url_key`)
		)
		ENGINE = InnoDB;";

		$bd->execQuery($SQL);

		sc3agregarQuery($query, $tabla, "URL Cortas", $menu, "url_key", 1, 1, 1, "fecha desc", 8, "images/comunidad.png");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);
	}

	$tabla = "url_usuarios";
	$query = getQueryName($tabla);
	$grupo = "";
	$bd = new BDObject();

	$bd = new BDObject();
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$SQL = "CREATE TABLE `$tabla`
		(
			`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			usuario VARCHAR(60) not null,
			clave VARCHAR(60) not null,
			PRIMARY KEY (`id`),
			UNIQUE INDEX `uq_$tabla`(`usuario`)
		)
		ENGINE = InnoDB;";

		$bd->execQuery($SQL);

		sc3agregarQuery($query, $tabla, "Usuarios", $menu, "usuario", 1, 1, 1, "usuario desc", 8, "images/person.gif");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);
	}

	$field = "url_base";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, 1, "", "10");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3updateFieldHelp($query, $field, "Url base del usuario.");
		sc3UpdateQueryFields($query, $tabla);
	}

	$url = "url-test.html";
	$opid = sc3AgregarOperacion('Test API (JS)', $url, 'images/fla.gif', "Probar API", '', $menu, 0, $perfil);

}
