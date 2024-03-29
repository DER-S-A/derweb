<?php
/**
 * Este script contiene todas las dependencias de clases que se
 * requieren para que el backend funcione.
 */

 // Incorporo las dependencias de admin para manejar la base de datos
 // de tal forma de tener unificada las clases en un solo lugar.
include_once("../../admin/config.php");
include_once("../../admin/funcionesSConsola.php");
include_once("../../admin/dbcommand.php");
include_once("../../admin/terceros/fpdf/fpdf.php");
include_once("includes/funciones.inc.php");

include("includes/apis/configuracion-sap.php");
include("includes/apis/api-controller.php");
include("includes/model.inc.php");

// Incluyo la API para establecer la comunicación con SAP.
include("includes/apis/api-sap.php");

// Referencia al endpoint rubros
include("modulos/articulos/rubros-model.inc.php");
include("modulos/articulos/rubros-controller.php");

// Referencia al end point entidades.
include("modulos/paises/paisesModel.inc.php");
include("modulos/paises/paisesController.php");
include("modulos/provincias/provinciasModel.inc.php");
include("modulos/provincias/provinciasController.php");
include("modulos/entidades/entidades-model.inc.php");
include("modulos/entidades/entidades-controller.php");
include("modulos/articulos/marcas-model.inc.php");
include("modulos/articulos/marcas-controller.php");
include("modulos/articulos/subrubros-model.inc.php");
include("modulos/articulos/subrubros-controller.php");
include("modulos/clientes-potenciales/clientes-potenciales-model.inc.php");
include("modulos/clientes-potenciales/clientes-potenciales-controller.php");
include("modulos/banner_portada/banner-portada-model.inc.php");
include("modulos/banner_portada/banner-portada-controller.php");
include("modulos/derweb_operaciones/lfw-operacionesModel.inc.php");
include("modulos/derweb_operaciones/lfw-operacionesController.php");
include("modulos/entidades/tipos-entidadesModel.inc.php");
include("modulos/entidades/tipos-entidadesController.php");
include("modulos/articulos/articulosModel.inc.php");
include("modulos/articulos/articulosController.php");
include("modulos/articulos/articulos-destacadosModel.inc.php");
include("modulos/articulos/articulos-destacadosController.php");
include("modulos/pedidos/pedidosModel.inc.php");
include("modulos/pedidos/pedidosController.php");
include("modulos/forma-envio/formas-enviosModel.inc.php");
include("modulos/forma-envio/formas-enviosController.php");
include("modulos/transportes/transportesModel.inc.php");
include("modulos/transportes/transportesController.php");
include("modulos/entidades/sucursalesModel.inc.php");
include("modulos/entidades/sucursalesController.php");
include("modulos/entidades/margenesEspeciales-model.inc.php");
include("modulos/entidades/margenesEspeciales-controller.php");
include("modulos/rendiciones/avp-rendicionesModel.inc.php");
include("modulos/rendiciones/avp-rendicionesController.php");
include("modulos/rendiciones/avp-rendiciones-pdf.php");
include("modulos/novedades/novedadesModel.inc.php");
include("modulos/novedades/novedadesController.php");
?>