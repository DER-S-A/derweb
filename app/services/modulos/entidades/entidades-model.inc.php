<?php

/**
 * EntidadesModel
 * Clase de acceso a datos para la tabla entidades.
 */
class EntidadesModel extends Model {
        
    /**
     * get
     * Obtiene cualquier tipo de consulta sobre la tabla entidades
     * @param  string $filter Filtro a aplicar en la consulta
     * @return array
     */
    public function get($filter) {
        $sql = "SELECT * FROM entidades ";
        $this->setWhere($sql, $filter);
        return $this->getQuery($sql);
    }
    
    /**
     * verificarUsuarioYClaveCliente
     * Permite verificar usuario y contraseña de un cliente.
     * @param  string $xusuario Usuario
     * @param  string $xclave Clave
     * @return array Array con los datos de la validación
     */
    public function verificarUsuarioYClaveCliente($xusuario, $xclave) {
        $aResult = array();
        $sql = "SELECT 
                    id, 
                    cliente_cardcode, 
                    usuario, 
                    clave,
                    id_tipoentidad
                FROM 
                    entidades 
                WHERE 
                    usuario = '$xusuario'";
        $aDatos = $this->getQuery($sql);
        
        if ($aDatos != null) {
            if (strcmp($aDatos[0]["clave"], $xclave) == 0) {
                $aResult["result"] = "OK";
                $aResult["usuario"] = $aDatos[0]["usuario"];
                $aResult["clave"] = $aDatos[0]["clave"];
                $aResult["id_cliente"] = intval($aDatos[0]["id"]);
                $aResult["codigo"] = $aDatos[0]["cliente_cardcode"];
                $aResult["id_tipoentidad"] = intval($aDatos[0]["id_tipoentidad"]);
            } else {
                $aResult["result"] = "ERR_CLAVE";
                $aResult["mensaje"] = "Contraseña inválida.";
            }
        } else {
            $aResult["result"] = "ERR_USUARIO";
            $aResult["mensaje"] = "Usuario inválido.";
        }
        return $aResult;
    }
}
?>