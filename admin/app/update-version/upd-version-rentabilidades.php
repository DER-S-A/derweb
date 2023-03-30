<?php

/**
 * Esta clase permite actualizar el módulo de rentabilidades.
 */

class UpdateVersionRentabilidades extends UpdateVersion
{
    /**
     * Actualiza el módulo de margenes especiales.
     */
    public static function actualizar() {
        self::instalarTablaRentabilidades();
    }

    /**
     * Instala la tabla de margenes especiales.
     */
    public static function instalarTablaRentabilidades()
    {
        $tabla = "margenes_especiales";
        $query = getQueryName($tabla);
        $tabla_suc = "sucursales";
        $querySuc = "sucursales";
        $tabla_marca = "marcas";
        $queryMarca = "marcas";
        $tabla_rubro = "rubros";
        $queryRub = "rubros";
        $tabla_subrubro = "subrubros";
        $querySubrubro = "subrubros";

        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE margenes_especiales (
                id int NOT NULL AUTO_INCREMENT UNIQUE,
                id_rubro int,
                id_subrubro int,
                id_marca int,
                id_sucursal  int NOT NULL,
                rentabilidad_1 decimal(5,2),
                rentabilidad_2 decimal(5,2),
                habilitado tinyint(3),
                PRIMARY KEY (id))";

            self::ejecutarSQL($sql);
            sc3addFk($tabla, "id_rubro", $tabla_rubro);
            sc3addFk($tabla, "id_subrubro", $tabla_subrubro);
            sc3addFk($tabla, "id_marca", $tabla_marca);
            sc3addFk($tabla, "id_sucursal", $tabla_suc);

            sc3agregarQuery($query, $tabla, "Margenes especiales", "", "", 0, 0, 0, "", 9, "", 1);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, "id", "ID");
            sc3UpdateField($query, "id_rubro", "Rubro", 0);
            sc3UpdateField($query, "id_subrubro", "Sub rubro", 0);
            sc3UpdateField($query, "id_marca", "Marca", 0);
            sc3UpdateField($query, "id_sucursal", "Sucursal", 0);
            sc3UpdateField($query, "rentabilidad_1", "Rentabilidad 1", 0);
            sc3UpdateField($query, "rentabilidad_2", "Rentabilidad 2", 0);
            sc3UpdateField($query, "habilitado", "Habilitado", 0);

            sc3addlink($query, "id_rubro", $queryRub);
            sc3addlink($query, "id_subrubro", $querySubrubro);
            sc3addlink($query, "id_marca", $queryMarca);
            sc3addlink($query, "id_sucursal", $querySuc);
            sc3AgregarQueryAPerfil($query, "root");
        }
    }
}
