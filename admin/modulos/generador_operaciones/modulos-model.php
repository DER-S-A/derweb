<?php
/**
 * ModulosModel
 * Contiene métodos estáticos para levantar información de las tablas
 * en donde se definen los módulos.
 * 
 * Fecha: 20/11/2021
 */
class ModulosModel {
    
    public static function getGrupos($xphp_file_name) {
        $sql = "SELECT
                    t1.grupo
                FROM
                    sc_mod_forms_inputs t1
                        INNER JOIN sc_modulos t2 ON t1.id_modulo = t2.id
                WHERE
                    t2.php_file_name = '$xphp_file_name'
                GROUP BY
                    t1.grupo
                ORDER BY
                    t1.grupo";
        return getRs($sql);
    }

    public static function getInputsForms($xphp_file_name, $xgrupo) {
        $sql = "SELECT
                    t3.nombre AS tipo_campo,
                    t2.* 
                FROM
                    sc_modulos t1
                        INNER JOIN sc_mod_forms_inputs t2 ON t2.id_modulo = t1.id
                        INNER JOIN sc_tipos_campos t3 ON t3.id = t2.id_tipo_campo
                WHERE
                    t1.php_file_name = '$xphp_file_name' AND
                    t2.grupo = '$xgrupo'
                ORDER BY
                    t2.grupo, t2.orden ASC";
        return getRs($sql);
    }

    public static function getSelectorQueryName($xid_query) {
        $sql = "SELECT
                    queryname
                FROM
                    sc_querys
                WHERE
                    id = $xid_query";
        return getRs($sql);
    }

    public static function getModuloPorId($xid) {
        $sql = "SELECT 
                    sc_modulos.*,
                    sc_querys.table_ AS 'tabla',
                    sc_querys.queryname,
                    sc_perfiles.nombre AS 'perfil',
                    sc_menuconsola.Item AS 'menu'
                FROM 
                    sc_modulos 
                        LEFT OUTER JOIN sc_querys ON sc_querys.id = sc_modulos.id_query
                        INNER JOIN sc_perfiles ON sc_perfiles.id = sc_modulos.id_perfil
                        LEFT OUTER JOIN sc_menuconsola ON sc_menuconsola.idItemMenu = sc_modulos.idItemMenu
                WHERE 
                sc_modulos.id = $xid";
        return getRs($sql);
    }
}

?>