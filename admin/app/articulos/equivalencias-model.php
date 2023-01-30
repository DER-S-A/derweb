<?php

/**
 * EquivalenciasModel
 * Clase que permite manejar las equivalencias.
 */
class EquivalenciasModel extends Model {    
    /**
     * asignarEquivalencia
     * Permite asignar un artículo como equivalente.
     * @param  int $xidArticulo Id. de artículo seleccionado.
     * @param  int $xidArticuloEquivalente Id. de artículo equivalente.
     * @return void
     */
    public function asignarEquivalencia($xidArticulo, $xidArticuloEquivalente) {
        // Recupero el valor que tiene el campo equivalencia.
        $sql = "SELECT equivalencia FROM articulos WHERE id = $xidArticulo";
        $rsEquivalencia = getRs($sql);
        $equivalencia = $rsEquivalencia->getValueInt("equivalencia");
        $rsEquivalencia->close();

        $sql = "UPDATE
                    articulos
                SET
                    articulos.equivalencia = $equivalencia
                WHERE
                    articulos.id = $xidArticuloEquivalente";
        echo $sql;

        $objBD = new BDObject();
        $objBD->execQuery($sql);
        $objBD->close();        
    }
    
    /**
     * eliminarEquivalencia
     * Permite eliminar una equivalencia.
     * @param  int $xidArticulo Id. de artículo a eliminar de equivalencias.
     * @return void
     */
    public function eliminarEquivalencia($xidArticulo) {
        // Pasa sacar un artículo de la cadena de equivalencias le vuelvo
        // a asignar su propio id en negativo.
        $sql = "UPDATE
                    articulos
                SET
                    articulos.equivalencia = ($xidArticulo * -1)
                WHERE
                    articulos.id = $xidArticulo";
        $objBD = new BDObject();
        $objBD->execQuery($sql);
        $objBD->close();
    }
}
?>