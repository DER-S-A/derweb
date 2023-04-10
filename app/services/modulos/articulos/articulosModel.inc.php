<?php

/**
 * Esta clase permite manejar la tabla de articulos
 * 
 */
class ArticulosModel extends Model {
    protected $id_listaprecio;
    protected $descuento_p1;
    protected $descuento_p2;
    protected $rentabilidad;  

    /**
     * get
     * Devuelve los registros de la tabla articulos.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xsesion, $xfilter, $xpagina) {
        $aResponse = [];
        $this->getClienteActual($xsesion);

        // Armado de la sentencia SQL.
        $sql = "    SELECT
                art.id,
                art.descripcion,
                art.codigo,
                pre.precio_lista,
                art.existencia_stock,
                art.alicuota_iva
            FROM
                articulos art
                    INNER JOIN articulos_precios pre ON pre.id_articulo = art.id ";

        $this->setWhere($sql, $xfilter);
        //return $this->getQuery($sql);
        $rsArticulo = getRs($sql, true);
        $aResponse = $this->loadResponseArray($rsArticulo, $xpagina, $this->descuento_p1, $this->descuento_p2, $this->rentabilidad);
        return $aResponse;
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
            $rubro_cod = $aRegistro["RubroCod"];
            $subrubro_cod = $aRegistro["SubRubroCod"];
            $marca_cod = $aRegistro["MarcaCod"];
            $codigo = $aRegistro["ItemCode"];
            $codigo_original = "";
            $descripcion = $aRegistro["ItemName"];
            $alicuota_iva = $aRegistro["IvaRate"];
            $existencia_stock = 0.00;
            $stock_minimo = 0.00;
            $habilitado = $aRegistro["Habilitado"];

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
                    xstockMinimo,
                    xhabilitado
                    )";
                $this->setParameter($sql, "xrubroCod", $rubro_cod);
                $this->setParameter($sql, "xsubrubroCod", $subrubro_cod);
                $this->setParameter($sql, "xmarcaCod", $marca_cod);
                $this->setParameter($sql, "xcodigo", $codigo);
                $this->setParameter($sql, "xcodOriginal", $codigo_original);
                $this->setParameter($sql, "xdescripcion", $descripcion);
                $this->setParameter($sql, "xalicuotaIVA", $alicuota_iva);
                $this->setParameter($sql, "xexistencia", $existencia_stock);
                $this->setParameter($sql, "xstockMinimo", $stock_minimo);
                $this->setParameter($sql, "xhabilitado", $habilitado);
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

        /** Recupero los datos de sesión */
        $this->getClienteActual($xsesion);

        // Recupero los parámetros recibidos con el filtro a aplicar
        $aParametros = json_decode($xJSONParam, true);
        $id_rubro = intval($aParametros["values"]["id_rubro"]);
        $id_subrubro = intval($aParametros["values"]["id_subrubro"]);

        $sql = "CALL sp_articulos_getByRubroAndRubro($this->id_listaprecio, $id_rubro, $id_subrubro, $xpagina)";
        $rsArticulos = getRs($sql, true);
        $arrayRenta = $this->generarRentabilidadGral($this->idSucursal);
        $aResponse = $this->loadResponseArray($rsArticulos, 
                $xpagina, 
                $this->descuento_p1, 
                $this->descuento_p2,
                $arrayRenta); 
        $rsArticulos->close();
        return $aResponse;
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
            $renta = $this->generarRentabilidadEspecial($this->idSucursal, $rsArticulos->getValueInt('id'));
            //$renta = [];
            //$renta[0]['rentabilidad_1'] = 100; 
            //$renta[0]['rentabilidad_2'] = 100; 
            //$renta = $this->generarRentabilidadEspecial(30455, 786061);
            if(!empty($renta)) {
                $xrentabilidad = $renta;
            }

            $aArticulosResponse[$index]["id"] = $rsArticulos->getValueInt('id');
            $aArticulosResponse[$index]["codigo"] = $rsArticulos->getValue('codigo');
            $aArticulosResponse[$index]["desc"] = utf8_encode($rsArticulos->getValue('descripcion'));
            $aArticulosResponse[$index]["iva"] = $rsArticulos->getValueFloat('alicuota_iva');
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
        if (strlen($xfrase) <= 3) {
            $aResponse["error"] = "VALID_ERROR";
            $aResponse["message"] = "Debe ingresar al menos 5 caracteres para buscar";
            return $aResponse;
            
        }
        
        $this->getClienteActual($xsesion);
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
                    art.existencia_stock,
                    art.alicuota_iva
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
        $arrayRenta = $this->generarRentabilidadGral($this->idSucursal);
        
