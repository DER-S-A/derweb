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
        $this->getWhere($sql, $filter);
        return $this->ejecutar_comando($sql);
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
                    clave 
                FROM 
                    entidades 
                WHERE 
                    usuario = '$xusuario' AND
                    id_tipoentidad = 1";
        $aDatos = $this->ejecutar_comando($sql);
        
        if ($aDatos != null) {
            if (strcmp($aDatos[0]["clave"], $xclave) == 0) {
                $aResult["result"] = "OK";
                $aResult["usuario"] = $aDatos[0]["usuario"];
                $aResult["clave"] = $aDatos[0]["clave"];
                $aResult["id_cliente"] = $aDatos[0]["id"];
                $aResult["codigo"] = $aDatos[0]["cliente_cardcode"];
            } else {
                $aResult["result"] = "ERR_CLAVE";
                $aResult["Mensaje"] = "Contraseña inválida.";
            }
        } else {
            $aResult["result"] = "ERR_USUARIO";
            $aResult["Mensaje"] = "Usuario inválido.";
        }
        return $aResult;
    }
}
?>