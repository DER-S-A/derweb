<?php

/**
 * Esta clase permite manejar la tabla de formas_envios
 * 
 */
class Formas_enviosModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla formas_envios.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM formas_envios ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql); 
    }

    public function getFormasEnviosMiCarrito($xfilter, $xid) { ///$xid es la id de sucursal
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM formas_envios ";
        $this->setWhere($sql, $xfilter);
        $sql = $this->getQuery($sql);

        // Hago un cruce de tabla para saber que que forma de envio es la principal para esa sucursal.
        $sqlx = "SELECT formas_envios.* FROM sucursales
        INNER JOIN formas_envios ON sucursales.id_formaenvio = formas_envios.id
        WHERE sucursales.id = $xid";
        $predeterminado = $this->getQuery($sqlx);

        // Aca armo un json nuevo para poner como indice 0 la forma de envio principal.
        $resultado = [$predeterminado[0]];

        // Aca recorro las formas de envio para agregarlasa al json que ya tiene la forma de envio principal.
        // Asi q uso un if para cuando se repota la forma de envio no se cargue.
        foreach ($sql as &$valor) {
            if($valor['id'] != $predeterminado[0]['id']) {
                $resultado[] = $valor;
            }
        }
        
        return $resultado;
    }
}

?>