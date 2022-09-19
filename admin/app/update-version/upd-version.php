<?php

/**
 * UpdateVersion
 * Contiene métodos útiles para crear updversions.
 */
class UpdateVersion {    
    /**
     * ejecutarSQL
     * Permite ejecutar un comando SQL.
     * @param  string $sql
     * @return void
     */
    public static function ejecutarSQL($sql) {
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
    }

        
    /**
     * instalarOpPedidosPendientes
     * Instala la operación PEDIDOS PENDIENTES en DERWEB Vendedores
     * @return void
     */
    public static function instalarOpPedidosPendientes() {
        UPDVersionWebAppUtils::addOperacion("PEDIDOS PENDIENTES", "javascript:ver_pedidos_pendientes();", "fa-solid fa-cart-flatbed", 3, 1);
    }
}
?>