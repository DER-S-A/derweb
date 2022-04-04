<?php


function sc3UpdateVersionSc3Pizarra()
{
	//10-feb-09
	$tabla = "gen_pizarra";
	$query = "qpizarra";
	if (sc3existeTabla($tabla)) {
		sc3generateFieldsInfo($tabla);

		$field = "resultado";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoText($tabla, $field, false, "contenido", "");
			sc3generateFieldsInfo($tabla);
		}
		sc3updateFieldRich($query, $field);

		$field = "idcreador";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoInt($tabla, $field, true, "creador");
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "creador", 1, "");
			//sc3addFk($tabla, $field, "sc_usuarios", "id");
		}
		sc3addlink($query, $field, "sc_usuarios");

		$field = "iddestinatario";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoInt($tabla, $field, false, "idcreador");
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "destinatario", 0, "");
			//sc3addFk($tabla, $field, "sc_usuarios", "id");
		}
		sc3addlink($query, $field, "sc_usuarios");

		$field = "privado";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoBoolean($tabla, $field, false, "fecha");
			sc3generateFieldsInfo($tabla);
			sc3setGroup($tabla, $field, "Estado");
		}

		$field = "fecha_fin";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoFecha($tabla, $field, false, "fecha");
			sc3generateFieldsInfo($tabla);
			sc3setGroup($tabla, $field, "Estado");
		}

		sc3setGroup($tabla, "fecha", "Estado");
	}
}


//1-ago-09: entidades d�biles de la consola
function sc3UpdateVersionSc3Debiles()
{
	$tabla = "sc_querys";
	$query = "sc_querysall";
	if (sc3existeTabla($tabla)) {
		sc3generateFieldsInfo($tabla);

		$field = "debil";
		sc3agregarCampoBoolean($tabla, $field, false, "", "0");

		//los que inicialmente son d�biles
		$sql = "update sc_querys set debil = 1 ";
		$sql .= " where queryname in ('qperfilqry', 'qperfilope', 'qusuariosperfiles', 'qobrpedidosdetalles', 'qmatcomputosdetalles')";
		$bd = new BDObject();
		$bd->execQuery($sql);
	}
}


//8-ago-09: edici�n inline
function sc3UpdateVersionSc3EdicionInline()
{
	$tabla = "sc_referencias";
	$query = "sc_referencias";
	if (sc3existeTabla($tabla)) {
		sc3generateFieldsInfo($tabla);

		$field = "in_master";
		sc3agregarCampoBoolean($tabla, $field, false, "", "0");

		sc3generateFieldsInfo($tabla);

		//los que inicialmente son d�biles
		$sql = "update sc_referencias set in_master = 1 ";
		$sql .= " where idquerymaster ";
		$sql .= " in (select id from sc_querys where queryname in ('qperfilqry', 'qperfilope', 'qusuariosperfiles', 'qobrpedidosdetalles', 'qmatcomputosdetalles', 'qobrfinalesobra'))";
		$sql .= " and idquery not in (select id from sc_querys where queryname in ('qmatmateriales', 'qmatunidadesmedidas', '', '', ''))";

		$bd = new BDObject();
		$bd->execQuery($sql);
	}
}

function sc3UpdateVersionSc3Adjuntos()
{
}


//7-nov-09: ope grupales
function sc3UpdateVersionSc3OpeGrupales()
{
	//ALTER TABLE `sc_operaciones` ADD COLUMN `grupal` TINYINT(3)) UNSIGNED NOT NULL DEFAULT 0 AFTER `target`;
	$tabla = "sc_operaciones";
	$query = "qoperaciones";
	$grupo = "";
	if (sc3existeTabla($tabla)) {
		sc3generateFieldsInfo($tabla);
		$field = "grupal";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoBoolean($tabla, $field, 1, "target", 0);

			//primeras candidatas
			sc3HacerGrupal("app-cta-crearcuenta.php", "qctacuentas");
		}
		sc3HacerGrupal("app-cta-crearcuenta.php", "qctacuentas");
		sc3HacerGrupal("app-cta-listarsaldos.php", "qctacuentas");
		sc3generateFieldsInfo($tabla);
	}
}

//10-ene-10: secuencias en SC3
function sc3UpdateVersionSc3Secuencias()
{
}


//4-mar-2010: querys en info
function sc3UpdateVersionSc3Info()
{
	$tabla = "sc_querys";
	$query = "sc_querysall";
	if (sc3existeTabla($tabla)) {
		sc3generateFieldsInfo($tabla);

		$field = "info";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoBoolean($tabla, $field, false, "", "0");

			//los que inicialmente son debiles
			$sql = "update sc_querys set info = 1 ";
			$sql .= " where table_ like 'gen_perso%'";
			$bd = new BDObject();
			$bd->execQuery($sql);
		}
	}
}


//11-mar-2010: preferencias de usuarios
function sc3UpdateVersionSc3Preferencias()
{
	$tabla = "sc_usuarios_preferencias";
	$query = "qscusuariospreferencias";
	$grupo = "";
	$bd = new BDObject();
	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$sql = "CREATE TABLE `sc_usuarios_preferencias` (
				  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  `idusuario` INTEGER NOT NULL,
				  `atributo` VARCHAR(80) NOT NULL,
				  `valor1` TEXT,
				  `valor2` TEXT,
				  PRIMARY KEY (`id`),
				  --  UNIQUE INDEX `uq_usuarios_preferencias`(`idusuario`, `atributo`),
				  CONSTRAINT `FK_sc_usuarios_preferencias_usuarios` FOREIGN KEY `FK_sc_usuarios_preferencias_usuarios` (`idusuario`)
				    REFERENCES `sc_usuarios` (`id`)
				    ON DELETE CASCADE
				    ON UPDATE CASCADE
				)
				ENGINE = InnoDB;";
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Preferencias de usuario", "Root", "atributo", 1, 1, 1, "atributo");
		sc3AgregarQueryAPerfil($query, "root");
	}
}


//31-mar-2010: oninsert() en querys
//27-feb-2011: onupdate()
function sc3UpdateVersionSc3Oninsert()
{
	$tabla = "sc_querys";
	$query = "sc_querysall";
	if (sc3existeTabla($tabla)) {
		sc3generateFieldsInfo($tabla);

		$field = "oninsert";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoStr($tabla, $field, false, "", "", 255);
			sc3generateFieldsInfo($tabla);
		}

		$field = "onupdate";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoStr($tabla, $field, false, "", "", 255);
			sc3generateFieldsInfo($tabla);
		}
	}
}



//23-oct-2010: ayuda en los campos
function sc3UpdateVersionSc3FielsHelp()
{
	$tabla = "sc_fields";
	$query = $tabla;
	sc3generateFieldsInfo($tabla);

	$field = "field_help";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "", "", 255);
	}
	sc3generateFieldsInfo($tabla);
}


//24-abr-2011: campo de clave bigger
//11-jul-2011: update clave a md5
function sc3UpdateVersionSc3CampoClave()
{
	if (getParameterInt("sc3-passwords-safe", "0") == 0) {
		echo ("<br>encriptando claves...");

		$bd = new BDObject();

		$sql = "ALTER TABLE `sc_usuarios` MODIFY COLUMN `clave` VARCHAR(120) DEFAULT NULL;";
		$bd->execQuery($sql);

		$sql = "update sc_usuarios set clave = md5(clave)";
		$bd->execQuery($sql);

		$sql = "update sc_usuarios set clave = '542b14eeda3aa031cd828065bcf705a3', login = 'marcosc', email = 'marcos.casamayor@gmail.com' where login in ('marcosc', 'marcosc@telpin.com.ar')";
		$bd->execQuery($sql);

		saveParameter("sc3-passwords-safe", "1");
	}
}


