<?php

/**
 * FUCK (alias FAQ)
 * prefijo faq_
 */

//16-mar-2022: salimos con las FAQ
function sc3UpdateVersionFaqSalimos()
{
	$bd = new BDObject();

	$perfil = "Faq";
	sc3AgregarPerfil($perfil);

	$menu = "FAQ";
	sc3AgregarMenu($menu, 200, "fa-info-circle", "#ffb142");

	$tabla = "faq_temas";
	$query = getQueryName($tabla);
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla <b>$tabla</b>...");

		$sql = "CREATE TABLE $tabla (
					id int(10) unsigned NOT NULL auto_increment,
					descripcion varchar(60) NOT NULL,
					orden int(11) NOT NULL,
					PRIMARY KEY  (id),
					UNIQUE INDEX uq_$tabla (descripcion)
					) ENGINE = InnoDB;";

		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Temas", $menu, "descripcion", 1, 1, 1, "orden, descripcion", 10, "images/book.png");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);
	}

	$field = "introduccion";
	$tabla = "faq_temas";
	$query = getQueryName($tabla);
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoText($tabla, $field, 0, "");
		sc3generateFieldsInfo($tabla);
	}

	$tabla = "faq_preguntas";
	$query = getQueryName($tabla);
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla <b>$tabla</b>...");

		$sql = "CREATE TABLE $tabla (
					id int(10) unsigned NOT NULL auto_increment,
					idtema int(10) unsigned NOT NULL,
					pregunta varchar(80) NOT NULL,
					orden int(11) NOT NULL,
					respuesta text NULL,
					PRIMARY KEY  (id),
					UNIQUE INDEX uq_$tabla (pregunta)
					) ENGINE = InnoDB;";

		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Preguntas", $menu, "pregunta", 1, 1, 1, "idtema, orden, descripcion", 10, "images/scayuda.gif");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		$field = "idtema";
		sc3addFk($tabla, $field, "faq_temas");
		sc3addlink($query, $field, getQueryName("faq_temas"));
	}


	$tabla = "faq_preguntas_recursos";
	$query = getQueryName($tabla);
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla <b>$tabla</b>...");

		$sql = "CREATE TABLE $tabla (
					id int(10) unsigned NOT NULL auto_increment,
					idpregunta int(10) unsigned NOT NULL,
					recurso varchar(200) NOT NULL,
					orden int(11) NOT NULL,
					PRIMARY KEY  (id),
					UNIQUE INDEX uq_$tabla (idpregunta, recurso)
					) ENGINE = InnoDB;";

		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Recursos", "", "id", 1, 1, 1, "idpregunta, orden", 10, "images/png.gif", 1, "id");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		$field = "recurso";
		sc3updateField($query, $field, "recurso", 1, "", 1);

		$field = "idpregunta";
		sc3addFk($tabla, $field, "faq_preguntas", "id", true);
		sc3addlink($query, $field, getQueryName("faq_preguntas"), 1);
	}

	$bd->close();
}
