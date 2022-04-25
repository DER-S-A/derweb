<?php
/**
 * Este script permite intalar el módulo de generador de operaciones.
 */

/**
 * instalarGeneradorDeOperaciones
 * Instala el generador de módulos / operaciones.
 * @return void
 */
function instalarGeneradorDeOperaciones() {
    instalarTablaModulos();
    instalarTablaModuloGUIInputs();
    instalarOperacionGenerarCodigo();
    agregarCamposASCModulos();
}

/**
 * instalarTablaModulos
 * Instalo la tabla módulos.
 * @return void
 */
function instalarTablaModulos() {
    $tabla = "sc_modulos";
    $query = getQueryName($tabla);

    if (!sc3ExisteTabla($tabla)) {
        // Nombre del módulo / operación
        // php_file_name: Nombre del archivo PHP a generar.
        $sql = "CREATE TABLE $tabla (
                    id int not null unique auto_increment,
                    nombre varchar(60) not null,
                    php_file_name varchar(60) not null,
                    PRIMARY KEY (id))";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();

        sc3agregarQuery($query, $tabla, "Módulos", "Desarrollador", "", 1, 1, 1, "nombre", 3, "images/table.png");
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, "id", "Módulo");
        sc3updateField($query, "nombre", "Nombre de operación", 1);
        sc3updateField($query, "php_file_name", "Nombre script a generar", 1);
        sc3AgregarQueryAPerfil($query, "Root");
    }
}

/**
 * agregarCamposASCModulos
 * Agrego los campos para definir los parámetros de la operación.
 * @return void
 */
function agregarCamposASCModulos() {
    $tabla = "sc_modulos";
    $query = getQueryName($tabla);

    $field = "idItemMenu";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field int NULL";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3addFk($tabla, $field, "sc_menuconsola", $field);
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Menú", 0, "", 0, "Parámetros operación");
        sc3addLink($query, $field, "sb_menuconsola", 0);
    }

    $field = "grupal";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field tinyint(3) NOT NULL DEFAULT 0";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Grupal", 1, "", 0, "Parámetros operación");
    }

    $field = "id_perfil";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field int(10) UNSIGNED NULL";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3addFk($tabla, $field, "sc_perfiles");
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Perfil", 1, "", 0, "Parámetros operación");
        sc3addlink($query, $field, "qperfiles", 0);
    }

    $field = "target";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field varchar(100)";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Target", 0, "", 0, "Parámetros operación");
    }

    $field = "emergente";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field tinyint(3) NOT NULL DEFAULT 0";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Emergente", 1, 1, 0, "Parámetros operación");
    }

    $field = "ayuda";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field varchar(255) NULL";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Ayuda", 1, "", 0, "Parámetros operación");
    }

    $field = "id_query";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field int NULL";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3AddFk($tabla, $field, "sc_querys");
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Query", 0, "", 0, "Parámetros operación");
        sc3addLink($query, $field, "sc_querysall", 0);
    }

    $field = "icono";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field varchar(100) NULL";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $field, "Icono", 0, "", 0, "Parámetros operación");
    }
}

/**
 * instalarTablaModuloGUIInputs
 * Instala la tabla que define los controles del formulario correspondiente
 * al módulo que se está desarrollando.
 * @return void
 */
function instalarTablaModuloGUIInputs() {
    $tabla = "sc_mod_forms_inputs";
    $query = getQueryName($tabla);
    $instalar_modulo_ejemplo = false;
    if (!sc3ExisteTabla($tabla)) {
        $sql = "CREATE TABLE $tabla (
                    id int not null unique auto_increment,
                    id_modulo int not null,
                    id_query int null,
                    id_tipo_campo int unsigned not null,
                    input_id varchar(100) not null,
                    etiqueta varchar(100) not null,
                    grupo varchar(100) null,
                    cant_decimales int null default 2,
                    selector_size_small tinyint(3) not null default 0,
                    requerido tinyint(3) not null default 0,
                    orden int not null,
                    PRIMARY KEY (id))";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        
        sc3addFk($tabla, "id_modulo", "sc_modulos");
        sc3addFk($tabla, "id_query", "sc_querys");
        sc3AddFk($tabla, "id_tipo_campo", "sc_tipos_campos");

        sc3agregarQuery($query, $tabla, "Inputs Form", "", "", 1, 1, 1, "", 12, "", 1);
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, "id", "Control Input");
        sc3updateField($query, "id_modulo", "Módulo", 1);
        sc3updateField($query, "id_tipo_campo", "Tipo campo", 1);
        sc3updateField($query, "input_id", "Id. HTML Control", 1);
        sc3updateField($query, "etiqueta", "Etiqueta", 1);
        sc3updateField($query, "grupo", "Grupo", 0);
        sc3updateField($query, "cant_decimales", "Cant. Decimales", 0, "", 0, "Propiedades HtmlInputText");
        sc3updateField($query, "id_query", "Query", 0, "", 0, "Propiedades HTMLSelector");
        sc3updateField($query, "selector_size_small", "Ajustar ancho buscador", 1, "0", 0, "Propiedades HTMLSelector");
        sc3updateField($query, "orden", "Orden", 1);

        sc3addLink($query, "id_modulo", getQueryName("sc_modulos"), 1);
        sc3addlink($query, "id_query", "sc_querysall");
        sc3addlink($query, "id_tipo_campo", "qsctiposcampos");

        sc3AgregarQueryAPerfil($query, "Root");
        $instalar_modulo_ejemplo = true;
    }

    // Agrego campo ReadOnly
    $field = "readonly";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD readonly tinyint(3) NOT NULL DEFAULT 0";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, "readonly", "Solo lectura", 1);
    }

    // Agrego campo para establecer el valor predeterminado del boolean
    $field = "htmlbool_default";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field tinyint(3)";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, "htmlbool_default", "Valor por defecto", 0, "0", 0, "Propiedades HTMLBoolean");
    }

    // Agrego campo para especificar si el inputText es numérico o no.
    $field = "valor_numerico";
    if (!sc3existeCampo($tabla, $field)) {
        $sql = "ALTER TABLE $tabla ADD $field tinyint(3)";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, "valor_numerico", "Es numérico", 1, "0", 0, "Propiedades HtmlInputText");
    }

    // Grid ABM
    sc3ConfigurarCamposAMostrar($query, "id, id_tipo_campo, input_id, etiqueta, grupo, requerido, orden");

    if ($instalar_modulo_ejemplo)
        instalarModuloTest();
}

