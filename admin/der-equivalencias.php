<?php
/* 
 * Template SC3 Core - Generador de código para desarrollo de operaciones.
 * 
 * Recomendación: Sacar los comentarios TODO y reemplazarlo por comentarios que explien
 * la funcionalidad desarrollada.
 * 
 * Fecha: 19/11/2021
*/
require('funcionesSConsola.php');
require('app/utils/model.inc.php');
require('app/articulos/equivalencias-model.php');

// TODO: Agregar los requires o includes que se requieran para esta
// operación.

checkUsuarioLogueado();

//analiza si hay anterior porque si no lo hay no usa la cache
//esto es porque al invocar al goOn() no se manda el parametro, por lo que viene de presionar el [ Aceptar ]
$ant = Request("anterior");
$useCache = true;
if (sonIguales($ant, ""))
{
    $useCache = false;
}

$stackname = Request("stackname");
$anterior = RequestInt("anterior");
//nuevos para los reportes
$mid = RequestInt("mid");
$opid = RequestInt("opid");
$mensaje = getMensaje();
$warning = getWarning();
$loc = "";

$error = "";

$idoperacion = RequestIntMaster("idoperacion", "qoperaciones");
$mquery = Request("mquery");

if (enviado()) {
    $tipo_operacion = Request("tipo_operacion");

    // Asigno la equivalencia
    if (sonIguales($tipo_operacion, "ASIGNAR")) {
        $idArticuloEquivalente = RequestInt("articulo");
        $objEquivalenciaModel = new EquivalenciasModel();
        $objEquivalenciaModel->asignarEquivalencia($mid, $idArticuloEquivalente);
    } else {
        // Paso por acá en caso de eliminar una equivalencia.
        $idArticuloAEliminar = RequestInt("id_articulo_equiv");
        $objEquivalenciaModel = new EquivalenciasModel();
        $objEquivalenciaModel->eliminarEquivalencia($idArticuloAEliminar);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF8">
        <?php 
            include_once('include-head.php');
            include_once('include-head-app.php');
        ?>
    </head>

    <body>
        <header class="w3-container headerTitulo">
            <section class="w3-row">
                <article class="w3-col m11">
                    <h4><?php echo(getOpTitle(Request("opid"))); ?></h4>
                </article>
                <article class="w3-col m1">
                    <?php echo(linkCerrar(0)); ?>
                </article>
            </section>
        </header>

        <script type="text/javascript">
            <?php 
                $req = new FormValidator();
            ?>
        </script>
        <?php 
            $tab = new HtmlTabs2();
            $btnOkCancel = new HtmlBotonOkCancel();

			$input_articulo_seleccionado = new HtmlSelector("articulo_seleccionado", "articulos", $mid);
			$input_articulo_seleccionado->setReadOnly(true);
			$req->add('articulo_seleccionado', 'Artículo seleccionado');
			$cpo_articulo_seleccionado = new HtmlEtiquetaValor('Artículo seleccionado', $input_articulo_seleccionado->toHtml());

            $input_articulo = new HtmlSelector("articulo", "articulos", 0);
            $input_articulo->setRequerido();
            $req->add('articulo', "Artículo");
            $cpo_articulo = new HtmlEtiquetaValor("Artículo", $input_articulo->toHtml());

            // Recupero los artículos equivalentes.
            $sql = "SELECT equivalencia FROM articulos WHERE id = $mid";
            $rsArticulo = getRs($sql, true);
            $equivalencia = $rsArticulo->getValueInt("equivalencia");
            $rsArticulo->close();

            $sql = "SELECT 
                        articulos.id AS 'N° Interno', 
                        articulos.codigo AS 'Código', 
                        articulos.descripcion AS 'Descripción'
                    FROM 
                        articulos 
                    WHERE 
                        articulos.equivalencia = $equivalencia AND
                        articulos.id <> $mid AND
                        articulos.eliminado = 0";

            $rsEquivalencias = getRs($sql);
            $grillaEquivs = new HtmlGrid($rsEquivalencias);
            $grillaEquivs->addOperacion("Eliminar", "ico/delete.ico", "javascript:eliminar_equivalencia(:PARAM);");

            $tab->agregarSolapa("Datos", '', 
                $cpo_articulo_seleccionado->toHtml() 
                . $cpo_articulo->toHtml()
                . $btnOkCancel->toHtml()
                . $grillaEquivs->toHtml());
        ?>   

        <form id="form1" action="der-equivalencias.php" method="post" id="form1" name="form1">
            <input type="hidden" id="opid" name="opid" value="<?php echo $opid; ?>"/>
            <input type="hidden" id="mid" name="mid" value="<?php echo $mid; ?>"/>
            <input type="hidden" id="tipo_operacion" name="tipo_operacion"/>
            <input type="hidden" id="id_articulo_equiv" name="id_articulo_equiv"/>
            
            <!-- TODO: Desarrollar el HTML con el diseño del formulario 
                Se recomienda utilizar las clases de W3.CSS para armar el layout.
            -->
            <?php echo $tab->toHtml(); ?>

        </form>

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Registrar las funciones que se van a invocar con ajax en
        // caso de ser necesario
        // Ejemplo:
        //      $ajaxH = sc3GetAjaxHelper();
        //      $ajaxH->registerFunction("buscarProductoPorCodigo", "app/gestion/ventas/ventas_funciones.php");
        //      sc3SaveAjaxHelper($ajaxH);
        ?>

        <!-- 
            Código Javascript para darle funcionalidad a la interfaz de usuario.
        -->
        <script type="text/javascript">
            <?php echo $req->toScript(); ?>

            document.getElementById("articulo").focus();

            /**
             * Asigna una equivalencia.
             */
            function submitForm() {
                if (validar())  {
                    pleaseWait2();
                    document.getElementById("tipo_operacion").value = "ASIGNAR";
                    document.getElementById('form1').submit();
                }
            }

            /**
             * Elimina una equivalencia.
             */
            function eliminar_equivalencia(xidArticulo) {
                pleaseWait2();
                document.getElementById("tipo_operacion").value = "ELIMINAR";
                document.getElementById("id_articulo_equiv").value = xidArticulo;
                document.getElementById('form1').submit();
            }
        </script>
    </body>
</html>
