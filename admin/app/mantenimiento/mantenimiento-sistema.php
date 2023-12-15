<?php

/**
 * MantenimientoSistema
 * Permite realizar el mantenimiento del sistema.
 * Fecha: 15/12/2023
 */
class MantenimientoSistema {
        
    /**
     * eliminarTemporales
     * Permite eliminar los archivos temporales que se van generando con el uso
     * del sistema
     * @return void
     */
    public function eliminarTemporales() {
        Sc3FileUtils::borrarArchivos("tmp/", true, 0);
        Sc3FileUtils::borrarArchivos("tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("scripts/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("css/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("app/administracion/avisos-pagos/tmpcache/", true, 0);

        Sc3FileUtils::borrarArchivos("../app/css/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/js/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/js/utils/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/aviso_pago/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/carrusel/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/grid-articulos/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/mi-carrito/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/mi-carrito/boton-mi-carrito/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/mi-carrito/grid-carrito/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/vendedores/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/confirmacion-pedido/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/ingreso-pedidos-rapidos/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/panel-opciones/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/ficha-articulo/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/catalogo/lista-articulos/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/form/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/menus/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/menus/mi_perfil/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/rendiciones/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/seguridad/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/utilities/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/buscador/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/carrusel_footer/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/centro_noticias/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/lista-articulos/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/pedidos/detalle_mis_pedidos/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/pedidos/mis_pedidos/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/rentabilidad/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/components/tabs/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/derweb-apis-library/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/terceros/lfw-controls-bs/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/terceros/lfw-datagrid/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/terceros/lfw-modal-bs/tmpcache/", true, 0);
        Sc3FileUtils::borrarArchivos("../app/terceros/sweet-alert/tmpcache/", true, 0);
    }
}
?>