<?php


//10-oct-2021: primera version de graficos
function sc3UpdateVersionGraSalimos()
{
	$bd = new BDObject();

	$menu = "Graficos";
	$perfil = "Graficos";

	$tabla = "gra_graficos";
	$query = getQueryName($tabla);
	if (!$bd->existeTabla($tabla)) {

		sc3AgregarMenu($menu, 500, "fa fa-bar-chart", "#25D366");
		sc3AgregarPerfil($perfil);

		echo ("<br>creando tabla <br>$tabla</br>...");

		$SQL = "CREATE TABLE `$tabla` 
					( 
						`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
						nombre varchar(100) not null,
						idunidad_medida INT(10) unsigned not null,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `uq_$tabla`(`nombre`)
					)
					ENGINE = InnoDB;";

		$bd->execQuery($SQL);

		sc3agregarQuery($query, $tabla, "Graficos", $menu, "", 1, 1, 1, "nombre", 8, "images/chart_bar.png");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		sc3addFk($tabla, "idunidad_medida", "mat_unidades_medidas", "id", false);
		sc3addlink($query, "idunidad_medida", "qmatunidadesmedidas", 0);
	}

	$tabla = "gra_series";
	$query = getQueryName($tabla);

	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla <br>$tabla</br>...");

		$SQL = "CREATE TABLE `$tabla` 
					( 
						`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
						idgrafico INT(10) UNSIGNED not null,
						nombre varchar(100) not null,
						PRIMARY KEY (`id`)
					)
					ENGINE = InnoDB;";

		$bd->execQuery($SQL);

		sc3agregarQuery($query, $tabla, "Series", $menu, "", 1, 1, 1, "nombre", 8, "images/statistics.gif");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		sc3addFk($tabla, "idgrafico", "gra_graficos", "id", false);
		sc3addlink($query, "idgrafico", "qgragraficos", 0);
	}


	$field = "color_linea";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, true, "nombre", "", 60);
		sc3generateFieldsInfo($tabla);
		sc3updateFieldColor($query, "color_linea");
	}

	$field = "rellena";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, 0, "color_linea");
		sc3generateFieldsInfo($tabla);
		$bd->execQuery("UPDATE $tabla SET $field = 0");
	}

	$field = "color_relleno";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, true, "rellena", "", 60);
		sc3generateFieldsInfo($tabla);
		sc3updateFieldColor($query, "color_relleno");
	}

	$field = "completar_linea";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, 0, "color_linea");
		sc3generateFieldsInfo($tabla);
		$bd->execQuery("UPDATE $tabla SET $field = 0");
	}

	$tabla = "gra_series_valores";
	$query = getQueryName($tabla);

	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla <br>$tabla</br>...");

		$SQL = "CREATE TABLE `$tabla` 
					( 
						`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
						idserie INT(10) UNSIGNED not null,
						x VARCHAR(100) not null,
						y DECIMAL(18,2) not null,
						PRIMARY KEY (`id`)
					)
					ENGINE = InnoDB;";

		$bd->execQuery($SQL);

		sc3agregarQuery($query, $tabla, "Valores", "", "", 1, 1, 1, "orden, x", 8, "images/statistics.gif", 1);

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		sc3addFk($tabla, "idserie", "gra_series", "id", true);
		sc3addlink($query, "idserie", "qgraseries", 1);
	}

	$field = "orden";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false, "idserie", "100");
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla, "id");

		$bd->execQuery("UPDATE $tabla SET $field = 100");
		sc3UpdateQueryFields($query, $tabla, "id");
	}

	$url = "app-gra-graficar.php";
	sc3AgregarOperacion("Graficar", $url, "images/grafico.png", "Graficar graficos", "", "Graficos", 0, "Graficos");

	$bd->close();
}
