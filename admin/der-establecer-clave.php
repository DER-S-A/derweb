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
    // Hago el cambio de clave
    $clave = Request("password");
    $sql = "UPDATE entidades
            SET entidades.clave = '$clave'
            WHERE entidades.id = $mid";

    $objBD = new BDObject();
    $objBD->execQuery($sql);
    $objBD->close();

    setMensaje("La clave fué establecida con éxito");
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

			$input_entidad_seleccionada = new HtmlSelector("entidad_seleccionada", "entidades", $mid);
			$input_entidad_seleccionada->setReadOnly(true);
			$input_entidad_seleccionada->setRequerido();
			$req->add('entidad_seleccionada', 'Entidad:');
			$input_entidad_seleccionada->setSizeSmall();
			$cpo_entidad_seleccionada = new HtmlEtiquetaValor('Entidad:', $input_entidad_seleccionada->toHtml());
			$input_password = new HtmlInputText("password", '');
			$input_password->setRequerido();
            $input_password->setTypePassword();
			$req->add('password', 'Clave');
			$input_password->setDefault("");
			$cpo_password = new HtmlEtiquetaValor('Clave', $input_password->toHtml());
			$input_password_confirm = new HtmlInputText("password_confirm", '');
            $input_password_confirm->setTypePassword();
			$cpo_password_confirm = new HtmlEtiquetaValor('Confirmar clave', $input_password_confirm->toHtml());
			$tab->agregarSolapa("Datos", '', $cpo_entidad_seleccionada->toHtml() . $cpo_password->toHtml() . $cpo_password_confirm->toHtml() );

        ?>   

        <form id="form1" action="der-establecer-clave.php" method="post" id="form1" name="form1">
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

            /**
             * Valido el ingreso de formularios.
             */
            function validar() {
                let password = document.getElementById("password").value;
                let passwordConfirm = document.getElementById("password_confirm").value;

                if (password !== passwordConfirm) {
                    alert("Las claves deben coincidir.");
                    return false;
                }

                return true;
            }
        </script>
    </body>
</html>
