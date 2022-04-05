<?php
class DBDataFormat {
	public static function MySqlDate($fecha) {
		if (!strpos($fecha, "-")) {
			$aFecha = explode("/", $fecha);
		}
		else {
			$aFecha = explode("-", $fecha);
		}
		return strval($aFecha[0]) . strval($aFecha[1]) . strval($aFecha[2]);
	}
}

?>