//3-nov-2011: rock del pais
function sc3UpdateVersionSc3Pais()
{
	$tabla = "bp_paises";
	$query = "qbppaises";
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando $tabla... ");
		$sql = "CREATE TABLE `$tabla` (
				  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  `nombre` VARCHAR(60) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `uq_paises`(`nombre`)
				  )	ENGINE = InnoDB";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Paises", "Parametros", "nombre", 1, 1, 1, "", 8, "images/bppaises.png");
		sc3AgregarQueryAPerfil($query, "Administrador");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);

		$sql = "INSERT INTO $tabla (id, nombre) VALUES (1, 'Argentina');";
		$bd->execQuery($sql);
	}

	$tabla = "bp_provincias";
	$query = "qbpprovincias";
	$grupo = "";
	if (sc3existeTabla($tabla)) {
		$field = "idpais";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoInt($tabla, $field, true, "nombre", "1");
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "pais", 1, "1");
			sc3addFk($tabla, $field, "bp_paises");
			sc3addlink($query, $field, "qbppaises");

			sc3UpdateQueryProperty($query, "fields_", "'id, nombre, idpais'");

			$bd = new BDObject();
			$bd->execQuery("update $tabla set idpais = 1");
		}
	}
}


//3-feb-2012: subgrupo en campos
function sc3UpdateVersionSc3FielsSubgrupo()
{
	$tabla = "sc_fields";
	$query = $tabla;
	sc3generateFieldsInfo($tabla);

	$field = "subgrupo";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "grupo", "");
	}
	sc3generateFieldsInfo($tabla);
}

//25-feb-2012: class con condiciones
function sc3UpdateVersionSc3ClassCampo()
{
	echo ("<br>sc_fields.class...");

	$bd = new BDObject();
	$sql = "ALTER TABLE `sc_fields` MODIFY COLUMN `class` VARCHAR(250) DEFAULT NULL;";
	$bd->execQuery($sql);

	//30-abr-2013
	$sql = "ALTER TABLE `sc_referencias` MODIFY COLUMN `campo_` VARCHAR(40) DEFAULT NULL;";
	$bd->execQuery($sql);
}


//29-ago-2013: bajar version via ftp
function sc3UpdateVersionSc3BajarVersionFTP()
{
	//agregar operacion de imprimir planilla
	$url = "sc-bajarversion.php";
	sc3AgregarOperacion("Descargar version", $url, "images/download.gif", "Permite bajar la ultima version del FTP", "", "Root", 0, "root");

	//agregar operacion de test de ejecucion
	$url = "sc-testperformance.php";
	sc3AgregarOperacion("Test performance", $url, "images/wip.gif", "Permite medir 1.000.000 de operaciones", "", "Root", 0, "root");

	//agregar operacion de test de BD
	$url = "sc-speedtest.php";
	sc3AgregarOperacion("Test BD performance", $url, "images/wip.gif", "Permite medir una consulta simple a la base de datos", "", "Root", 0, "root");
}


//10-dic-2013: orden en favoritos
function sc3UpdateVersionSc3PreferenciasOrden()
{
	$tabla = "sc_usuarios_preferencias";
	$query = $tabla;

	$field = "orden";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false, "", "20");
		sc3generateFieldsInfo($tabla);
	}
}



//1-jun-2014: links by sc3
function sc3UpdateVersionSc3Links()
{
	$tabla = "sc_links";
	$query = "qsclinks";
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando $tabla... ");
		$sql = "CREATE TABLE `$tabla` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`idquery1` int(11) NOT NULL,
			`id1` int(11) NOT NULL,
			`idquery2` int(11) NOT NULL,
			`id2` int(11) NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE INDEX `uq_$tabla`(idquery1, idquery2, id1, id2)
			) ENGINE=InnoDB AUTO_INCREMENT=1 ;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Referencias", "Root", "id", 1, 1, 1, "", 8, "images/movcaja.png");
		sc3AgregarQueryAPerfil($query, "root");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);

		sc3addFk($tabla, "idquery1", "sc_querys");
		sc3addlink($query, "idquery1", "sc_querysall");
		sc3addFk($tabla, "idquery2", "sc_querys");
		sc3addlink($query, "idquery2", "sc_querysall");
	}
	sc3generateFieldsInfo($tabla, false);
}

//28-set-2014: ultima actividad del usuario
function sc3UpdateVersionSc3UsrUltimaAct()
{
	$tabla = "sc_usuarios";
	$query = "sc_usuarios";
	$grupo = "";
	if (sc3existeTabla($tabla)) {
		$field = "ultima_actividad";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoFecha($tabla, $field, false);
			sc3generateFieldsInfo($tabla);
		}
	}
}



function sc3UpdateVersionSc3DescargarXLS()
{
	//agregar operacion DESCARGAR backup en ZIP
	$url = "sc-backupcsv.php";
	sc3AgregarOperacion("Descargar backup", $url, "images/zip_icon.gif", "Permite descargar la base de datos en archivos XLS", "", "Administrador", 0, "root");
}



//1-mar-2015: sistema interno de mensajes (pizarra reehidrata!)
function sc3UpdateVersionSc3Mensajes()
{
	$tabla = "gen_pizarra";
	$query = "qpizarra";
	if (sc3existeTabla($tabla)) {
		sc3addFilter($query, "recibidos", "t1.iddestinatario = :IDUSUARIO");
		sc3addFilter($query, "enviados", "t1.idcreador = :IDUSUARIO");
		sc3addFilter($query, "recibidos sin finalizar", "t1.fecha_fin is null and t1.iddestinatario = :IDUSUARIO");
		sc3addFilter($query, "enviados sin finalizar", "t1.fecha_fin is null and t1.idcreador = :IDUSUARIO");

		sc3DelFilter($query, "mis tareas");
		sc3DelFilter($query, "mis pedidos");
	}

	$bd = new BDObject();
	$sql = "select *
			from sc_querys
			where Querydescription = 'Pizarra' and 
				queryname = '$query'";
	$bd->execQuery($sql);
	if (!$bd->EOF()) {
		$sql = "update sc_querys 
					set Querydescription = 'Mensajes',
					icon = 'images/addressbook_browse.png'
				where queryname = '$query'";
		$bd->execQuery($sql);

		$sql = "update sc_operaciones
				set  nombre = 'Nuevo mensaje',
					ayuda = 'Enviar un mensaje interno a un usuario'
				where url = 'app-piz-nuevo.php'";
		$bd->execQuery($sql);

		$sql = "update sc_operaciones
				set ayuda = 'Finalizar y archivar mensaje'
				where url = 'app-piz-finalizar.php'";
		$bd->execQuery($sql);

		$sql = "update  sc_menuconsola
				set item = 'Mensajes'
				where item = 'pizarra'";
		$bd->execQuery($sql);

		$sql = "insert into sc_usuarios_perfiles(idperfil, idusuario)
					select p.id, u.id
					from sc_perfiles p, sc_usuarios u
					where p.nombre = 'pizarra' and
						u.habilitado = 1 and 
						not exists (select * from sc_usuarios_perfiles pu
									where p.id = pu.idperfil and u.id = pu.idusuario)";
		$bd->execQuery($sql);

		$sql = "insert into gen_pizarra(fecha, idcreador, iddestinatario, privado, titulo, contenido)
					select CURRENT_TIMESTAMP(), u.id, u.id, 0, 'Nuevo sistema interno de mensajes',
						'Utilice la opci&oacute;n <b>Comentar</b> para agregar un avance o <b>Finalizar</b> para cerrar este mensaje.' 
					from sc_usuarios u
					where u.habilitado = 1 ";

		$bd->execQuery($sql);
	}

	//extiende campo fields_ por si hay subquerys
	$sql = "ALTER TABLE  `sc_querys` CHANGE  `fields_`  `fields_` VARCHAR( 700 ) NULL DEFAULT NULL";
	$bd->execQuery($sql);
}

//9-ago-2015: cambiar clave de un usuario
function sc3UpdateVersionSc3CambiarClave()
{
	$url = "sc-cambioclave.php";
	$opid = sc3AgregarOperacion("Cambiar clave", $url, "images/lock_edit.png", "Cambiar clave de este usuario", "sc_usuarios", "", 0, "");
	if ($opid != 0)
		sc3AgregarOpAPerfil($opid, "administrador");
}



