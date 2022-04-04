<?php

/**
 * Modulo de documentos electronicos
 * por Marcos C.
 * ago-2021
 * Prefijo doc
 */


//30-ago-2021: SALIMOS
function sc3UpdateVersionDocSalimos()
{
	$bd = new BDObject();

	$menu = "Documentos";
	sc3AgregarMenu($menu, 800, "fa-file-text-o", "#e1b12c");
	sc3AgregarPerfil($menu);

	$tabla = "doc_documentos";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$sql3 = "CREATE TABLE `$tabla`
			(
				id int(10) unsigned NOT NULL AUTO_INCREMENT,
				archivo varchar(100) not null,
				descripcion varchar(250) null,					
				fecha_alta datetime not null,
				checksum varchar(60) null,
				PRIMARY KEY (`id`),
				UNIQUE INDEX uq_$tabla (archivo, fecha_alta, descripcion)
			)
			ENGINE = InnoDB;";

		$bd->execQuery($sql3);

		sc3agregarQuery($query, $tabla, "Documentos", $menu, "descripcion", 1, 1, 1, "fecha_alta desc", 8, "images/file.gif", 0);

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $menu);

		sc3updateField($query, "archivo", "archivo", 1, "", 1);
	}


	$tabla = "doc_etiquetas";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$sql3 = "CREATE TABLE `$tabla`
			(
				id int(10) unsigned NOT NULL AUTO_INCREMENT,
				nombre varchar(60) not null,
				color varchar(20) null,					
				PRIMARY KEY (`id`),
				UNIQUE INDEX uq_$tabla (nombre)
			)
			ENGINE = InnoDB;";

		$bd->execQuery($sql3);

		sc3agregarQuery($query, $tabla, "Etiquetas", $menu, "", 1, 1, 1, "nombre", 8, "images/tag_blue.png", 0);

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $menu);

		sc3updateFieldColor($query, "color");
	}


	$tabla = "doc_documentos_etiquetas";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$sql3 = "CREATE TABLE `$tabla`
			(
				id int(10) unsigned NOT NULL AUTO_INCREMENT,
				iddocumento int(10) unsigned NOT null,
				idetiqueta int(10) unsigned not null,

				PRIMARY KEY (`id`),
				unique UQ_$tabla (iddocumento, idetiqueta)
			)
			ENGINE = InnoDB;";

		$bd->execQuery($sql3);

		sc3agregarQuery($query, $tabla, "Etiquetas de documento", "", "id", 1, 1, 1, "iddocumento", 8, "images/avietiquetas.png", 1, "id");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $menu);

		$field = "iddocumento";
		sc3addFk($tabla, $field, "doc_documentos", "id", true);
		sc3addlink($query, $field, "qdocdocumentos", 1);

		$field = "idetiqueta";
		sc3addFk($tabla, $field, "doc_etiquetas", "id", false);
		sc3addlink($query, $field, getQueryName("doc_etiquetas"));
	}


	$tabla = "doc_dnis";
	$query = getQueryName($tabla);
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$sql = "CREATE TABLE `$tabla` (
			`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			`nombre` VARCHAR(100) NOT NULL,
			`sexo` VARCHAR(20) NOT NULL,
			`dni` VARCHAR(20) NOT NULL,
			`cuit` VARCHAR(20) NULL,
			`fecha_nacimiento` DATETIME NULL,
			`fecha_tramite` DATETIME NULL,
			`fecha_escaneo` DATETIME NOT NULL,
			`informado` TINYINT(3) NOT NULL,
			PRIMARY KEY (`id`)
		)
		ENGINE = InnoDB;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "DNIs", $menu, "nombre", 1, 1, 1, "dni, nombre", 8, "images/ecomatriz.png");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $menu);
	}

	$field = "idusuario";
	$query = getQueryName($tabla);
	if (!sc3existeCampo($tabla, $field)) {
		sc3addlink($query, $field, "sc_usuarios");
		sc3agregarCampoInt($tabla, $field, false, "id", "");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3UpdateQueryFields($query, $tabla);
	}

	$field = "ejemplar";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, true, "cuit", "");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3UpdateQueryFields($query, $tabla);
	}

	$field = "nro_tramite";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, true, "ejemplar", "", 100);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3UpdateQueryFields($query, $tabla);
	}

	$field = "texto_escaneado";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, true, "informado", "", 500);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3UpdateQueryFields($query, $tabla);

		$field = "fecha_escaneo";
		sc3setGroup($tabla, $field, "Sistema");
		$field = "informado";
		sc3setGroup($tabla, $field, "Sistema");
		$field = "texto_escaneado";
		sc3setGroup($tabla, $field, "Sistema");
	}

	$bd->close();
}
