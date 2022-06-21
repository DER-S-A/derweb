<?php

/**
 * Esta clase permite manejar la tabla de articulos
 * 
 */
class ArticulosModel extends Model {
    private $id_listaprecio;
    private $descuento_p1;
    private $descuento_p2;
    private $rentabilidad;  

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
     * @param  int $xJSONParam Parámetros de filtros
     * @param  int $xidSubrubro Id. de subrubro.
     * @return array
     */
    public function getByRubroAndSubrubro($xsesion, $xJSONParam, $xpagina) {
        $aResponse = [];
        $this->getDatosClientes($xsesion);
        // Recupero los parámetros recibidos con el filtro a aplicar
        $aParametros = json_decode($xJSONParam, true);
        $id_rubro = intval($aParametros["values"]["id_rubro"]);
        $id_subrubro = intval($aParametros["values"]["id_subrubro"]);

        $sql = "CALL sp_articulos_getByRubroAndRubro($this->id_listaprecio, $id_rubro, $id_subrubro, $xpagina)";
        $rsArticulos = getRs($sql, true);
        $aResponse = $this->loadResponseArray($rsArticulos, 
                $xpagina, 
                $this->descuento_p1, 
                $this->descuento_p2, 
                $this->rentabilidad);
        $rsArticulos->close();
        
        return $aResponse;
    }
    
    /**
     * getDatosClientes
     * Levanta los datos de descuento y rentabilidad de los clientes.
     * @param  string $xsesion JSON con la sesi´pon iniciada en DERWEB.
     * @return void
     */
    private function getDatosClientes($xsesion) {
        $objEndidadesModel = new EntidadesModel();
        $aCliente = $objEndidadesModel->getBySesion($xsesion);
        $this->id_listaprecio = intval($aCliente[0]["id_listaprecio"]);
        $this->descuento_p1 = doubleval($aCliente[1]["descuento_1"]);
        $this->descuento_p2 = doubleval($aCliente[2]["descuento_2"]);
        $this->rentabilidad = doubleval($aCliente[3]["rentabilidad_1"]);        
    }
        
    /**
     * loadResponseArray
     * Permite recuperar los datos del conjunto de resultados en el arrya que va a devolver
     * la API.
     * @param  BDObject $rsArticulos
     * @return array
     */
    private function loadResponseArray(&$rsArticulos, $xpagina, $xdescuento_p1, $xdescuento_p2, $xrentabilidad) {
        $aArticulosResponse = array();
        $aResponse["cantreg"] = $rsArticulos->affectedRows;
        $aResponse["pagina"] = $xpagina;
        $aResponse["next"] = $xpagina + 40;
        $index = 0;
        while (!$rsArticulos->EOF()) {
            $aArticulosResponse[$index]["id"] = $rsArticulos->getValueInt('id');
            $aArticulosResponse[$index]["codigo"] = $rsArticulos->getValue('codigo');
            $aArticulosResponse[$index]["desc"] = $rsArticulos->getValue('descripcion');
            $aArticulosResponse[$index]["prlista"] = doubleval($rsArticulos->getValue('precio_lista'));
            $aArticulosResponse[$index]["cped"] = calcular_costo("PED", doubleval($rsArticulos->getValue('precio_lista')), $xdescuento_p1, $xdescuento_p2);
            $aArticulosResponse[$index]["cpre"] = calcular_costo("PRE", doubleval($rsArticulos->getValue('precio_lista')), $xdescuento_p1, $xdescuento_p2);
            $aArticulosResponse[$index]["vped"] = calcular_precio_venta(doubleval($aArticulosResponse[$index]["cped"]), $xrentabilidad);
            $aArticulosResponse[$index]["vpre"] = calcular_precio_venta(doubleval($aArticulosResponse[$index]["cpre"]), $xrentabilidad);
            $aArticulosResponse[$index]["stkd"] = doubleval($rsArticulos->getValue("existencia"));
    
            $aArticulosResponse[$index]["imgs"] = $this->getImagenesByArt($rsArticulos->getValueInt('id_articulo'));
            $aArticulosResponse[$index]["co"] = $this->getCodigosOriginales($rsArticulos->getValueInt('id_articulo'));
            $aArticulosResponse[$index]["apl"] = []; // Por ahora dejo en un array vacío, se completará en la segunda etapa.

            $index++;
            $rsArticulos->next();
        }
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
        $rsArticulos = getRs($sql, true);
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
    
    /**
     * getArticuloByFrase
     * Permite recuperar los artículos buscando por una frase.
     * @param  string $xfrase
     * @param  int $xpagina
     * @return void
     */
    public function getByFrase($xsesion, $xfrase, $xpagina) {
        if (strlen($xfrase) <= 4) {
            $aResponse["error"] = "VALID_ERROR";
            $aResponse["message"] = "Debe ingresar al menos 5 caracteres para buscar";
            return $aResponse;
        }

        $this->getDatosClientes($xsesion);
        $this->formatearValorBuscado($xfrase);
        $palabras = explode(' ', $xfrase);
        $filtro = "";
        for ($i = 0; $i < sizeof($palabras); $i++) {
            $this->generarCriterios($filtro, $i);
            $filtro .= $palabras[$i] . "%'";
        }

        $sql = "SELECT
                    art.id,
                    art.descripcion,
                    art.codigo,
                    pre.precio_lista,
                    art.existencia_stock
                FROM
                    articulos art
                        INNER JOIN articulos_precios pre ON pre.id_articulo = art.id
                        INNER JOIN marcas ON marcas.id = art.id_marca
                        INNER JOIN rubros rub ON rub.id = art.id_rubro
                        INNER JOIN subrubros srb ON srb.id = art.id_subrubro
                WHERE
                    art.eliminado = 0 AND
                    art.habilitado = 1 AND
                    $filtro AND
                    pre.id_listaprecio = 1
                ORDER BY 
                    id ASC
                LIMIT 40 OFFSET $xpagina";

        $rsArticulos = getRs($sql, true);
        $aResponse = $this->loadResponseArray($rsArticulos, $xpagina, $this->descuento_p1, $this->descuento_p2, $this->rentabilidad);
        $rsArticulos->close();
        return $aResponse;
    }
    
    /**
     * formatearValorBuscado
     * Formatea los valores a filtrar
     * @param  string $valorBuscado
     * @return void
     */
    private function formatearValorBuscado(&$valorBuscado) {
        $valorBuscado = str_replace(" ", "%", strtoupper($valorBuscado));
    }
    
    /**
     * generarCriterios
     * Genera los criterios para buscar por frase.
     * @param  string $filtro
     * @param  int $i
     * @return void
     */
    private function generarCriterios(&$filtro, $i) {
        if ($i == 0) {
            $filtro = "CONCAT(art.codigo, ' ', " .
                    "srb.descripcion, ' ', " .
                    "IFNULL(art.informacion_general, ''), ' ', " .
                    "marcas.descripcion, ' ', " .
                    "rub.descripcion, ' ', " .
                    "art.descripcion, ' ') LIKE '%";
        } else {
            $filtro .= "AND CONCAT(art.codigo, ' ', " .
                    "srb.descripcion, ' ', " .
                    "IFNULL(art.informacion_general, ''), ' ', " .
                    "marcas.descripcion, ' ', " .
                    "rub.descripcion, ' ', " .
                    "art.descripcion, ' ') LIKE '%";
        }
    }  
}

?>