<?php
/*
 * Nombre: DBConnection
 * Desarrollado por: Zulli, Leonardo Diego
 * Descripción: Permite manipular la conexión con la base de datos
 * Fecha: 21/10/2016
*/

class DBConnection {
	public static function Connect() {
		$link = mysqli_connect(Configuracion::$server, Configuracion::$uid, 
				Configuracion::$password);
		mysqli_select_db($link, Configuracion::$dbName);
		return $link;
	}
	
	public static function Close(&$link) {
		mysqli_close($link);
	}

	public static function BeginTransaction(&$linkDB) {
		$r = mysqli_query($linkDB, "START TRANSACTION");
	}

	public static function Rollback(&$linkDB) {
		$r = mysqli_query($linkDB, "ROLLBACK");
	}

	public static function Commit(&$linkDB) {
		$r = mysqli_query($linkDB, "COMMIT");
	}
}
