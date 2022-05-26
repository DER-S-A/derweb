<?php

/**
 * Esta clase permite manejar la tabla de articulos
 * 
 */
class ArticulosModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla articulos.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM articulos ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
    
    /**
     * upgrade
     * Permite actualizar los artículos.
     * @param  string $registro JSON con el registro del artículo a actualizar.
     * @return array Resultado
     */
    public function upgrade($registro) {
        $aRegistro = json_decode($registro, true);
        $aResult = array();
        $bd = new BDObject();
        try {
            $rubro_cod = $aRegistro["U_ONESL_RubroCod"];
            $subrubro_cod = $aRegistro["U_ONESL_SubRubroCod"];
            $marca_cod = $aRegistro["U_ONESL_MarcaCod"];
            $codigo = $aRegistro["ItemCode"];
            $codigo_original = "";
            $descripcion = $aRegistro["ItemName"];
            $alicuota_iva = 21;
            $existencia_stock = 0.00;
            $stock_minimo = 0.00;

            $sql = "CALL sp_articulos_upgrade (
                    xrubroCod,
                    xsubrubroCod,
                    xmarcaCod,
                    xcodigo,
                    xcodOriginal,
                    xdescripcion,
                    xalicuotaIVA,
                    xexistencia,
                    xstockMinimo)";
            $this->setParameter($sql, "xrubroCod", $rubro_cod);
            $this->setParameter($sql, "xsubrubroCod", $subrubro_cod);
            $this->setParameter($sql, "xmarcaCod", $marca_cod);
            $this->setParameter($sql, "xcodigo", $codigo);
            $this->setParameter($sql, "xcodOriginal", $codigo_original);
            $this->setParameter($sql, "xdescripcion", $descripcion);
            $this->setParameter($sql, "xalicuotaIVA", $alicuota_iva);
            $this->setParameter($sql, "xexistencia", $existencia_stock);
            $this->setParameter($sql, "xstockMinimo", $stock_minimo);
            $bd->execQuery($sql);

            $aResult["result_code"] = "OK";
            $aResult["result_mensaje"] = "Articulo actualizado satisfactoriamente";
        } catch (Exception $ex) {
            $aResult["result_code"] = "BD_ERROR";
            $aResult["result_mensaje"] = $ex->getMessage();
        }

        return $aResult;
    }
}

?>