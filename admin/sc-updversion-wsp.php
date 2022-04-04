<?php


//11-ago-2021: primera version mensajes WhatsApp
function sc3UpdateVersionWspSalimos()
{
	$bd = new BDObject();

	$menu = "WhatsApp";
	$perfil = "WhatsApp Server";

	$tabla = "wsp_mensajes";
	$query = getQueryName($tabla);
	if (!$bd->existeTabla($tabla)) {

		sc3AgregarMenu($menu, 400, "fa fa-whatsapp", "#25D366");
		sc3AgregarPerfil($perfil);

		echo ("<br>creando tabla <br>$tabla</br>...");

		$SQL = "CREATE TABLE `$tabla` 
				( 
					`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
					idempresa int(10) unsigned not null,
					fecha DATETIME not null,
					fecha_envio DATETIME,
					guid VARCHAR(80) not null,
					destino VARCHAR(100) not null,
					tema VARCHAR(100),
					mensaje VARCHAR (1000) not null,
					adjuntos VARCHAR(500),
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(`guid`)
				)
				ENGINE = InnoDB;";

		$bd->execQuery($SQL);

		sc3agregarQuery($query, $tabla, "Mensajes", $menu, "", 1, 1, 1, "nombre", 8, "images/wri.gif");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		$grupo = "Mensaje";
		sc3setGroup($tabla, "destino", $grupo);
		sc3setGroup($tabla, "tema", $grupo);
		sc3setGroup($tabla, "mensaje", $grupo);
		sc3setGroup($tabla, "adjuntos", $grupo);

		$field = "idempresa";
		sc3addlink($query, $field, "qb2bempresas");
		sc3addFk($tabla, $field, "b2b_empresas");

		sc3addFilter($query, "enviados", "t1.fecha_envio is not null");
		sc3addFilter($query, "sin enviar", "t1.fecha_envio is null");
	}

	$field = "chatid";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, 0, "", "", 250);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3updateFieldHelp($query, $field, "ID Chat.");
	}

	$field = "queueNumber";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, 0, "", "");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3updateFieldHelp($query, $field, "Numero en cola.");
	}

	$field = "checksum";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, 0, "", "", 250);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3updateFieldHelp($query, $field, "Checksum.");
	}

	$url = "app-wsp-cron.php";
	$opid = sc3AgregarOperacion("Enviar pendientes", $url, "images/arrow_rotate_clockwise.png", "Enviar todos los mensajes pendientes", "", $menu, 0, $perfil);

	$url = "sc-enviarwsp.php";
	$opid = sc3AgregarOperacion("Enviar WhatsApp", $url, "images/images/wri.gif", "Escribirle un mensaje a alguien", "", $menu, 0, $perfil);

	$perfil = "WhatsApp Cliente";
	sc3AgregarPerfil($perfil);
	$url = "app-srv-wspfacturacion.php";
	$opid = sc3AgregarOperacion("Enviar por WhatsApp", $url, "images/whatsapp.png", "Enviar facturas por WhatsApp", "srv_periodos", "", 0, $perfil);

	$url = "app-ema-wsp.php";
	$opid = sc3AgregarOperacion("Enviar WhatsApps", $url, "images/whatsapp.png", "Enviar notificaciones por WhatsApp", "", "emailing", 0, $perfil);

	$perfil = "WhatsApp Server";
	$url = "app-wsp-purgar-mensajes.php";
	sc3AgregarOperacion("Eliminar Mensajes", $url, "images/delete.gif", "Eliminar mensajes de la cola.", "wsp_mensajes", "", 1, $perfil);

	$perfil = "WhatsApp Cliente";
	$url = "sc-wsppdf.php";
	sc3AgregarOperacion("Enviar WhatsApp", $url, "images/whatsapp.png", "Enviar comprobante por WhatsApp", "cja2_comprobantes", "", 0, $perfil, "", 1, "");

	$url = "app-eco-wspexpensas.php";
	$opid = sc3AgregarOperacion("Enviar por WhatsApp", $url, "images/whatsapp.png", "Enviar Expensas por WhatsApp", "eco_periodos", "", 0, $perfil);

	$field = "masivo";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, 0, "fecha", 1);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
	}

	$SQL = "UPDATE wsp_mensajes 
			SET masivo = 1 
			WHERE masivo IS NULL";
	$bd->execQuery($SQL);

	$bd->close();
}
