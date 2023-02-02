<?php
/**
 * Clase: UpdateVersionOfertas
 *  Permite instalar y/o actualizar el módulo de ofertas.
 */

class UpdateVersionOfertas extends UpdateVersion {
    public static function actualizar() {
        self::instalarABMOfertas();
    }

    public static function instalarABMOfertas() {
        $tablaOferta = "ofertas";
        $queryOferta = getQueryName($tablaOferta);

        if (!sc3existeTabla($tablaOferta)) {
            // Tabla principal de ofertas.
            $sql = "CREATE TABLE $tablaOferta (
                        id int NOT NULL UNIQUE AUTO_INCREMENT,
                        descripcion varchar(100) not null unique,
                        fecha datetime not null default current_timestamp,
                        habilitado tinyint(3) not null default 1,
                        publicado tinyint(3) not null default 0,
                        imagen varchar(255) not null,
                        PRIMARY KEY (id))";
            self::ejecutarSQL($sql);
            sc3agregarQuery($queryOferta, $tablaOferta, "Ofertas", "Panel", "descripcion", 1, 1, 1, "id", 4);
            sc3generateFieldsInfo($tablaOferta);
            sc3updateField($queryOferta, "id", "Oferta N°");
            sc3updateField($queryOferta, "descripcion", "Descripción", 1);
            sc3updateField($queryOferta, "fecha", "Fecha", 1);
            sc3updateField($queryOferta, "habilitado", "Habilitado", 1, "1");
            sc3updateField($queryOferta, "imagen", "Imágen Carrusel", 1, "", 1, "Imágen para carrusel");
            sc3AgregarQueryAPerfil($queryOferta, "Administrador");
        }

        $tablaOfertasDetalles = "ofertas_detalles";
        $queryOfertasDetalles = getQueryName($tablaOfertasDetalles);
        if (!sc3existeTabla($tablaOfertasDetalles)) {
            // Detalles de ofertas.
            $sql = "CREATE TABLE $tablaOfertasDetalles (
                        id bigint not null unique auto_increment,
                        id_oferta int not null,
                        id_articulo int null,
                        id_marca int null,
                        habilitado tinyint(3) not null default 1,
                        PRIMARY KEY (id))";
            self::ejecutarSQL($sql);
            sc3addFk($tablaOfertasDetalles, "id_oferta", $tablaOferta);
            sc3addFk($tablaOfertasDetalles, "id_articulo", "articulos");
            sc3addFk($tablaOfertasDetalles, "id_marca", "marcas");

            sc3agregarQuery($queryOfertasDetalles, $tablaOfertasDetalles, "Marcas en oferta", "", "id", 1, 1, 1, "id", 5, "", 1);
            sc3generateFieldsInfo($tablaOfertasDetalles);
            sc3updateField($queryOfertasDetalles, "id", "Renglón N°");
            sc3updateField($queryOfertasDetalles, "id_oferta", "Oferta N°", 1);
            sc3updateField($queryOfertasDetalles, "id_articulo", "Artículo", 0);
            sc3updateField($queryOfertasDetalles, "id_marca", "Marca", 0);
            sc3updateField($queryOfertasDetalles, "habilitado", "Habilitado", 1, "1");
            sc3AgregarQueryAPerfil($queryOfertasDetalles, "Administrador");
            
            sc3addlink($queryOfertasDetalles, "id_oferta", $queryOferta, 1);
            sc3addLink($queryOfertasDetalles, "id_articulo", "articulos");
            sc3addlink($queryOfertasDetalles, "id_marca", "marcas");
        }
    }
}
?>