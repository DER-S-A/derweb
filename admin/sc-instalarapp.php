<?php
require("funcionesSConsola.php");
require("sc-updversion-utils.php");
include_once('sc-updversion-cel-menuyprecargas.php');
checkUsuarioLogueadoRoot();

if (enviado()) {

    $app = Request("app");

    $bd = new BDObject();
    $respuesta = "";

    if ($app == "ascensores")
        $respuesta = insertarDatosAscensor($bd);

    if ($app == "logistica")
        $respuesta = insertarDatosLogistica($bd);

    if ($app == "pedidos")
        $respuesta = insertarDatosPedidos($bd);

    if ($app == "dni")
        $respuesta = insertarDatosEscanerDNI($bd);

    if ($app == "reclamos")
        $respuesta = insertarDatosReclamos($bd);

    setMensaje("$respuesta \nApp $app instalada.");
    goOn();

    $bd->close();
}
?>
<!doctype html>
<html lang="es">

<head>
    <title>Instalar App - por SC3</title>

    <?php include("include-head.php"); ?>

</head>

<body>

    <?php
    $req = new FormValidator();
    ?>

    <form action="" id="form1" method="post">

        <table class="dlg">
            <tr>
                <td class="td_titulo" colspan="2">Instalar App</td>
            </tr>
            <tr>
                <td class="td_etiqueta">APP a instalar</td>
                <td class="td_dato">
                    <?php

                    $cboApp = new HtmlCombo("app", "");
                    $cboApp->addSeleccione();
                    $cboApp->setRequerido();
                    $cboApp->add("ascensores", "Ascensores");
                    $cboApp->add("logistica", "Logistica");
                    $cboApp->add("pedidos", "Pedidos");
                    $cboApp->add("dni", "DNI");
                    $cboApp->add("reclamos", "Reclamos");

                    echo ($cboApp->toHtml());
                    $req->add("app", "App");
                    ?>
                </td>
            </tr>

            <tr>
                <td class="td_etiqueta"></td>
                <td class="td_dato">
                    <?php
                    $bok = new HtmlBotonOkCancel();
                    echo ($bok->toHtml());
                    ?>
                </td>
            </tr>

        </table>

        <script type="text/javascript">
            <?php
            echo ($req->toScript());
            ?>

            function submitForm() {
                if (validar()) {
                    pleaseWait2();
                    document.getElementById('form1').submit();
                }
            }
        </script>

    </form>
</body>

</html>