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
require('terceros/fpdf/fpdf.php');
require('app/utils/model.inc.php');
require('app/administracion/avisos-pagos/avp-rendiciones-pdf.php');
require('app/administracion/avisos-pagos/rendiciones-admin-model.php');

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

$objModelRendicion = new RendicionesAdminModel();

if (enviado()) {
    $linkPDF = $objModelRendicion->confirmarRevision($mid);
    setMensaje("El PDF ha sido generado, <a href='" . $linkPDF . "' class='w3-button w3-primary' target='_blank'>[haga click aquí para abrirlo]</a>");
    goOn(); // En caso de no necesitarlo sacarlo.
} else {
    // Verifico al entrar a la operacion que todos los movimientos hayan
    // sido revisados
    $revisado_ok = $objModelRendicion->validar_revision($mid);
    if (!$revisado_ok) {
        setMensaje("Los movimientos no han sido revisados, por favor ingrese a la operación controlar recibos y verifique los mismos");
        goOn();
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
            $contentHtml = "";
            $tab = new HtmlTabs2();
            $btnOkCancel = new HtmlBotonOkCancel();

            $input_nro_rendicion = new HtmlInputText("txt_nro_rendicion", $mid);
            $input_nro_rendicion->setTypeFloat(0);
            $input_nro_rendicion->setReadOnly();
            $cpoNroRendicion = new HtmlEtiquetaValor("Rendición N°:", $input_nro_rendicion->toHtml());

            $rsRendicion = $objModelRendicion->getRendicion($mid);
            $sel_vendedor = new HtmlSelector("sel_vendedor", "entidades", $rsRendicion->getValueInt("id_entidad"));
            $sel_vendedor->setReadOnly();
            $cpoSelVendedor = new HtmlEtiquetaValor("Vendedor:", $sel_vendedor->toHtml());

            $contentHtml = "<div class='w3-container'>"
                            . "<div class=\"w3-row\">"
                                    . "<div class='w3-col m6'>"
                                    . $cpoNroRendicion->toHtml()
                                    . "</div>"
                                    . "<div class='w3-col m6'>"
                                    . $cpoSelVendedor->toHtml()
                                . "</div>"
                            . "</div>"
                        . "</div>";
            
            $tab->agregarSolapa("Rendición", "", $contentHtml);
        ?>   

        <form id="form1" action="der-avp-generar-rendicion.php" method="post" id="form1" name="form1">
            <input type="hidden" id="opid" name="opid" value="<?php echo $opid; ?>"/>
            <input type="hidden" id="mid" name="mid" value="<?php echo $mid; ?>"/>
            
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
