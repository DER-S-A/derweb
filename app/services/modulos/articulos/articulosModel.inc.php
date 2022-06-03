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
        $procesar = false;
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

            // Valido que los datos que se requieren estén cargados en el JSON.
            $procesar = true;
            if (esVacio($aRegistro["ItemCode"])) {
                $procesar = false;
                $aResult["result_code"] = "VALID_ERROR";
                $aResult["result_mensaje"] = "Campo código de artículo está vacío";                
            }

            if (esVacio($aRegistro["ItemName"])) {
                $procesar = false;
                $aResult["result_code"] = "VALID_ERROR";
                $aResult["result_mensaje"] = "Campo descripción de artículo está vacío";                    
            }

            if (esVacio($rubro_cod)) {
                $procesar = false;
                $aResult["result_code"] = "VALID_ERROR";
                $aResult["result_mensaje"] = "Campo código de rubro está vacío";                
            }

            if (esVacio($subrubro_cod)) {
                $procesar = false;
                $aResult["result_code"] = "VALID_ERROR";
                $aResult["result_mensaje"] = "Campo código de subrubro está vacío";                
            }

            if (esVacio($marca_cod)) {
                $procesar = false;
                $aResult["result_code"] = "VALID_ERROR";
                $aResult["result_mensaje"] = "Campo código de marca está vacío";                
            }

            // Si los datos están correctos, entonces, envío a procesar.
            if ($procesar) {
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
            }

        } catch (Exception $ex) {
            $aResult["result_code"] = "BD_ERROR";
            $aResult["result_mensaje"] = $ex->getMessage();
        }

        return $aResult;
    }
    
    /**
     * getByRubroAndSubrubro
     * Permite recuperar los artículos por rubros y subrubros
     * @param  string $xsesion JSON con la sesión iniciada en DERWEB.
     * @param  int $xidRubro Id. de rubro
     * @param  int $xidSubrubro Id. de subrubro.
     * @return array
     */
    public function getByRubroAndSubrubro($xsesion, $xidRubro, $xidSubrubro, $xpagina) {
        $objEndidadesModel = new EntidadesModel();
        $aArticulosResponse = [];
        $aResponse = [];
        $aCliente = $objEndidadesModel->getBySesion($xsesion);
        $id_listaprecio = intval($aCliente[0]["id_listaprecio"]);
        $descuento_p1 = doubleval($aCliente[1]["descuento_1"]);
        $descuento_p2 = doubleval($aCliente[2]["descuento_2"]);
        $rentabilidad = doubleval($aCliente[3]["rentabilidad_1"]);

        $sql = "CALL sp_articulos_getByRubroAndRubro($id_listaprecio, $xidRubro, $xidSubrubro, $xpagina)";
        $rsArticulos = getRs($sql, true);
        $aResponse["cantreg"] = $rsArticulos->affectedRows;
        $aResponse["pagina"] = $xpagina;
        $aResponse["next"] = $xpagina + 40;
        $index = 0;
        while (!$rsArticulos->EOF()) {
            $aArticulosResponse[$index]["id"] = $rsArticulos->getValueInt('id');
            $aArticulosResponse[$index]["codigo"] = $rsArticulos->getValue('codigo');
            $aArticulosResponse[$index]["desc"] = $rsArticulos->getValue('descripcion');
            $aArticulosResponse[$index]["prlista"] = doubleval($rsArticulos->getValue('precio_lista'));
            $aArticulosResponse[$index]["cped"] = calcular_costo("PED", doubleval($rsArticulos->getValue('precio_lista')), $descuento_p1, $descuento_p2);
            $aArticulosResponse[$index]["cpre"] = calcular_costo("PRE", doubleval($rsArticulos->getValue('precio_lista')), $descuento_p1, $descuento_p2);
            $aArticulosResponse[$index]["vped"] = calcular_precio_venta(doubleval($aArticulosResponse[$index]["cped"]), $rentabilidad);
            $aArticulosResponse[$index]["vpre"] = calcular_precio_venta(doubleval($aArticulosResponse[$index]["cpre"]), $rentabilidad);
            $aArticulosResponse[$index]["stkd"] = doubleval($rsArticulos->getValue("existencia")); 

            $aArticulosResponse[$index]["imgs"] = $this->getImagenesByArt($rsArticulos->getValueInt('id_articulo'));
            $aArticulosResponse[$index]["co"] = $this->getCodigosOriginales($rsArticulos->getValueInt('id_articulo'));
            $aArticulosResponse[$index]["apl"] = []; // Por ahora dejo en un array vacío, se completará en la segunda etapa.

            $index++;
            $rsArticulos->next();
        }

        $rsArticulos->close();
        $aResponse["values"] = $aArticulosResponse;
        return $aResponse;
    }
    
    /**
     * getImagenesByArt
     * Permite obtener el array con las imagenes del artículo.
     * @param  mixed $xidArticulo
     * @return void
     */
    public function getImagenesByArt($xidArticulo) {
        $sql = "SELECT 
                    archivo AS 'url',
                    predeterminada AS 'default'
                FROM
                    art_fotos
                WHERE
                    art_fotos.id_articulo = $xidArticulo";
        return getRs($sql)->getAsArray();
    }
    
    /**
     * getCodigosOriginales
     * Permite levantar los códigos originales.
     * @param  mixed $xidArticulo
     * @return void
     */
    public function getCodigosOriginales($xidArticulo) {
        $sql = "SELECT
                    codigo AS 'cod'
                FROM
                    art_codigos_originales
                WHERE
                    id_articulo = $xidArticulo";
        return getRs($sql)->getAsArray();
    }
}

?>