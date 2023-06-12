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
            $btnOkCancel = new HtmlBotonOkCancel(true, false);
            $query = "qavpmovimientos";

            $objModelRendiciones = new RendicionesAdminModel();
            $rsRecibos = $objModelRendiciones->getMovimientosByRendicion($mid);
			$gridAvisosPagos = new HtmlGrid($rsRecibos);
            $gridAvisosPagos->addOperacion("Revisar", "fa-pencil-square-o", "javascript:revisar_aviso(:PARAM);");
			$tab->agregarSolapa("Recibos", '', $gridAvisosPagos->toHtml());

            $rsRecibos->close();
        ?>   

        <form id="form1" action="der-avp-controlar-recibos.php" method="post" id="form1" name="form1">
            <input type="hidden" id="opid" name="opid" value="<?php echo $opid; ?>"/>
            <input type="hidden" id="id_movimiento" name="id_movimiento" />
            
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


        <div id="edicion_aviso" class="w3-modal">
            <div class="w3-modal-content w3-card-4">
                <header class="w3-container w3-teal"> 
                    <span onclick="document.getElementById('edicion_aviso').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                    <h2>Revisión de recibo</h2>
                </header>
            <div class="w3-container w3-padding-small">
                <div class="w3-row">
                    <div class="w3-col m12">
                        <?php
                        $inputFecha = new HtmlInputText("txt_fecha", "");
                        $inputFecha->setReadOnly();
                        $cpoFecha = new HtmlEtiquetaValor("Fecha:", $inputFecha->toHtml());
                        echo $cpoFecha->toHtml();
                        ?>
                    </div>
                </div>

                <div class="w3-row">
                    <div class="w3-col m6">
                        <?php
                            $input_cliente = new HtmlInputText("txt_cliente", "");
                            $input_cliente->setReadOnly();
                            $input_cliente->setSize(45);
                            $cpoCliente = new HtmlEtiquetaValor("Cliente:", $input_cliente->toHtml());
                            echo $cpoCliente->toHtml();
                        ?>
                    </div>
                    <div class="w3-col m6">
                        <?php
                            $input_sucursal = new HtmlInputText("txt_sucursal", "");
                            $input_sucursal->setReadOnly();
                            $input_sucursal->setSize(45);
                            $cpoSucursal = new HtmlEtiquetaValor("Sucursal:", $input_sucursal->toHtml());
                            echo $cpoSucursal->toHtml();
                        ?>
                    </div>
                </div>

                <div class="w3-row">
                    <div class="w3-col m12">
                        <?php
                            $inputNumeroRecibo = new HtmlInputText("txt_numero_recibo", "");
                            $inputNumeroRecibo->setReadOnly();
                            $cpoNumeroRecibo = new HtmlEtiquetaValor("N° Recibo", $inputNumeroRecibo->toHtml());
                            echo $cpoNumeroRecibo->toHtml();
                        ?>
                    </div>
                </div>

                <div class="w3-row">
                    <div class="w3-col m3">
                        <?php
                            $inputImporteEfectivo = new HtmlInputText("txt_importe_efectivo", "0");
                            $inputImporteEfectivo->setTypeFloat();
                            $cpoImporteEfectivo = new HtmlEtiquetaValor("Imp. Efvo.:", $inputImporteEfectivo->toHtml());
                            echo $cpoImporteEfectivo->toHtml();
                        ?>
                    </div>

                    <div class="w3-col m3">
                        <?php
                            $inputCheques = new HtmlInputText("txt_importe_cheques", "0");
                            $inputCheques->setTypeFloat();
                            $cpoCheques = new HtmlEtiquetaValor("Imp. Cheques:", $inputCheques->toHtml());
                            echo $cpoCheques->toHtml();
                        ?>
                    </div>

                    <div class="w3-col m3">
                        <?php
                            $inputDeposito = new HtmlInputText("txt_importe_deposito", "0");
                            $inputDeposito->setTypeFloat();
                            $cpoDepositos = new HtmlEtiquetaValor("Imp. Depósito:", $inputDeposito->toHtml());
                            echo $cpoDepositos->toHtml();
                        ?>
                    </div>

                    <div class="w3-col m3">
                        <?php
                            $inputRetenciones = new HtmlInputText("txt_importe_retenciones", "0");
                            $inputRetenciones->setTypeFloat();
                            $cpoRetenciones = new HtmlEtiquetaValor("Retenciones:", $inputRetenciones->toHtml());
                            echo $cpoRetenciones->toHtml();
                        ?>
                    </div>

                    <div class="w3-col m3">
                        <?php
                            $inputTotal = new HtmlInputText("txt_total", "0");
                            $inputTotal->setTypeFloat();
                            $inputTotal->setReadOnly();
                            $cpoTotal = new HtmlEtiquetaValor("Total:", $inputTotal->toHtml());
                            echo $cpoTotal->toHtml();
                        ?>
                    </div>
                </div>

                <div class="w3-row">
                    <div class="w3-col m12">
                        <?php
                            $inputRevisado = new HtmlCheckBox("chk_revisado", "0");
                            $cpoRevisado = new HtmlEtiquetaValor("Revisado: ", $inputRevisado->toHtml());
                            echo $cpoRevisado->toHtml();
                        ?>
                    </div>
                </div>
            </div>
                <footer class="w3-container w3-teal">
                    <div class="w3-row">
                        <div class="w3-col m12 w3-center w3-padding-small">
                            <?php
                                $btnGrabar = new HtmlBotonSmall("javascript:grabar_aviso()", "fa-floppy-o", "Graba las modificaciones del aviso");
                                $btnCancelar = new HtmlBotonSmall("javascript:cancelar_edicion()", "fa-undo", "Cancela la edición");

                                echo $btnGrabar->toHtml();
                                echo $btnCancelar->toHtml();
                            ?>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="<?php echo sc3CacheButer("app/administracion/avisos-pagos/revisar-avisos.js");?>" type="text/javascript"></script>
    </body>
</html>