        $aResponse = $this->loadResponseArray($rsArticulos, $xpagina, $this->descuento_p1, $this->descuento_p2, $arrayRenta);
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
    public function generarFichaArt($xid_articulo,$xid_cliente) {
    
        $sql = "SELECT 
                articulos.id AS ID_Articulo,
                articulos.descripcion AS Descripcion,
                articulos.existencia_stock AS Stock,
                articulos.codigo AS Codigo,
                articulos.informacion_general AS Informacion_general,
                articulos.datos_tecnicos AS Datos_tecnicos,
                articulos.diametro AS Diametro,
                art_unidades_ventas.unidad_venta AS Unidades_de_venta,
                marcas.linklogo AS Logo
                FROM articulos
                INNER JOIN articulos_precios ON articulos_precios.id_articulo = articulos.id
                INNER JOIN marcas ON marcas.id = articulos.id_marca
                CROSS JOIN art_unidades_ventas ON art_unidades_ventas.id_articulo = articulos.id
                WHERE articulos.id = $xid_articulo"
            ;

        $sql_codigoOriginales = "SELECT codigo FROM art_codigos_originales  
                WHERE id_articulo = $xid_articulo"
            ;

        $sql_equivalencias = $this->consultaGenerarEquivalencias($xid_articulo, $xid_cliente);
        $sql_art_fotos = $this->consultaGenerarFotosArticulos($xid_articulo);


        $response = [];
        $response ["informacion"]= getRs($sql, true)->getAsArray();
        $response ['informacion'][0]['Descripcion'] = utf8_encode($response ['informacion'][0]['Descripcion']);
        $response ["codigos_originales"]= getRs($sql_codigoOriginales, true)->getAsArray();
        $response ["equivalencias"] = getRs($sql_equivalencias, true)->getAsArray();
        $response["fotos"] = getRs($sql_art_fotos, true)->getAsArray();
        return $response;
    }

    public function consultaGenerarEquivalencias($xid_articulo, $xid_cliente) {
        $sql = "select linklogo AS Logo, articulos.codigo AS Codigo, articulos_precios.precio_lista AS Precio_lista,
        round(precio_lista * (100-(SELECT descuento_1 FROM entidades WHERE id = $xid_cliente) )/100,2) AS Precio_costo,
        round(precio_lista * (100+(SELECT rentabilidad_1 FROM entidades WHERE id = $xid_cliente) )/100,2) AS Precio_venta
        from articulos 
        INNER JOIN marcas ON marcas.id = articulos.id_marca
        INNER JOIN articulos_precios ON articulos_precios.id_articulo = articulos.id
        where equivalencia = (select equivalencia from articulos where id=$xid_articulo) and articulos.id != $xid_articulo";

        return $sql;
    }

    public function consultaGenerarFotosArticulos($xid_articulo) {
        $sql = "SELECT archivo FROM art_fotos WHERE id_articulo = $xid_articulo";
        return $sql;
    }

    function consultaFiltrar_marcasRubrosSubrubros($stringFiltro) {
        $sql = "SELECT id_marca, id_rubro, id_subrubro FROM articulos 
        WHERE $stringFiltro. GROUP BY id_marca, id_rubro, id_subrubro";
        $response = getRs($sql)->getAsArray();
        return $response;

    }

    /**
     * generarRentabilidadGral
     * Genera el array con las rentabilidad_1 y rentabilidad_2.
     * @param  int $id_sucursal
     * @return array
     */

    private function generarRentabilidadGral($id_sucursal) {

        $sql = "SELECT rentabilidad_1, rentabilidad_2 FROM sucursales WHERE id = $id_sucursal";
        $arrayRenta = getRs($sql, true)->getAsArray();
        return $arrayRenta;
    }

    /**
     * generarRentabilidadEspecial
     * Genera el array con las rentabilidad_1 y rentabilidad_2, si el array q devuelve esta vacio entonces no sera tomado 
     * en cuenta, y el articulo colocara el margen general.
     * @param  int $id_sucursal
     * @param  int $id_articulo
     * @return array
     */

    private function generarRentabilidadEspecial($id_sucursal, $id_articulo) {
        $sql = "SELECT * FROM margenes_especiales WHERE id_sucursal = $id_sucursal";
        $aResult = getRs($sql, true)->getAsArray();
        $aRentaEsp = [];
        
        if(empty($aResult)) {
            return $aRentaEsp;
        }
   
        $articulo = $this->generarMarcaRubroSubrubro($id_articulo);

        
        foreach($aResult as $row) {
            $coincidencia = true;
            if(!empty($row['id_rubro'])) {
                if($row['id_rubro'] != $articulo[0]['id_rubro']) {
                    $coincidencia = false;
                }
            }
            if(!empty($row['id_subrubro'])) {
                if($row['id_subrubro'] != $articulo[0]['id_subrubro']) {
                    $coincidencia = false;
                }
            }
            if(!empty($row['id_marca'])) {
                if($row['id_marca'] != $articulo[0]['id_marca']) {
                    $coincidencia = false;
                }
            }
            if($coincidencia) {
                $aRentaEsp[0]['rentabilidad_1'] = $row['rentabilidad_1'];
                $aRentaEsp[0]['rentabilidad_2'] = $row['rentabilidad_2'];
            }
        }
        return $aRentaEsp;
    }

    /**
     * generarMarcaRubroSubrubro
     * Genera el array con los id de marcas, rubros y subrubros que usaremos para comparar con los datos de la 
     * tabla margenes especiales.
     * @param  int $articulo
     * @return array
     */
    private function generarMarcaRubroSubrubro($articulo) {
        $sql = "SELECT id_rubro, id_subrubro, id_marca FROM articulos WHERE id = $articulo";
        $result = getRs($sql, true)->getAsArray();
        return $result;
    }

}

?>