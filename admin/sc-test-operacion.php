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
require('modulos/generador_operaciones/modulos-model.php');

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
    ///////////////////////////////////////////////
    // TODO: Desarrollar el procesamiento del formulario enviado

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

			$input_sel_tipocampo = new HtmlSelector("sel_tipocampo", "qsctiposcampos", 0);
			$input_sel_tipocampo->setRequerido();
			$req->add('sel_tipocampo', 'Tipos Campos');
			$input_sel_tipocampo->setSizeSmall();
			$cpo_sel_tipocampo = new HtmlEtiquetaValor('Tipos Campos', $input_sel_tipocampo->toHtml());
			$input_sel_tablas = new HtmlSelector("sel_tablas", "sc_querysall", 0);
			$input_sel_tablas->setRequerido();
			$req->add('sel_tablas', 'Tablas');
			$input_sel_tablas->setSizeSmall();
			$cpo_sel_tablas = new HtmlEtiquetaValor('Tablas', $input_sel_tablas->toHtml());
			$tab->agregarSolapa("Datos", '', $cpo_sel_tipocampo->toHtml() . $cpo_sel_tablas->toHtml() );
			$input_opcion_sample_1 = new HtmlBoolean2("opcion_sample_1", 1);
			$input_opcion_sample_1->setRequerido();
			$req->add('opcion_sample_1', 'Opcion 1');
			$cpo_opcion_sample_1 = new HtmlEtiquetaValor('Opcion 1', $input_opcion_sample_1->toHtml());
			$input_opcion_sample_2 = new HtmlBoolean2("opcion_sample_2", 0);
			$input_opcion_sample_2->setRequerido();
			$req->add('opcion_sample_2', 'Opcion 2');
			$cpo_opcion_sample_2 = new HtmlEtiquetaValor('Opcion 2', $input_opcion_sample_2->toHtml());
			$tab->agregarSolapa("HtmlBoolean2 / HtmlBoolean", '', $cpo_opcion_sample_1->toHtml() . $cpo_opcion_sample_2->toHtml() );
			$input_txt_test = new HtmlInputText("txt_test", '');
			$input_txt_test->setRequerido();
			$req->add('txt_test', 'Prueba');
			$input_txt_test->setDefault("");
			$cpo_txt_test = new HtmlEtiquetaValor('Prueba', $input_txt_test->toHtml());
			$input_txt_test_numerico = new HtmlInputText("txt_test_numerico", '');
			$input_txt_test_numerico->setRequerido();
			$req->add('txt_test_numerico', 'Campo numÃ©rico');
			$input_txt_test_numerico->setTypeFloat(2);
			$input_txt_test_numerico->setDefault("0");
			$cpo_txt_test_numerico = new HtmlEtiquetaValor('Campo numÃ©rico', $input_txt_test_numerico->toHtml());
			$tab->agregarSolapa("HtmlInputText Sample", '', $cpo_txt_test->toHtml() . $cpo_txt_test_numerico->toHtml() );
			$input_input_file_sample = new HtmlInputFile2("input_file_sample", '');
			$cpo_input_file_sample = new HtmlEtiquetaValor('Archivo', $input_input_file_sample->toHtml());
			$input_input_file_2_sample = new HtmlInputFile("input_file_2_sample", '');
			$cpo_input_file_2_sample = new HtmlEtiquetaValor('Archivo 2', $input_input_file_2_sample->toHtml());
			$tab->agregarSolapa("Input File", '', $cpo_input_file_sample->toHtml() . $cpo_input_file_2_sample->toHtml() );
			$input_rango_fecha_sample = new HtmlDateRange("rango_fecha_sample");
			$cpo_rango_fecha_sample = new HtmlEtiquetaValor('Rango Fecha', $input_rango_fecha_sample->toHtml());
			$tab->agregarSolapa("Rangos fecha", '', $cpo_rango_fecha_sample->toHtml() );

        ?>   

        <form id="form1" action="nombre_script" method="post" id="form1" name="form1">
            <input type="hidden" id="enviar" name="enviar" />
            
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