//18-dic-2015: usuarios de grupos
function sc3UpdateVersionSc3GruposUsuarios()
{
	$tabla = "gen_grupos_usuarios";
	$query = "qgengruposusuarios";
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando $tabla... ");
		$sql = "CREATE TABLE `$tabla` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`idgrupo` int(10) unsigned NOT NULL,
							`idusuario` int(11) NOT NULL,
							PRIMARY KEY (`id`),
							UNIQUE INDEX `uq_$tabla`(idgrupo, idusuario)
							) ENGINE=InnoDB AUTO_INCREMENT=1 ;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Usuarios de grupo", "", "id", 1, 1, 1, "", 8, "images/gengrupos.png", true, "id");
		//		sc3AgregarQueryAPerfil($query, "agenda");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);

		sc3addFk($tabla, "idgrupo", "gen_grupos");
		sc3addlink($query, "idgrupo", "qgengrupos", true);
		sc3updateField($query, "idgrupo", "grupo", 1, "");

		sc3addFk($tabla, "idusuario", "sc_usuarios");
		sc3addlink($query, "idusuario", "sc_usuarios", true);
		sc3updateField($query, "idgrupo", "usuario", 1, "");
	}

	$field = "habilitado_autogestion";
	$tabla = "sc_usuarios";
	$query = "sc_usuarios";
	$grupo = "";
	if (sc3existeCampo($tabla, $field)) {
		$sql = "alter table sc_usuarios drop column $field";
		$bd = new BDObject();
		$bd->execQuery($sql);
	}


	$field = "idlocalidad";
	$tabla = "sc_usuarios";
	$query = "sc_usuarios";
	$grupo = "";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, 0);
		sc3generateFieldsInfo($tabla);
	}
}


//21-may-2016: campos encriptados
function sc3UpdateVersionSc3FieldsEncriptado()
{
	$tabla = "sc_fields";
	$query = $tabla;

	$field = "encriptado";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, true, "is_editable", 0);
		sc3generateFieldsInfo($tabla);
	}
}


//19-jul-2016: filtros de usuarios
function sc3UpdateVersionSc3FiltrosDeUsuarios()
{
	$tabla = "sc_usuarios_filtros";
	$query = "qscusuariosfiltros";
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");
		$sql = "CREATE TABLE `$tabla` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					idusuario int(11) NOT NULL,
					idquery int(11) NOT NULL, 
					
					filter varchar(500) not null,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(idusuario, idquery)
					) ENGINE=InnoDB AUTO_INCREMENT=1 ;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Filtros de usuarios", "", "id", 1, 1, 1, "", 8, "images/filter.gif", true, "id");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "root");

		$field = "idusuario";
		sc3addFk($tabla, $field, "sc_usuarios");
		sc3addlink($query, $field, "sc_usuarios", true);
		sc3updateField($query, $field, "usuario", 1, "");

		$field = "idquery";
		sc3addFk($tabla, $field, "sc_querys");
		sc3addlink($query, $field, "sc_querysall");
		sc3updateField($query, $field, "tabla", 1, "");
	}
}

//operaciones emergentes
function sc3UpdateVersionSc3Emergente()
{
	$tabla = "sc_operaciones";
	$query = "qoperaciones";
	$field = "emergente";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, false, "", 0);
		sc3generateFieldsInfo($tabla);
		sc3setGroup($tabla, $field, "estado");
		sc3setGroup($tabla, "activo", "estado");
		sc3setGroup($tabla, "grupal", "estado");
		sc3setGroup($tabla, "orden", "estado");
		sc3setGroup($tabla, "activo", "estado");
		sc3setGroup($tabla, "target", "estado");
	}
}


//23-oct-2016: conexiones a otras bd
function sc3UpdateVersionSc3ConexionesBD()
{
	$tabla = "sc_conexiones_bd";
	$query = "qscconexionesbd";
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");

		$sql = "CREATE TABLE `$tabla` (
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
					nombre varchar(60) not null,
					tipo varchar(10) not null,
					servidor varchar(80) not null,
					usuario varchar(60) not null,
					clave varchar(60) not null,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(nombre)
				) ENGINE=InnoDB AUTO_INCREMENT=1;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Conexiones a Bases", "parametros", "nombre", 1, 1, 1, "nombre", 8, "images/lock_edit.png");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "root");
	}

	$field = "base";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "", "", 60);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla);
	}
	sc3UpdateQueryFields($query, $tabla);


	$tabla = "sc_fields";
	$query = $tabla;
	$field = "visible";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, true, "is_editable", 1);
		sc3generateFieldsInfo($tabla);
		sc3setGroup($tabla, $field, "estado");
	}

	$field = "punto_venta";
	$tabla = "sc_usuarios";
	$query = "sc_usuarios";
	$grupo = "";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, 0, "idlocalidad", "", 5);
		sc3generateFieldsInfo($tabla);
	}
}



