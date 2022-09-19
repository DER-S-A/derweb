<?php
include_once "../../funcionesSConsola.php";

class UPDVersionWebAppUtils {    
    /**
     * addOperacion
     * Permite agregar una operación dentro del APP DERWEB
     * @param  string $xnombre Nombre de la operación
     * @param  string $xurl URL o función javascript que invoca.
     * @param  string $xicono Icono fontawersome
     * @param  int $xidTipoEntidad Tipo de entidad al que puede acceder a la operación.
     * @param  int $xorden Orden.
     * @return void
     */
    public static function addOperacion($xnombre, $xurl, $xicono, $xidTipoEntidad, $xorden = 0) {
        $sql = "SELECT
                    COUNT(*) AS cantReg
                FROM
                    lfw_operaciones op
                WHERE
                    op.nombre = '$xnombre'";
        $rs = getRs($sql);
        $cantReg = $rs->getValue("cantReg");
        $rs->close();

        $bd = new BDObject();
        $bd->beginT();
        try {
            if ($cantReg == 0) {
                echo "<br>Agregando operación $xnombre ...";
                $sql = "INSERT INTO lfw_operaciones (
                            nombre,
                            url,
                            icono,
                            orden)
                        VALUES (
                            '$xnombre',
                            '$xurl',
                            '$xicono',
                            '$xorden')";
                $id = $bd->execInsert($sql);

                $sql = "INSERT INTO lfw_accesos (
                            id_tipoentidad,
                            id_operacion)
                        VALUES (
                            $xidTipoEntidad,
                            $id)";
                $bd->execInsert($sql);

                $bd->commitT();

                echo "<br>Operación agregada con éxito.";
            }
        } catch (Exception $e) {
            $bd->rollbackT();
            echo "<br>" . $e->getMessage();
        } finally {
            $bd->close();
        }
    }
}
?>