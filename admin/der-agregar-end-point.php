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
require('modulos/generador-endpoint/generar-end-point.php');

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
    $modulo = Request("txt_modulo");
    $tabla = Request("txt_nombre_tabla");
    $objGenerador = new GeneradorEndPoint($modulo, $tabla);
    $objGenerador->generarEndPoint();

    goOn(); // En caso de no necesitarlo sacarlo.
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

			$input_txt_modulo = new HtmlInputText("txt_modulo", '');
			$req->add('txt_modulo', 'Módulo');
			$input_txt_modulo->setDefault("");
			$cpo_txt_modulo = new HtmlEtiquetaValor('Módulo', $input_txt_modulo->toHtml());
			$input_txt_nombre_tabla = new HtmlInputText("txt_nombre_tabla", '');
			$req->add('txt_nombre_tabla', 'Nombre de tabla');
			$input_txt_nombre_tabla->setDefault("");
			$cpo_txt_nombre_tabla = new HtmlEtiquetaValor('Nombre de tabla', $input_txt_nombre_tabla->toHtml());
			$tab->agregarSolapa("Datos", '', $cpo_txt_modulo->toHtml() . $cpo_txt_nombre_tabla->toHtml() );

        ?>   

        <form id="form1" action="der-agregar-end-point.php" method="post" id="form1" name="form1">
            <input type="hidden" id="opid" name="opid" value="<?php echo $opid; ?>"/>
            
            <!-- TODO: Desarrollar el HTML con el diseño del formulario 
                Se recomienda utilizar las clases de W3.CSS para armar el layout.
            -->
            <?php echo $tab->toHtml(); ?>

            <div class="div-botones">
                <?php echo $btnOkCancel->toHtml(); ?>
            </div>
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

            function submitForm() {
                if (validar())  {
                    pleaseWait2();
                    //TODO: Desarrollar el envío de formularios y validaciones de datos
                    // en caso de requerirlo.
                    document.getElementById('form1').submit();
                }
            }
        </script>
    </body>
</html>
