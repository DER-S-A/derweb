<?php
/**
 * Clase: UpdateVersionArticulos
 * Descripción:
 *  Permite actualizar el módulo de artículos.
 */

class UpdateVersionArticulos extends UpdateVersion {    
    /**
     * actualziar
     * Ejecuta la actualización del módulo de artículos.
     * @return void
     */
    public static function actualziar() {
        self::agregarCampoAMarcas();
        self::instalarTablaUnidadesVentas();
        self::instalarOpEquivalencias();
    }
    
    /**
     * agregarCampoAMarcas
     * Agrega campos a la tabla marcas.
     * @return void
     */
    private static function agregarCampoAMarcas() {
        $tabla = "marcas";
        $query = "marcas";

        // Se agrega link para cargar el logo de la marca.
        $campo = "linkLogo";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo varchar(255) NULL";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, $campo, "Imagen Logo", 0, "", 1);
        }
    }
    
    /**
     * instalarTablaUnidadesVentas
     * Instala la tabla de unidades de ventas.
     * @return void
     */
    private static function instalarTablaUnidadesVentas() {
        $tabla = "art_unidades_ventas";
        $query = getQueryName($tabla);
        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                        id int not null unique auto_increment,
                        id_articulo int not null,
                        descripcion varchar(20) not null,
                        unidad_venta decimal(20, 2) not null,
                        PRIMARY KEY (id))";
            self::ejecutarSQL($sql);
            sc3AddFk($tabla, "id_articulo", "articulos");
            sc3agregarQuery($query, $tabla, "Unidades Ventas", "", "", 1, 1, 1, "", 4, "", 1);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, "id", "U.V. N°");
            sc3updateField($query, "descripcion", "Descripción", 1);
            sc3updateField($query, "unidad_venta", "Unidad Venta", 1, "1");
            sc3addlink($query, "id_articulo", "articulos", 1);
            sc3AgregarQueryAPerfil($query, "root");
        }
    }
    
    /**
     * instalarOpEquivalencias
     * Permite instalar la operación para gestionar las equivalencias en
     * el ABM de artículos.
     * @return void
     */
    private static function instalarOpEquivalencias() {
        $opid = sc3AgregarOperacion(
            "Equivalencias", 
            "der-equivalencias.php", 
            "ico/barragrisMapa.ico", 
            "Permite gestionar las equivalencias de los artículos.", 
            "articulos", 
            "", 
            0, 
            "Administrador", 
            "", 
            0, 
            "articulos");
    }    
}
?>