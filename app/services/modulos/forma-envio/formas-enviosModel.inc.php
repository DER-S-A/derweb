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

    /*public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM formas_envios ";
        $this->setWhere($sql, $xfilter);
        $sql = $this->getQuery($sql);
        
        $sqlx = "SELECT id_formaenvio FROM sucursales WHERE id=2";
        $idFormaEnvio = $this->getQuery($sqlx);
        $sqlx = "SELECT * FROM formas_envios WHERE id=6";
        $predeterminado = $this->getQuery($sqlx);

        $resultado = [$predeterminado[0]];
        //$resultado[] = $sql[0];

        foreach ($sql as &$valor) {
            //$resultado[] = $valor;
            if($valor['id'] != $predeterminado[0]['id']) {
                $resultado[] = $valor;
            }
        }
        
        return $resultado;
        //return $predeterminado[0]['descripcion'];
        //return $sql[0]['descripcion'];
    }*/

    public function getFormasEnviosMiCarrito($xfilter, $xid) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM formas_envios ";
        $this->setWhere($sql, $xfilter);
        $sql = $this->getQuery($sql);
        
        $sqlx = "SELECT id_formaenvio FROM sucursales WHERE id=$xid";
        $idFormaEnvio = $this->getQuery($sqlx);
        $idFormaEnvio = $idFormaEnvio[0]['id_formaenvio'];
        $sqlx = "SELECT * FROM formas_envios WHERE id=$idFormaEnvio";
        $predeterminado = $this->getQuery($sqlx);

        $resultado = [$predeterminado[0]];
        //$resultado[] = $sql[0];

        foreach ($sql as &$valor) {
            //$resultado[] = $valor;
            if($valor['id'] != $predeterminado[0]['id']) {
                $resultado[] = $valor;
            }
        }
        
        return $resultado;
        //return $idFormaEnvio;
        //return $predeterminado[0]['descripcion'];
        //return $sql[0]['descripcion'];
    }
}

?>