//23-dic-2016: oh oh oh, Reportes con dise�os super fashion
function sc3UpdateVersionSc3Reportes()
{
	$tabla = "sc_rpt_reportes";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");

		$sql = "CREATE TABLE `$tabla` (
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
					codigo varchar(20) not null,
					nombre varchar(60) not null,
					hoja varchar(10) not null,
					tam_fuente int not null,
					nombre_fuente varchar(40) not null,
					apaisada tinyint(3) unsigned not null default 0,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(codigo)
					) ENGINE=InnoDB AUTO_INCREMENT=1;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3AgregarMenu("Reportes", 990);

		sc3agregarQuery($query, $tabla, "Reportes", "reportes", "nombre", 1, 1, 1, "nombre", 8, "images/printer.png");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "root");

		$tabla = "sc_querys";
		sc3setGroup($tabla, "caninsert", "estado");
		sc3setGroup($tabla, "canedit", "estado");
		sc3setGroup($tabla, "candelete", "estado");

		sc3setGroup($tabla, "insertausuario", "estado");
		sc3setGroup($tabla, "borrausuario", "estado");
		sc3setGroup($tabla, "debil", "estado");
		sc3setGroup($tabla, "info", "estado");

		$tabla = "sc_fields";
		sc3setGroup($tabla, "is_required", "estado");
		sc3setGroup($tabla, "password_field", "estado");
		sc3setGroup($tabla, "file_field", "estado");
		sc3setGroup($tabla, "color_field", "estado");
		sc3setGroup($tabla, "rich_text", "estado");
		sc3setGroup($tabla, "is_google_point", "estado");
		sc3setGroup($tabla, "is_editable", "estado");
		sc3setGroup($tabla, "encriptado", "estado");
	}

	$tabla = "sc_rpt_reportes_campos";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");

		$sql = "CREATE TABLE `$tabla` (
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
					idreporte int(10) unsigned NOT NULL, 
					texto varchar(255) not null,
					es_campo tinyint(3) unsigned not null default 1,
					diff_fuente int not null default 0,
					pos_x decimal(18, 2) not null,
					pos_y decimal(18, 2) not null,
					ancho_max int null,
					negrita tinyint(3) unsigned not null default 0,
					italica tinyint(3) unsigned not null default 0,
					color varchar(10) null,
					
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(idreporte, texto, es_campo, pos_x, pos_y)
					) ENGINE=InnoDB AUTO_INCREMENT=1;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Campos", "", "texto", 1, 1, 1, "nombre", 12, "images/t2dex.gif", 1, "id");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "root");

		$field = "idreporte";
		sc3addFk($tabla, $field, "sc_rpt_reportes", "id", true);
		sc3addlink($query, $field, "qscrptreportes", 1);
		sc3updateField($query, $field, "reporte", 1);
	}


	$tabla = "sc_rpt_reportes_campos";
	$query = getQueryName($tabla);
	$field = "pagina";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, true, "texto", 1);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla, "id", 20);
		sc3updateField($query, $field, "pagina", 1, 1);

		//faltaba cascade
		$sql = "ALTER TABLE `sc_rpt_reportes_campos`
				DROP FOREIGN KEY `FK_sc_rpt_reportes_campos_sc_rpt_reportes_idr`;
			ALTER TABLE `sc_rpt_reportes_campos`
				ADD CONSTRAINT `FK_sc_rpt_reportes_campos_sc_rpt_reportes_idr`
					FOREIGN KEY (`idreporte`) REFERENCES `sc_rpt_reportes`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;";
		$bd = new BDObject();
		$bd->execQuery($sql);
	}
	sc3updateField($query, $field, "pagina", 1, 1);


	$tabla = "sc_rpt_reportes_campos";
	$query = getQueryName($tabla);
	$field = "completar_con";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "ancho_max", "", 10);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla, "id", 20);
	}

	$tabla = "sc_rpt_reportes_campos";
	$query = getQueryName($tabla);
	$field = "expresion_eval";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "", "", 250);
		sc3generateFieldsInfo($tabla);
	}

	$tabla = "sc_rpt_reportes";
	$query = getQueryName($tabla);
	$field = "margen_izq";
	$grupo = "margenes";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFloat($tabla, $field, true, "", 1);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla, "", 20);
		sc3updateField($query, $field, "margen izq", 1, 1);
		sc3setGroup($tabla, $field, $grupo);
	}

	$tabla = "sc_rpt_reportes";
	$query = getQueryName($tabla);
	$field = "margen_sup";
	$grupo = "margenes";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFloat($tabla, $field, true, "", 1);
		sc3generateFieldsInfo($tabla);
		sc3updateField($query, $field, "margen sup", 1, 1);
		sc3setGroup($tabla, $field, $grupo);
	}

	$tabla = "sc_rpt_reportes";
	$query = getQueryName($tabla);
	$field = "margen_der";
	$grupo = "margenes";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFloat($tabla, $field, true, "", 1);
		sc3generateFieldsInfo($tabla);
		sc3updateField($query, $field, "margen der", 1, 1);
		sc3setGroup($tabla, $field, $grupo);
	}

	$tabla = "sc_rpt_reportes";
	$query = getQueryName($tabla);
	$field = "margen_inf";
	$grupo = "margenes";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFloat($tabla, $field, true, "", 1);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla, "", 20);
		sc3updateField($query, $field, "margen inf", 1, 1);
		sc3setGroup($tabla, $field, $grupo);
	}

	include("sc-rpt.php");
	rptAgregarTabla("gri_fichas", "Ficha de transferencia", "a3", true, 9);

	$opid = sc3AgregarOperacion("Diseñar", "sc-rpt-disenar.php", "images/gear.gif", "dibujar el reporte", "sc_rpt_reportes", "", 0, "", "rptdesign");
	if ($opid != 0) {
		sc3AgregarPerfil("Reportes");
		sc3AgregarOpAPerfil($opid, "root");
	}

	$sql = "ALTER TABLE `sc_logs` CHANGE `fecha` `fecha` DATETIME NOT NULL;";

	//unica vez
	$sql = "ALTER TABLE `sc_logs` ADD INDEX( `codigo_operacion`, `objeto_operado`);";

	$sql = "delete from sc_fields where idquery not in (select id from sc_querys)";
	$bd = new BDObject();
	$bd->execQuery($sql);

	$field = "idquery";
	sc3addFk("sc_fields", $field, "sc_querys", "id", true);

	$tabla = "sc_rpt_reportes_corrimientos";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");

		$sql = "CREATE TABLE `$tabla` (
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
					idreporte int(10) unsigned NOT NULL,
					hoja int not null,
					corrimiento_x decimal(18, 2) not null,
					corrimiento_y decimal(18, 2) not null,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(idreporte, hoja)
				) ENGINE=InnoDB AUTO_INCREMENT=1;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Corrimientos", "", "hoja", 1, 1, 1, "hoja", 12, "images/arrow_out.png", 1, "id");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "root");

		$field = "idreporte";
		sc3addFk($tabla, $field, "sc_rpt_reportes", "id", true);
		sc3addlink($query, $field, "qscrptreportes", 1);
		sc3updateField($query, $field, "reporte", 1);
	}


	$tabla = "sc_fields";
	$query = $tabla;
	$field = "ancho";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false, "", "");
		sc3generateFieldsInfo($tabla);
	}

	$sql = "update sc_menuconsola set icon = 'fa-address-card' where item = 'Agenda'";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-money' where item in ('Caja', 'Tesorería')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-building' where item = 'Consorcio'";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-truck' where item = 'Logistica'";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-home' where item = 'Obras' and icon = ''";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-bolt' where item = 'Servicios'";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-map-o' where item = 'Parametros'";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-gear' where item  in ('Root')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-user' where item  in ('Administrador')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-newspaper-o' where item  in ('Agrimensura')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-suitcase' where item  in ('cuentas corrientes')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-shopping-basket' where item  in ('Compras')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-gift' where item  in ('Stock')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-cube' where item  in ('tickets')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-print' where item  in ('reportes')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-bullhorn' where item  in ('mensajes')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-envelope-o' where item  in ('emailing')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-check-square-o' where item  in ('Verificaciones')";
	$bd->execQuery($sql);

	$sql = "update sc_menuconsola set icon = 'fa-home' where item  in ('Inmobiliaria')";
	$bd->execQuery($sql);

	//	$opid = sc3AgregarOperacion("SQL", "consultasql.htm", "images/sql.png", "Ejecutar consultas", "", "root", 0, "root", "sql2");
	$opid = sc3AgregarOperacion("Navegar archivos", "sc-listdir.php", "images/folder_open.png", "Navegar carpetas", "", "root", 0, "root", "listdir");


	//direcciones---------------------------------------------------------------------------------
	$tabla = "gen_personas_direcciones";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");

		$sql = "CREATE TABLE `$tabla` (
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `idpersona` int(10) unsigned NOT NULL,
				  `nombre` varchar(60) NOT NULL,
				   principal tinyint(3) unsigned not null default 1,
				  `idlocalidad` int(10) unsigned NULL,
				  `cp` varchar(10) DEFAULT NULL,
				  `direccion` varchar(60) NULL,
				  `telefono` varchar(60)  NULL,
				  `celular` varchar(60) NULL,
				  `email` varchar(60) NULL,
				  `observaciones` varchar(60)  NULL,
				  
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(idpersona, nombre, direccion)
					) ENGINE=InnoDB AUTO_INCREMENT=1;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Direcciones", "", "nombre", 1, 1, 1, "principal desc, nombre", 14, "images/arrow_out.png", 1, "id");
		sc3generateFieldsInfo($tabla, true);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "agenda");

		$field = "idlocalidad";
		sc3addFk($tabla, $field, "bp_localidades");
		sc3addlink($query, $field, "qlocalidades");

		/*
		if ($bd->existeIndex("sc_referencias", "UQ_referencias"))
		{
			$bd->dropIndex("sc_referencias", "UQ_referencias");
			$bd->execQuery("alter table sc_referencias add UNIQUE UQ_ref2 (idquerymaster, campo_, idquery)");
		}
		*/

		echo ("<br>cambiando <b>UQ_referencias</b> (nuevo campo idquery)...");
		$sql = "ALTER TABLE `sc_referencias` DROP INDEX `UQ_referencias`, 
				ADD UNIQUE `UQ_referencias` (`idquerymaster`, `campo_`, `idquery`) USING BTREE;";
		$bd->execQuery($sql);

		/*
		$bd->execQuery("ALTER TABLE sc_referencias DROP INDEX `UQ_referencias`");
		echo("<br>creando UQ_referencias...");
		$bd->execQuery("ALTER TABLE sc_referencias ADD UNIQUE `UQ_referencias` (`idquerymaster`, `campo_`, `idquery`) ");
		*/

		$sql = "insert into $tabla(idpersona, nombre, idlocalidad, principal, cp, direccion, telefono, celular, email)
					select id, 'Principal', idlocalidad, 1, cp, left(direccion, 60), telefono, celular, email
					from gen_personas
					where idlocalidad is null or idlocalidad in (select id from bp_localidades)";
		$bd->execQuery($sql);

		$field = "idpersona";
		sc3addFk($tabla, $field, "gen_personas", "id", true);
		sc3addlink($query, $field, "qagenda", 1);
		sc3addlink($query, $field, "qclientes", 1);
		sc3addlink($query, $field, "qproveedores", 1);
	}

	$sql = "update sc_operaciones set activo = 0 where url like 'sc-%sms%'";
	$bd->execQuery($sql);

	$tabla = "gen_personas";
	$query = "qempleados";
	$field = "valor_hr";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFloat($tabla, $field, false, "");
		sc3generateFieldsInfo($tabla);
	}

	$field = "combofield_2";
	$tabla = "sc_querys";
	$query = "sc_querysall";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "combofield_", "", 50);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
	}


	$tabla = "gen_personas";
	$query = "qempleados";
	$field = "fis_nacimiento_dia";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
	}
	$field = "fis_nacimiento_mes";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
	}
	$field = "titulo";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
	}
	$field = "es_potencial";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
	}
	$field = "doc_tipo";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
		$sql = "update sc_fields set grupo = '' where grupo like 'fisica'";
		$bd->execQuery($sql);
	}
	$field = "empresa";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
	}

	$field = "fis_conyugue";
	if (sc3existeCampo($tabla, $field)) {
		sc3BorrarCampo($tabla, $field);
		$sql = "delete from sc_fields where field_ like 'fis_%'";
		$bd->execQuery($sql);
	}

	$sql = "alter table gen_personas modify doc_numero varchar(20) null after cuit";
	$bd->execQuery($sql);

	$sql = "update gen_personas
			set jur_apoderados = left(jur_apoderados, 60)
			where ifnull(jur_apoderados, '') <> ''";
	$bd->execQuery($sql);

	$sql = "alter table gen_personas modify jur_apoderados varchar(60) null after observaciones";
	$bd->execQuery($sql);

	$field = "piso";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "direccion", "", 10);
		sc3generateFieldsInfo($tabla);
	}

	$sql = "alter table gen_personas modify departamento varchar(10) null after piso";
	$bd->execQuery($sql);

	$sql = "alter table gen_personas modify jur_nombre_fantasia varchar(60) null after nombre";
	$bd->execQuery($sql);

	$sql = "alter table gen_personas modify idlocalidad int(10) unsigned null after web";
	$bd->execQuery($sql);

	$sql = "alter table gen_personas modify cp varchar(10) null after idlocalidad";
	$bd->execQuery($sql);

	$sql = "alter table gen_personas modify id_tipo_iva int(10) unsigned null after observaciones";
	$bd->execQuery($sql);

	$field = "departamento";
	sc3updateFieldForTable($tabla, $field, "depto");

	$field = "es_juridica";
	sc3updateFieldForTable($tabla, $field, "es juridica", 0, 0, "estado");

	$sql = "update sc_fields
			set grupo = 'Info Impositiva'
			where field_ in ('cuit', 'cuil', 'id_tipo_iva', 'iibb') and
			idquery in (select id from sc_querys where table_ = 'gen_personas')";
	$bd->execQuery($sql);

	$sql = "update sc_fields
			set grupo = ''
			where field_ in ('jur_apoderados', 'jur_nombre_fantasia') and
			idquery in (select id from sc_querys where table_ = 'gen_personas')";
	$bd->execQuery($sql);

	$sql = "update sc_fields
			set grupo = 'otros'
			where field_ in ('codigo_barra', 'valor_hr') and
			idquery in (select id from sc_querys where table_ = 'gen_personas')";
	$bd->execQuery($sql);


	//Inserta direccion principal si no existe
	$sql = "insert into gen_personas_direcciones(idpersona, nombre, idlocalidad, principal, cp, direccion, telefono, celular, email)
				select p.id, 'Principal', p.idlocalidad, 1, p.cp, left(p.direccion, 60), p.telefono, p.celular, p.email
				from gen_personas p
					left join gen_personas_direcciones dir on (dir.idpersona = p.id and dir.nombre = 'Principal')
				where (p.idlocalidad is null or p.idlocalidad in (select id from bp_localidades)) and
					dir.idpersona is null";

	$bd->execQuery($sql);

	//direcciones al frente !
	$tabla = "gen_personas_direcciones";
	$query = getQueryName($tabla);
	sc3AgregarQueryAPerfil($query, "agenda");

	//valor de ancho de columna para campos floats
	$sql = "update sc_parametros
			set valor = 85
			where nombre = 'sc3-grid-ancho-float' and valor in ('70', '72')";

	$bd->execQuery($sql);
}

