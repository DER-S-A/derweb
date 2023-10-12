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
    // Ejecuto el proceso cuando se recibe el formulario
    $id_marca = RequestInt("sel_marcas", "0");
    $id_rubro = RequestInt("sel_rubros", "0");
    $id_subrubro = RequestInt("sel_subrubros", "0");
    
    // Ejecuto el SP que permite cargar las novedades.
    $sql = "CALL sp_generar_articulos_novedades($mid, $id_marca, $id_rubro, $id_subrubro)";
    $rs = getRs($sql);
    if (sonIguales($rs->getValueInt("result"), 1))
        setWarning($rs->getValue("mensaje"));
    else
        setMensaje($rs->getValue("mensaje"));
    $rs->close();

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

			$input_sel_marcas = new HtmlSelector("sel_marcas", "marcas", 0);
			$req->add('sel_marcas', 'Marcas');
			$input_sel_marcas->setSizeSmall();
			$cpo_sel_marcas = new HtmlEtiquetaValor('Marcas', $input_sel_marcas->toHtml());
			$input_sel_rubros = new HtmlSelector("sel_rubros", "rubros", 0);
			$req->add('sel_rubros', 'Rubro');
			$input_sel_rubros->setSizeSmall();
			$cpo_sel_rubros = new HtmlEtiquetaValor('Rubro', $input_sel_rubros->toHtml());
			$input_sel_subrubros = new HtmlSelector("sel_subrubros", "subrubros", 0);
			$req->add('sel_subrubros', 'Subrubros');
			$input_sel_subrubros->setSizeSmall();
			$cpo_sel_subrubros = new HtmlEtiquetaValor('Subrubros', $input_sel_subrubros->toHtml());
			$tab->agregarSolapa("Datos", '', $cpo_sel_marcas->toHtml() . $cpo_sel_rubros->toHtml() . $cpo_sel_subrubros->toHtml() );

        ?>   

        <form id="form1" action="der-cnoticias-generar-articulos.php" method="post" id="form1" name="form1">
            <input type="hidden" id="mid" name="mid" value="<?php echo $mid; ?>"/>
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