/**
 * instalarOperacionGenerarCodigo
 * Instala la operación para generar el código de un módulo
 * @return void
 */
function instalarOperacionGenerarCodigo() {
    $tabla = "sc_modulos";
    $xid = sc3AgregarOperacion(
            "Generar código", 
            "sc-mod-generar-codigo.php", 
            "images/gear.gif", 
            "Permite generar el código de un módulo",
            $tabla, 
            "", 
            0, 
            "Root");
}

/**
 * instalarModuloTest
 * Instala el módulo de testing.
 * @return void
 */
function instalarModuloTest() {
    $bd = new BDObject();
    $sql = "DELETE FROM sc_tipos_campos";
    $bd->execQuery($sql);
    $sql = "ALTER TABLE sc_tipos_campos AUTO_INCREMENT = 11";
    $bd->execQuery($sql);
    $sql = "INSERT INTO `sc_tipos_campos` VALUES 
        (11,'HtmlBoolean'),
        (2,'HtmlBoolean2'),
        (9,'HtmlDate'),
        (8,'HtmlDate2'),
        (6,'HtmlDateRange'),
        (10,'HtmlGrid'),
        (3,'HtmlInputFile'),
        (4,'HtmlInputFile2'),
        (1,'HtmlInputText'),
        (7,'HtmlRichText'),
        (5,'HtmlSelector'),
        (12, 'HtmlCombo');";
    $bd->execQuery($sql);
    
    $sql = "INSERT INTO sc_modulos (nombre, php_file_name) VALUES ('Operación Test', 'sc-test-operacion.php');";
    $bd->execQuery($sql);
    $sql = "SELECT MAX(id) AS ultimoid FROM sc_modulos";
    $rs = getRs($sql);
    $ultimo_id = $rs->getValue("ultimoid");

    // Importo los inputs
    $sql = "INSERT INTO `sc_mod_forms_inputs` VALUES 
        (1,$ultimo_id,266,5,'sel_tipocampo','Tipos Campos','',NULL,1,1,1,0,0,0),
        (2,$ultimo_id,37,5,'sel_tablas','Tablas','',NULL,1,1,2,0,NULL,NULL),
        (3,$ultimo_id,NULL,1,'txt_test','Prueba','HtmlInputText Sample',NULL,0,1,3,0,0,0),
        (4,$ultimo_id,NULL,1,'txt_test_numerico','Campo numÃ©rico','HtmlInputText Sample',2,0,1,4,0,0,1),
        (5,$ultimo_id,NULL,2,'opcion_sample_1','Opcion 1','HtmlBoolean2 / HtmlBoolean',NULL,0,1,5,0,1,0),
        (6,$ultimo_id,NULL,6,'rango_fecha_sample','Rango Fecha','Rangos fecha',NULL,0,0,6,0,0,0),
        (7,$ultimo_id,NULL,4,'input_file_sample','Archivo','Input File',NULL,0,1,7,0,0,0),
        (8,$ultimo_id,NULL,3,'input_file_2_sample','Archivo 2','Input File',NULL,0,1,8,0,0,0),
        (9,$ultimo_id,NULL,2,'opcion_sample_2','Opcion 2','HtmlBoolean2 / HtmlBoolean',NULL,0,1,9,0,0,0);";
    $bd->execQuery($sql);
    $bd->close();

    // Agrego la operacion
    $idoperacion = sc3AgregarOperacion("Operación Test", "sc-test-operacion.php", "", "Testing del generador de operaciones", "", "Desarrollador", 0, "Root");
}