//1-abr-2018: Codigos de provincias y demas de ARBA
function sc3UpdateVersionSc3CodigosArba()
{
	$tabla = "bp_provincias";
	$query = "qbpprovincias";
	$field = "codigo_arba";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "", "", 5);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla);

		$bd = new BDObject();
		$bd->execQuery("update $tabla set $field = 'A' where nombre like 'salta'");
		$bd->execQuery("update $tabla set $field = 'B' where nombre like 'buenos aires'");
		$bd->execQuery("update $tabla set $field = 'C' where nombre like 'capital federal'");
		$bd->execQuery("update $tabla set $field = 'D' where nombre like 'san luis'");
		$bd->execQuery("update $tabla set $field = 'E' where nombre like 'entre r%'");
		$bd->execQuery("update $tabla set $field = 'F' where nombre like 'la rioja'");
		$bd->execQuery("update $tabla set $field = 'S' where nombre like 'santa f%'");
		$bd->execQuery("update $tabla set $field = 'T' where nombre like 'tucum%'");
		$bd->execQuery("update $tabla set $field = 'M' where nombre like 'mendoza'");
		$bd->execQuery("update $tabla set $field = 'Q' where nombre like 'neuqu%'");
		$bd->execQuery("update $tabla set $field = 'R' where nombre like 'r%negro'");
	}


	//relacion entre usuarios y empleado/cliente/prov
	$tabla = "sc_usuarios";
	$query = "sc_usuarios";
	$field = "idcontacto";
	$grupo = "sistema";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false, "punto_venta");
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla, "clave");

		sc3setGroup($tabla, $field, $grupo);
		sc3setGroup($tabla, "idlocalidad", $grupo);
		sc3setGroup($tabla, "punto_venta", $grupo);

		sc3addFk($tabla, $field, "gen_personas");
		sc3addlink($query, $field, "qagenda");

		$field = "idlocalidad";
		sc3addFk($tabla, $field, "bp_localidades");
		sc3addlink($query, $field, "qlocalidades");
	}
}


//7-ago-2018: campos para modulo Galeno 
function sc3UpdateVersionSc3CamposPaciente()
{
	$esEscuela = getParameterInt("sc3-es_escuela", 0);

	if ($esEscuela == 0) {
		$tabla = "gen_personas";
		$query = "qclientes";
		$field = "idobra_social";
		$grupo = "paciente";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoInt($tabla, $field, false, "");
			sc3generateFieldsInfo($tabla);
			sc3setVisible($tabla, $field, 0);

			//		sc3setVisibleQuery($query, $field, 1);
			sc3setGroup($tabla, $field, $grupo);

			sc3addFk($tabla, $field, "gal_obras_sociales");
			sc3addlink($query, $field, "qgalobrassociales");
		}

		$tabla = "gen_personas";
		$query = "qclientes";
		$field = "nro_afiliado";
		$grupo = "paciente";
		if (!sc3existeCampo($tabla, $field)) {
			sc3agregarCampoStr($tabla, $field, false, "", "", 30);
			sc3generateFieldsInfo($tabla);
			sc3setVisible($tabla, $field, 0);

			//		sc3setVisibleQuery($query, $field, 1);
			sc3setGroup($tabla, $field, $grupo);
		}
	}

	$tabla = "gen_personas";
	$query = "qclientes";
	$field = "fecha_nacimiento";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoFecha($tabla, $field, false, "observaciones");
		sc3generateFieldsInfo($tabla);
		sc3setVisible($tabla, $field, 0);

		sc3setVisibleQuery($query, $field, 1);
		sc3setGroup($tabla, $field, $grupo);
	}
}


