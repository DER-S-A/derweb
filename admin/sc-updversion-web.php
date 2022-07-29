<?php 


function sc3UpdateVersionWeb1()
{
	$tabla = "web_secciones";
	$query = "qwebsecciones";
	if (!sc3existeTabla($tabla))
	{
		echo("<br>creando tabla $tabla...");
		
		$sql = "CREATE TABLE `$tabla` (
				  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  `idseccion_padre` INTEGER UNSIGNED,
				  `titulo` varchar(80) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `uq_web_secciones`(`titulo`),
				  CONSTRAINT `FK_web_secciones_padre` FOREIGN KEY `FK_web_secciones_padre` (`idseccion_padre`)
				    REFERENCES `web_secciones` (`id`)
				    ON DELETE cascade
				    ON UPDATE cascade
				)
				ENGINE = InnoDB;";
				
		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Secciones", "sitio", "titulo", 1, 1, 1, "idseccion_padre, titulo", 7, "images/websecciones.png", 0);
		sc3AgregarQueryAPerfil($query, "sitio");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		
		$field = "idseccion_padre";
		sc3updateField($query, $field, "seccion padre", 0);
		sc3addlink($query, $field, $query);
	}
	
	$tabla = "web_secciones";
	$query = "qwebsecciones";
	if (sc3existeTabla($tabla))
	{
		$field = "foto";
		$grupo = "";
		if (!sc3existeCampo($tabla, $field))
		{
			sc3agregarCampoStr($tabla, $field, 0, "", "", 200);
			sc3generateFieldsInfo($tabla);
			sc3UpdateQueryProperty($query, "fields_", "'idseccion_padre, titulo, foto'");
			sc3updateField($query, $field, "foto", 0, "", 1);
		}
	}
	
	$tabla = "web_secciones";
	$query = "qwebsecciones";
	if (sc3existeTabla($tabla))
	{
		$field = "idioma";
		$grupo = "";
		if (!sc3existeCampo($tabla, $field))
		{
			sc3agregarCampoStr($tabla, $field, 0, "titulo", "", 6);
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "idioma", 0, "sp");
		}
	}
	
	$tabla = "web_novedades";
	$query = "qwebnovedades";
	if (sc3existeTabla($tabla))
	{
		$field = "idseccion";
		$grupo = "";
		if (!sc3existeCampo($tabla, $field))
		{
			sc3agregarCampoInt($tabla, $field, false, "titulo");
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "seccion", 0, "", 0, $grupo);
			sc3addFk($tabla, $field, "web_secciones");
			sc3addlink($query, $field, "qwebsecciones");
			sc3UpdateRequeridos($tabla);
			sc3UpdateQueryProperty($query, "fields_", "'idseccion, titulo, fecha, foto'");
		}
		
		sc3UpdateQueryProperty($query, "querydescription", "'Artículos'");
		
		sc3AgregarQueryAPerfil($query, "sitio");
		
		$field = "activo";
		$grupo = "";
		if (!sc3existeCampo($tabla, $field))
		{
			sc3agregarCampoBoolean($tabla, $field, false, "idseccion", 1);
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "activo", 1, "1");
		}
		
		$field = "idioma";
		$grupo = "";
		if (!sc3existeCampo($tabla, $field))
		{
			sc3agregarCampoStr($tabla, $field, 0, "titulo", "", 6);
			sc3generateFieldsInfo($tabla);
			sc3updateField($query, $field, "idioma", 0, "sp");
			sc3UpdateQueryProperty($query, "fields_", "'idseccion, titulo, $field, fecha, foto'");
		}
		sc3UpdateQueryProperty($query, "fields_", "'idseccion, titulo, $field, fecha, foto'");
		
		sc3addFilter($query, "en español (sp)", "t1.idioma = '''' or t1.idioma = ''sp''");
		sc3addFilter($query, "en ingles (en)", "t1.idioma = ''en''");
	}
	
	$tabla = "web_novedades_fotos";
	$query = "qwebnovedadesfotos";
	if (!sc3existeTabla($tabla))
	{
		echo("<br>creando tabla $tabla...");
		
		$sql = "CREATE TABLE `$tabla` (
				  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  `idarticulo` INTEGER UNSIGNED NOT NULL,
				  `foto` varchar(200) NOT NULL,
				  `comentarios` varchar(80),
				  `orden` int not null default 5,
				  PRIMARY KEY (`id`),
				  CONSTRAINT `FK_web_fotos_articulo` FOREIGN KEY `FK_web_fotos_articulo` (`idarticulo`)
				    REFERENCES `web_novedades` (`id`)
				    ON DELETE cascade
				    ON UPDATE cascade
				)
				ENGINE = InnoDB;";
				
		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Fotos", "", "foto", 1, 1, 1, "idarticulo, orden", 7, "images/webfotos.png");
		sc3AgregarQueryAPerfil($query, "sitio");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		
		$field = "idarticulo";
		sc3updateField($query, $field, "artículo", 0);
		sc3addlink($query, $field, "qwebnovedades");
		
		$field = "orden";
		sc3updateField($query, $field, "orden", 1, "10");

		$field = "foto";
		sc3updateField($query, $field, "foto", 1, "", 1);
	}
	
	$tabla = "web_rotulos";
	$query = "qwebrotulos";
	if (!sc3existeTabla($tabla))
	{
		echo("<br>creando tabla $tabla...");
		
		$sql = "CREATE TABLE `$tabla` (
				  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  `rotulo` varchar(40) NOT NULL,
				  `idioma` varchar(6) not null,
				  `traduccion` varchar(2000) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `uq_web_rotulos`(rotulo, idioma)
				)
				ENGINE = InnoDB;";
				
		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Rótulos", "sitio", "rotulo", 1, 1, 1, "rotulo, idioma", 3, "images/webrotulos.png", 0);
		sc3AgregarQueryAPerfil($query, "sitio");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		
		sc3addFilter($query, "en español (sp)", "t1.idioma = '''' or t1.idioma = ''sp''");
		sc3addFilter($query, "en ingles (en)", "t1.idioma = ''en''");
		
		$field = "idioma";
		sc3updateField($query, $field, "idioma", 1, "sp");
	}
}


//17-ene-2011: fotos para armar galeria en la web
function sc3UpdateVersionWebFotos()
{
	$tabla = "web_fotos";
	$query = "qwebfotos";
	if (!sc3existeTabla($tabla))
	{
		echo("<br>creando tabla $tabla...");
		
		$sql = "CREATE TABLE `$tabla` (
				  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
				  `foto` varchar(120) NOT NULL,
				  `titulo` varchar(120) NULL,
				  `url` varchar(120) NULL,
				  orden int not null,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `uq_web_fotos`(`foto`)
				)
				ENGINE = InnoDB;";
				
		$bd = new BDObject();
		$bd->execQuery($sql);

		sc3agregarQuery($query, $tabla, "Fotos", "sitio", "titulo", 1, 1, 1, "orden", 7, "images/camera.png", 0);
		sc3AgregarQueryAPerfil($query, "sitio");
		sc3generateFieldsInfo($tabla);
		sc3UpdateRequeridos($tabla);
		
		$field = "foto";
		sc3updateField($query, $field, "foto", 1, "", 1);
		
		$field = "orden";
		sc3updateField($query, $field, "orden", 0, "5");
	}
}

?>