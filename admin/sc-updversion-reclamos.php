<?php

/**
 * Reclamos
 */

//19-may-2021: salimos con reclamos
function sc3UpdateVersionReclamosSalimos()
{

	$bd = new BDObject();

	$perfil = "Alta Reclamos";
	sc3AgregarPerfil($perfil);

	$perfil = "Jefe Plantel";
	sc3AgregarPerfil($perfil);

	$tabla = "recla";
	$query = getQueryName($tabla);
	$field = "idperfil";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoint($tabla, $field, 1, "desc_recla");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3updateFieldHelp($query, $field, "Url alterna.");
		sc3UpdateQueryFields($query, $tabla);

		sc3addFk($tabla, "idperfil", "sc_perfiles", "id", false);
		sc3addlink($query, "idperfil", "qperfiles", 0);
	}


	$tabla = "recla_con_historial";
	$query = $tabla;
	$perfil = "reclamos";
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla <b>$tabla</b>...");

		$sql = "CREATE TABLE $tabla (
					id int(10) unsigned NOT NULL auto_increment,
					id_nro_recla int(11) NOT NULL,
					fecha datetime not null,
					idusuario INTEGER NOT NULL,
					descripcion varchar(500) NOT NULL,
					PRIMARY KEY  (id),
					UNIQUE INDEX uq_$tabla (id_nro_recla, fecha, idusuario, descripcion)
					) ENGINE = InnoDB;";

		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Historial", "", "descripcion", 1, 1, 0, "fecha desc", 10, "images/ctadetalles.png", 1, "id");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		$field = "id_nro_recla";
		sc3addlink($query, $field, "recla_con", true);
		sc3addFk($tabla, $field, "recla_con", "id_nro_recla", true);

		$field = "idusuario";
		sc3addlink($query, $field, "sc_usuarios", true);
		sc3addFk($tabla, $field, "sc_usuarios", "id", true);
	}

	$tabla = "recla_con";
	$query = $tabla;
	$field = "direccion";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, 0, "servicio");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
	}

	$tabla = "recla_con";
	$query = $tabla;
	$field = "ultima_modificacion";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFecha($tabla, $field, 0, "");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
	}
}