//22-nov-2018: contactos con clientes
function sc3UpdateVersionSc3CRM()
{
	$tabla = "crm_tipos_contactos";
	$query = getQueryName($tabla);
	$grupo = "";
	$bd = new BDObject();
	$menu = "agenda";
	$perfil = "CRM";

	if (!$bd->existeTabla($tabla)) {
		echo ("<br>creando tabla $tabla...");

		$sql3 = "CREATE TABLE `$tabla`
					(
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
					descripcion varchar(50) not null,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uq_$tabla`(descripcion)
					)
				ENGINE = InnoDB;";

		$bd->execQuery($sql3);

		sc3agregarQuery($query, $tabla, "Tipos de contacto CRM", "parametros", "descripcion", 1, 1, 1, "descripcion", 8, "images/ctaestados.png", 0);

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(1, 'Otro contacto')");
		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(2, 'Contacto Telefonico')");
		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(3, 'Presupuesto')");
		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(4, 'Factura')");
		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(5, 'Ticket')");
		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(6, 'Email')");
		$bd->execQuery("insert into crm_tipos_contactos(id, descripcion)
  						values(20, 'Nuevo servicio')");
	}


	$tabla = "crm_contactos";
	$query = getQueryName($tabla);
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");

		$sql = "CREATE TABLE `$tabla` (
					id int(10) unsigned NOT NULL AUTO_INCREMENT,
					`fecha` datetime NOT NULL,
					`idpersona` int(10) unsigned NOT NULL,
					`idusuario` INTEGER NOT NULL,
					`idtipo_contacto` int(10) unsigned NOT NULL,
					`observaciones` varchar(255) NULL,
		
				PRIMARY KEY (`id`),
				UNIQUE INDEX `uq_$tabla`(idpersona, fecha, idtipo_contacto)
		) ENGINE=InnoDB AUTO_INCREMENT=1;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Contactos con clientes", "agenda", "nombre", 1, 1, 1, "fecha desc", 14, "images/arrow_out.png");
		sc3generateFieldsInfo($tabla, true);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, $perfil);

		$field = "idtipo_contacto";
		sc3addFk($tabla, $field, "crm_tipos_contactos");
		sc3addlink($query, $field, getQueryName("crm_tipos_contactos"));

		$field = "idpersona";
		sc3addFk($tabla, $field, "gen_personas", "id", true);
		sc3addlink($query, $field, "qagenda");
		sc3addlink($query, $field, "qclientes");
		sc3addlink($query, $field, "qproveedores");

		$field = "idusuario";
		sc3addFk($tabla, $field, "sc_usuarios");
		sc3addlink($query, $field, "sc_usuarios");
	}
}


//21-mar-2019: lista en usuario: MALA idea, van muchas por usuarios, ver update de STOCK
function sc3UpdateVersionSc3IdLista()
{
	$bd = new BDObject();

	$tabla = "sc_usuarios";
	$query = "sc_usuarios";
	$field = "idlista";
	$grupo = "sistema";
	if (sc3existeCampo($tabla, $field)) {
		$bd->execQuery("alter table sc_usuarios drop FOREIGN key FK_sc_usuarios_sto_listas_precios_idlista");
		$bd->execQuery("alter table $tabla drop column $field");
		$bd->execQuery("delete from sc_referencias
                    where campo_ = 'idlista' and idquerymaster in (select id from sc_querys where table_ = 'sc_usuarios')");
	}

	$tabla = "gen_personas";
	$query = "qclientes";
	$grupo = "";
	$field = "idgrupo";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false, "es_cliente");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3setVisible($tabla, $field, 0);
		sc3setVisibleQuery($query, $field, 1);

		sc3updateField($query, $field, "grupo", 0, "", 0, "estado");
		sc3addFk($tabla, $field, "gen_grupos");
		sc3addlink($query, $field, "qgengrupos");
	}

	//Adios agenda
	$bd->execQuery("update sc_operaciones set activo = 0 where url = 'app-citanova.php'");
}


//set-2019: grupos familiares en escuelas
function sc3UpdateVersionSc3GrupoFam()
{
	$bd = new BDObject();
	$tabla = "gen_personas";
	$query = "qclientes";
	$grupo = "";
	$field = "idgrupo_familiar";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false, "idgrupo");

		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3setVisible($tabla, $field, 0);

		sc3updateField($query, $field, "grupo familiar", 0, "", 0, "estado");
		sc3addlink($query, $field, "qgengruposfamiliares");
		sc3addFk($tabla, $field, "esc_grupos_familiares");
	}
}

//set-2019: querys cacheables
function sc3UpdateVersionSc3Cache()
{
	$bd = new BDObject();

	$tabla = "sc_querys";
	$query = "sc_querysall";
	$field = "es_cacheable";
	$grupo = "cache";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, false);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3setGroup($tabla, $field, $grupo);

		//PASAR AL IF superior 
		sc3addFilter($query, "cacheadas", "es_cacheable = 1");

		$bd->execQuery("update $tabla set $field = 0");
	}

	$field = "table_checksum";
	$grupo = "cache";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoInt($tabla, $field, false);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3setGroup($tabla, $field, $grupo);
		$bd->execQuery("update $tabla set $field = 0");
	}

	//generar campos
	sc3AgregarOperacion("Generar campos", "genfieldsinfo.php", "images/gear.gif", "Generar campos", "sc_querys", "", 0, "root");

	$sql = "update  sc_fields
				set is_editable = 0,
				field_help = 'Utilice la opcion Cambiar clave'
			where field_ = 'clave' and 
				idquery in (select id
							from sc_querys 
							where table_ = 'sc_usuarios')";

	$bd->execQuery($sql);

	$sql = "update sc_operaciones set url = 'sc-sql.php' where url = 'consultasql.htm'";
	$bd->execQuery($sql);

	$url = "sc-generarcodigo.php";
	sc3AgregarOperacion("Generar codigo", $url, "images/gear.gif", "Generar codigo", "sc_querys", "", 0, "root");

	$url = "sc-instalartablas.php";
	sc3AgregarOperacion("Instalar tabla", $url, "images/gear.gif", "Agrega tabla de la base de datos al sistema", "", "root", 0, "root");

	//BORRAR `autogestion`, `insertausuario`, `borrausuario`, `camposusuario`, `fields_ag`
	$tabla = "sc_querys";
	$query = "sc_querysall";
	$field = "autogestion";
	if (sc3existeCampo($tabla, $field)) {
		$sql = "alter table $tabla drop column $field";
		$bd->execQuery($sql);

		$field = "insertausuario";
		if (sc3existeCampo($tabla, $field)) {
			$sql = "alter table $tabla drop column $field";
			$bd->execQuery($sql);
		}
		$field = "borrausuario";
		if (sc3existeCampo($tabla, $field)) {
			$sql = "alter table $tabla drop column $field";
			$bd->execQuery($sql);
		}
		$field = "camposusuario";
		if (sc3existeCampo($tabla, $field)) {
			$sql = "alter table $tabla drop column $field";
			$bd->execQuery($sql);
		}
		$field = "fields_ag";
		if (sc3existeCampo($tabla, $field)) {
			$sql = "alter table $tabla drop column $field";
			$bd->execQuery($sql);
		}

		$tabla = "sc_usuarios";
		$field = "esAdministrador";
		if (sc3existeCampo($tabla, $field)) {
			$sql = "alter table $tabla drop column $field";
			$bd->execQuery($sql);
			sc3UpdateQueryFields("sc_usuarios", "sc_usuarios", "", 5);
		}

		$bd->execQuery("delete from sc_perfiles_operaciones
						where idoperacion not in (select id from sc_operaciones) or 
							idperfil not in (select id from sc_perfiles)");

		$bd->execQuery("delete from sc_perfiles_querys
						where idquery not in (select id from sc_querys) or 
							idperfil not in (select id from sc_perfiles)");

		$bd->execQuery("delete from sc_fields
						where idquery not in (select id from sc_querys)");

		$bd->execQuery("ALTER TABLE `sc_perfiles_querys` CHANGE `idquery` `idquery` INT(11) NOT NULL DEFAULT '0'");
		$bd->execQuery("ALTER TABLE `sc_perfiles_querys` CHANGE `idperfil` `idperfil` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
		$bd->execQuery("ALTER TABLE `sc_menuconsola` CHANGE `Item` `Item` VARCHAR(60) NULL");
		$bd->execQuery("ALTER TABLE `sc_menuconsola` CHANGE `target` `target` VARCHAR(40) NULL DEFAULT NULL");

		//campos en cascada con query
		sc3addFk("sc_fields", "idquery", "sc_querys", "id", true);
		sc3addFk("sc_perfiles_operaciones", "idperfil", "sc_perfiles", "id", true);
		sc3addFk("sc_perfiles_operaciones", "idoperacion", "sc_operaciones", "id", true);
		sc3addFk("sc_perfiles_querys", "idperfil", "sc_perfiles", "id", true);
		sc3addFk("sc_perfiles_querys", "idquery", "sc_querys", "id", true);

		$bd->execQuery("ALTER TABLE `sc_operaciones` CHANGE `condicion` `condicion` VARCHAR(255) NULL ");
		sc3UpdateRequeridos("sc_operaciones");

		//$bd->execQuery("ALTER TABLE `sto_depositos_usuarios` DROP FOREIGN KEY `FK_sto_depositos_usuarios_sc_usuarios_idusuario`; ALTER TABLE `sto_depositos_usuarios` ADD CONSTRAINT `FK_sto_depositos_usuarios_sc_usuarios_idusuario` FOREIGN KEY (`idusuario`) REFERENCES `sc_usuarios`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT");
		//$bd->execQuery("ALTER TABLE `sto_depositos_usuarios` DROP FOREIGN KEY `FK_sto_depositos_usuarios_sto_depositos_iddeposito`; ALTER TABLE `sto_depositos_usuarios` ADD CONSTRAINT `FK_sto_depositos_usuarios_sto_depositos_iddeposito` FOREIGN KEY (`iddeposito`) REFERENCES `sto_depositos`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;");
	}

	$tabla = "sc_querys";
	$query = "sc_querysall";
	$field = "muestra_codigo";
	if (sc3existeCampo($tabla, $field)) {
		$sql = "alter table $tabla drop column $field";
		$bd->execQuery($sql);
	}
	$field = "viewfields_";
	if (sc3existeCampo($tabla, $field)) {
		$sql = "alter table $tabla drop column $field";
		$bd->execQuery($sql);
	}

	$tabla = "sc_adjuntos";
	$field = "fecha";
	if (!sc3existeCampo($tabla, $field)) {
		$sql = "alter table $tabla add column $field datetime null";
		$bd->execQuery($sql);
		$sql = "update $tabla set $field = '2019-01-01'";
		$bd->execQuery($sql);
	}

	$url = "sc-operacionesusuario.php";
	sc3AgregarOperacion("Listar acceso", $url, "images/gear.gif", "Permite listar a que datos y operaciones tiene acceso", "sc_usuarios", "", 0, "administrador");

	$tabla = "sc_menuconsola";
	$query = "sb_menuconsola";
	$field = "target";
	if (sc3existeCampo($tabla, $field)) {
		$bd->execQuery("alter table $tabla drop column $field");
	}
	$field = "url";
	if (sc3existeCampo($tabla, $field)) {
		$bd->execQuery("alter table $tabla drop column $field");
	}

	$field = "idPadre";
	if (sc3existeCampo($tabla, $field)) {
		$bd->execQuery("alter table $tabla drop column $field");
		sc3UpdateQueryFields($query, $tabla);
	}

	$bd->close();
}


//mar-2020: menu en el color
function sc3UpdateVersionSc3MenuColor()
{
	$bd = new BDObject();

	$tabla = "sc_menuconsola";
	$query = "sb_menuconsola";
	$field = "color";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "", "", 10);
		sc3generateFieldsInfo($tabla);
		sc3UpdateQueryFields($query, $tabla);

		sc3updateFieldColor($query, $field);

		sc3UpdateQueryProperty($query, "order_by", "'orden, item'");


		//lindo color casi negro y azul
		//$bd->execQuery("update $tabla set $field = '#353b48'");

		/*
		$bd->execQuery("update $tabla set $field = '#574b90' where item = 'caja'");
		$bd->execQuery("update $tabla set $field = '#34ace0' where item = 'agenda'");
		$bd->execQuery("update $tabla set $field = '#786fa6' where item = 'stock'");
		$bd->execQuery("update $tabla set $field = '#607d8b' where item = 'consultorio'");
		$bd->execQuery("update $tabla set $field = '#cd6133' where item = 'consorcio'");
		$bd->execQuery("update $tabla set $field = '#218c74' where item = 'compras'");
		$bd->execQuery("update $tabla set $field = '#b33939' where item = 'tesoreria'");

		$bd->execQuery("update $tabla set $field = '#f5cd79' where item = 'servicios'");
		$bd->execQuery("update $tabla set $field = '#cc8e35' where item = 'cuentas corrientes'");
		$bd->execQuery("update $tabla set $field = '#BDC581', orden = 10 where item = 'obras'");
		$bd->execQuery("update $tabla set $field = '#44bd32' where item = 'tickets'");
		*/
		$bd->execQuery("update $tabla set $field = '#607d8b'");

		$bd->execQuery("update $tabla set $field = '#8395a7' where item in ('administrador', 'parametros') or item like 'par%metros'");
		$bd->execQuery("update $tabla set $field = '#079992' where item = 'root'");
		$bd->execQuery("update $tabla set $field = '#BDC581', orden = 1 where item = 'agenda'");
		$bd->execQuery("update $tabla set orden = 10 where item = 'obras'");
	}


	$tabla = "sc_querys";
	$query = "sc_querysall";
	$field = "cargar_siempre";
	$grupo = "cache";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, false);
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		sc3setGroup($tabla, $field, $grupo);

		sc3addFilter($query, "precargar", "t1.cargar_siempre = 1");

		$bd->execQuery("update $tabla set $field = 0");
		$bd->execQuery("update $tabla set $field = 1, es_cacheable = 1 where table_ in ('bp_monedas', 'gen_alicuotas_iva', 'bp_localidades')");
	}

	$bd->execQuery("update sc_querys
					set es_cacheable = 1 
					where table_ in ('sc_usuarios', 'sc_perfiles', 'sc_menuconsola')");

	$url = "sc-usuariosoperacion.php";
	sc3AgregarOperacion("Usuarios con acceso", $url, "images/gear.gif", "Permite listar usuarios que acceden", "sc_operaciones", "", 0, "administrador");

	$bd->close();
}

//Mayo 2020: horarios por usuario
function sc3UpdateVersionSc3ReglaHorarios()
{
	$bd = new BDObject();

	$tabla = "sc_perfiles_horarios";
	$query = "qscperfileshorarios";
	$grupo = "";
	if (!sc3existeTabla($tabla)) {
		echo ("<br>creando <b>$tabla</b>... ");
		$sql = "CREATE TABLE `$tabla` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					idperfil  INT(10) UNSIGNED NOT NULL,
					dia int(10) NOT NULL, 
					dia_semana varchar(5) NOT NULL,
					hr_inicio int(10) NOT NULL,
					hr_fin int(10) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 ;";

		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Horarios", "", "id", 1, 1, 1, "", 8, "images/watch.gif", true, "id");
		sc3generateFieldsInfo($tabla, false);
		sc3UpdateRequeridos($tabla);
		sc3AgregarQueryAPerfil($query, "administrador");

		$field = "idperfil";
		sc3addFk($tabla, $field, "sc_perfiles", "id", true);
		sc3addlink($query, $field, "qperfiles", true);
		sc3updateField($query, $field, "perfil", 1, "");
	}

	//Modificar esta operacion
	$url = "sc-operacionesusuario.php";
	sc3AgregarOperacion("Listar acceso", $url, "images/gear.gif", "Permite listar a que datos y operaciones tiene acceso", "sc_usuarios", "", 0, "administrador");

	$url = "sc-perfil-generarhorarios.php";
	sc3AgregarOperacion("Generar horarios", $url, "images/gear.gif", "Permite generar horarios de acceso", "sc_perfiles", "", 0, "administrador");

	$url = "sc-generarcodigo-op.php";
	sc3AgregarOperacion("Generar codigo", "sc-generarcodigo-op.php", "images/gear.gif", "Generar codigo", "sc_operaciones", "", 0, "root");


	$tabla = "bp_localidades";
	$query = "qlocalidades";
	$field = "atendida";
	if (sc3existeCampo($tabla, $field)) {
		$bd->execQuery("alter table $tabla drop column $field");
		sc3UpdateQueryFields($query, $tabla);

		$bd->execQuery("update $tabla set orden = 100 where orden is null");
		sc3addFk("sc_usuarios_perfiles", "idperfil",  "sc_perfiles", "id", true);
	}

	$url = "sc-exportar-fk.php";
	sc3AgregarOperacion("Exportar FKs", $url, "images/diagram.jpg", "Exporta Fk para luego generarlas", "", "root", 0, "root");

	$tabla = "sc_querys";
	$query = "sc_querysall";
	$field = "en_backup";

	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, false);
		sc3generateFieldsInfo($tabla);
		sc3setGroup($tabla, $field, "estado");

		$bd->execQuery("UPDATE `sc_querys` 
						set sc_querys.en_backup = 1
						WHERE sc_querys.queryname IN ('qagenda', 'qcja2cajas', 'qcja2movimientos', 'qcja2ordenespago', 
							'qcajas', 'qmovimientos', 'qcja2comprobantes', 'qcja2comprobantesrenglones', 
							'qcja2comprobantesiva', 'qgritrabajos', 'qgritrabajospagos', 'qgrifichas', 'qvenpuestos', 
							'qvenpedidos', 'qobrasactivas', 'qobrgastos', 'qmatmateriales', 'qstoarticulos', 
							'qctacuentas', 'qctamovimientos', 'qpropropiedades', 'qproocupacion', 'qfarfarmacias', 
							'qfarturnos', 'qecoconsorcios', 'qecopropietarios', 'qecopropietariosmatriz', 
							'qecoperiodos', 'qecofacturasproveedor', 'qlogbultos', 'qloghojasderuta', 
							'qlogtiposbultos', 'qlogunidades', 'qloghojasdetalles', 'qlogacuerdos', 'qsrvservicios', 
							'qsrvperiodos', 'qsrvproductos', 'qsrvtiposservicios', 'qkarcarreras', 'qkarcategorias', 
							'qkarcarrerasparticipantes', 'sc_usuarios')");

		sc3addFilter($query, "al backup", "t1.$field = 1");
	}

	$bd->close();
}

//enero 2021: utf, copiar perfiles....
function sc3UpdateVersionSc3Varios2021()
{
	$bd = new BDObject();

	$url = "sc-convertir-utf8.php";
	sc3AgregarOperacion("Convertir UTF8", $url, "images/sort.gif", "Convertir a UTF8", "", "root", 0, "root");

	$url = "sc-backupbd.php";
	sc3AgregarOperacion("Grabar backup", $url, "images/zip_icon.gif", "Grabar backup", "", "root", 0, "root");

	$url = "sc-copiarperfiles.php";
	sc3AgregarOperacion("Copiar perfiles", $url, "images/arrowbright.gif", "Copiar los perfiles de este usuario a otro", "sc_usuarios", "", 0, "administrador");

	$url = "sc-api-test.php";
	sc3AgregarOperacion("API Test", $url, "images/arrowdot.gif", "Testear API en cualquier servidor", "", "root", 0, "root");

	//porque el emailing busca repeditos por dia
	if (!$bd->existeIndex("sc_logs", "IND_log_emails")) {
		//era 10
		$bd->execQuery("alter table sc_logs 
							modify column codigo_operacion varchar(20) null");

		// era 60
		$bd->execQuery("alter table sc_logs 
							modify column objeto_operado varchar(100) null");

		$sql = "ALTER TABLE sc_logs 
					ADD INDEX `IND_log_emails` (`codigo_operacion`, `objeto_operado`, `descripcion`)";
		$bd->execQuery($sql);
	}

	$tabla = "sc_fields";
	$query = $tabla;
	$field = "ocultar_vacio";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, false, "visible", 0);
		sc3generateFieldsInfo($tabla);

		sc3setGroup($tabla, $field, "estado");
	}

	$tabla = "bp_localidades";
	$query = "qlocalidades";
	$field = "prefijo";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoStr($tabla, $field, false, "nombre", "", 10);
		sc3generateFieldsInfo($tabla);

		$bd->execQuery("update $tabla set prefijo = upper(left(nombre, 3))");
	}

	$url = "sc-updversion-core.php";
	sc3AgregarOperacion("Actualizar SC3 Core", $url, "images/sum.png", "Actualizar nucleo del sistema", "", "root", 0, "root");

	//TODO: armar FK con cascada !
	$bd->execQuery("delete from sc_usuarios_perfiles 
					where idperfil not in (select id from sc_perfiles)");

	//TODO: armar FK con cascada !
	$bd->execQuery("delete from sc_usuarios_perfiles 
					where idusuario not in (select id from sc_usuarios)");

	$url = "sc-limpiar-temporales.php";
	sc3AgregarOperacion("Limpiar temporales", $url, "images/delete.gif", "Limpia archivos temporales creados en tmpcache", "", "Root", 0, "Root");

	$tabla = "sc_operaciones";
	$query = "qoperaciones";
	$field = "pantalla_inicial";
	if (!sc3existeCampo($tabla, $field)) {
		sc3agregarCampoBoolean($tabla, $field, false, "emergente", 0);
		sc3generateFieldsInfo($tabla);
		sc3setGroup($tabla, $field, "estado");

		$bd->execQuery("update $tabla set $field = 0");
	}

	//Index por nombre de tabla
	$tabla = "sc_querys";
	$index = "IND_sc_querys_table_";
	if (!$bd->existeIndex($tabla, $index)) {
		echo ("<br>creando indice <b>$tabla $index </b>...");
		$bd->execQuery("ALTER TABLE $tabla ADD INDEX $index (table_)");
	}

	//Index por nombre de tabla
	$tabla = "sc_querys";
	$index = "IND_sc_querys_queryname";
	if (!$bd->existeIndex($tabla, $index)) {
		echo ("<br>creando indice <b>$tabla $index </b>...");
		$bd->execQuery("ALTER TABLE $tabla ADD INDEX $index (queryname)");
	}

	//Parametros por nombre
	$tabla = "sc_parametros";
	$index = "IND_sc_parametros_nombre";
	if (!$bd->existeIndex($tabla, $index)) {
		echo ("<br>creando indice <b>$tabla $index </b>...");
		$bd->execQuery("ALTER TABLE $tabla ADD INDEX $index (nombre)");
	}

	$url = "sc-instalarmodulo.php";
	//sc3AgregarOperacion("Instalar modulo", $url, "images/inmoobjetivos.gif", "Instalar o actualizar un modulo", "", "root", 0, "root"); 
	
	//operacion para administrar query (campos draggeables etc)
	$url = "sc-administrarquery.php";
	$opid = sc3AgregarOperacion("Definir consulta", $url, "images/toolsc3.png", "Administar tabla y campos", "sc_querys", "", 0, "root", "defquery", 0, "sc_querysall");
	if ($opid != 0) {

	}

	$url = "sc-adminer.php";
	$opid = sc3AgregarOperacion("Adminer", $url, "images/database.png", "Administar base de datos", "", "root", 0, "root", "adminer");

	$bd->close();
}
