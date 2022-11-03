<?php
/**
 * Este script contiene la actualización de versión del módulo
 * clientes potenciales.
 */

/**
 * agregarOperCliPot_CambiarEstado
 * Permite cambiar el estado de un cliente potencial.
 * @return void
 */
function agregarOperCliPot_CambiarEstado() {
	$opid = sc3AgregarOperacion(
		"Cambiar estados", 
		"der-clipot-cambiar-estado.php", 
		"ico/status_closed.ico", 
		"Permite cambiar el estado a un cliente potencial.", 
		"clipot_registros", 
		"", 
		0, 
		"Root", 
		"", 
		0, 
		"clipot_registros");
}

/**
 * agregarOperCliPot_AgregarNotas
 * Agrega la operación para tomar notas en los clientes potenciales
 * para su seguimiento.
 * @return void
 */
function agregarOperCliPot_AgregarNotas() {
	$opid = sc3AgregarOperacion(
		"Registrar notas", 
		"der-clipot-registrar-notas.php", 
		"ico/note.ico", 
		"Permite registrar las notas que se requieren agregar durante el proceso de negociación del cliente.", 
		"clipot_registros", 
		"", 
		0, 
		"Root", 
		"", 
		0, 
		"clipot_registros");
}

/**
 * agregarCpoCliPotRegistro
 * Agrego campos a la tabla clipot_registros.
 * @return void
 */
function agregarCpoCliPotRegistro() {
	$tabla = "clipot_registros";
	$query = "clipot_registros";
	$campo = "comentarios";

	if (!sc3existeCampo($tabla, $campo)) {
		echo "<br>Agregando campo $campo a la tabla $tabla...";
		$sql = "ALTER TABLE $tabla ADD $campo TEXT NULL";
		$objBd = new BDObject();
		$objBd->execQuery($sql);
		$objBd->close();
		sc3generateFieldsInfo($tabla);
		sc3updateField($query, $campo, "Comentarios", 0);
	}
}
?>