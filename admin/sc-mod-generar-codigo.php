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
    $codigo = array();
    $rsmodulo = ModulosModel::getModuloPorId($mid);    
    $phpFileName = $rsmodulo->getValue("php_file_name");
    $rsgrupos = ModulosModel::getGrupos($phpFileName);
    $titulo_solapa = "";
    while (!$rsgrupos->EOF()) {
        if (!esVacio($rsgrupos->getValue("grupo")))
            $titulo_solapa = $rsgrupos->getValue("grupo");

        $rscontroles = ModulosModel::getInputsForms($phpFileName, $titulo_solapa);
        $html = "";
        while (!$rscontroles->EOF()) {
            $nombre_objeto = "\$input_" . $rscontroles->getValue("input_id");
            $nombre_obj_campo = "\$cpo_" . $rscontroles->getValue("input_id");
            if (sonIguales($rscontroles->getValue("tipo_campo"), "HtmlSelector")) {
                // Si es selector tengo qeu pasar el query asociado al input.

                $rsQuery = ModulosModel::getSelectorQueryName(intval($rscontroles->getValue("id_query")));
                $php_code = "$nombre_objeto = new " . $rscontroles->getValue("tipo_campo")
                    . "(\"" . $rscontroles->getValue("input_id") . "\", \"". $rsQuery->getValue("queryname") . "\", 0);";

            } elseif (sonIguales($rscontroles->getValue("tipo_campo"), "HtmlBoolean") 
                    || sonIguales($rscontroles->getValue("tipo_campo"), "HtmlBoolean2")) {
                // En caso de campos bool se pasa 0 o 1 como valor por defecto.

                $php_code = "$nombre_objeto = new " . $rscontroles->getValue("tipo_campo")
                    . "(\"" . $rscontroles->getValue("input_id") . "\", " . $rscontroles->getValue("htmlbool_default") . ");";

            } elseif (sonIguales($rscontroles->getValue("tipo_campo"), "HtmlInputFile")
                    || sonIguales($rscontroles->getValue("tipo_campo"), "HtmlInputFile2")
                    || sonIguales($rscontroles->getValue("tipo_campo"), "HtmlRichText")
                    || sonIguales($rscontroles->getValue("tipo_campo"), "HtmlInputText")
                    || sonIguales($rscontroles->getValue("tipo_campo"), "HtmlCombo")) {
                // A estos controles les paso un string vacío como valor por defecto.        
                
                $php_code = "$nombre_objeto = new " . $rscontroles->getValue("tipo_campo")
                    . "(\"" . $rscontroles->getValue("input_id") . "\", '');";
            } else {
                // Por descarte quedan los controles que solo se le pasa ID al constructor.
                $php_code = "$nombre_objeto = new " . $rscontroles->getValue("tipo_campo")
                    . "(\"" . $rscontroles->getValue("input_id") . "\");";
            }

            $codigo[] = $php_code;
            // Reemplazo el nombre de la instancia para que pueda ir autoevaluando el
            // código y saber qué métodos se pueden aplicar por cada objeto.
            $php_code = str_replace($nombre_objeto, "\$instancia", $php_code);
            eval($php_code);

            // A continuación valido las propiedades verificando si se encuentran los
            // métodos definidos en la clase.
            if (method_exists($instancia, "setReadOnly")) {
                if ($rscontroles->getValue("readonly") == 1)
                    $codigo[] = $nombre_objeto . "->setReadOnly(true);";
            }

            if (method_exists($instancia, "setRequerido")) {
                if ($rscontroles->getvalue("requerido") == 1)
                    $codigo[] = $nombre_objeto . "->setRequerido();";
                    $codigo[] = "\$req->add('" . $rscontroles->getValue("input_id") . "', '". $rscontroles->getValue("etiqueta") ."');";
            }

            if (method_exists($instancia, "setSizeSmall")) {
                if ($rscontroles->getValue("selector_size_small") == 1)
                    $codigo[] = $nombre_objeto . "->setSizeSmall();";       
            }

            if (method_exists($instancia, "setTypeFloat")) {
                if ($rscontroles->getValue("valor_numerico") == 1) {
                    $codigo[] = $nombre_objeto . "->setTypeFloat(" . $rscontroles->getValue("cant_decimales") . ");";
                    $codigo[] = $nombre_objeto . "->setDefault(\"0\");";
                } else {
                    $codigo[] = $nombre_objeto . "->setDefault(\"\");";
                }
            }

            if (sonIguales($rscontroles->getValue("tipo_campo"), "HtmlCombo")) {
                // Armo el esqueleto para llenar operaciones
                $codigo[] = "//TODO: A continuación llenar el combo con las opciones.";
                $codigo[] = $nombre_objeto . "->add('1', 'Opcion1');";
                $codigo[] = $nombre_objeto . "->add('2', 'Opcion2');";
                $codigo[] = $nombre_objeto . "->add('3', 'Opcion3');";
                $codigo[] = "// -- Fin de opciones HtmlCombo";
            }

            $codigo[] = "$nombre_obj_campo = new HtmlEtiquetaValor('" . $rscontroles->getValue("etiqueta") . "', $nombre_objeto" . "->toHtml());";
            $html .= "$nombre_obj_campo" . "->toHtml() . ";
    
            $rscontroles->next();
        }

        $html = substr($html, 0, strlen($html) - 2); // Saco el . espacio que me queda al final
        $codigo[] = "\$tab->agregarSolapa(\"" . (esVacio($titulo_solapa) ? "Datos" : $titulo_solapa) . "\"" . ", '', $html);";    

        $rsgrupos->next();
    }

    $codigo_aux = "";
    for ($i = 0; $i < sizeof($codigo); $i++)
        $codigo_aux .= "\t\t\t" . $codigo[$i] . "\n";

    $rsmodulo = ModulosModel::getModuloPorId($mid);
    //$phpFileNameDestino = $rsmodulo->getValue("php_file_name");
    $codigo_final = file_get_contents("modulos/generador_operaciones/php_template_code_proceso.php");
    $codigo_final = str_replace("{codigo_php}", $codigo_aux, $codigo_final);
    $codigo_final = str_replace("{nombre_script}", $rsmodulo->getValue("php_file_name"), $codigo_final);
    $destino = __DIR__ . "/" . $phpFileName;
    file_put_contents($destino, $codigo_final);

    $codigo_updversion = array();
    $codigo_updversion[] = "function function_name() {\n";
    $codigo_updversion[] = "\t\$opid = sc3AgregarOperacion(\n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("nombre") . "\", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("php_file_name") . "\", \n";
    $codigo_updversion[] = "\t\t\"" . (esVacio($rsmodulo->getValue("icono")) ? "images/table.png" : $rsmodulo->getValue("icono")) . "\", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("ayuda") . "\", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("tabla") . "\", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("menu") . "\", \n";
    $codigo_updversion[] = "\t\t" . $rsmodulo->getValue("grupal") . ", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("perfil") . "\", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("target") . "\", \n";
    $codigo_updversion[] = "\t\t" . $rsmodulo->getValue("emergente") . ", \n";
    $codigo_updversion[] = "\t\t\"" . $rsmodulo->getValue("queryname") . "\");\n";
    $codigo_updversion[] = "\tsc3AgregarOpAPerfil(\$opid, \"Nombre_Perfil\");\n";
    $codigo_updversion[] = "}";

    $codUpdVersionFinal = "";
    for ($i = 0; $i < sizeof($codigo_updversion); $i++)
        $codUpdVersionFinal .= $codigo_updversion[$i];

    //goOn(); // En caso de no necesitarlo sacarlo.
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

            if (enviado()) {
                $objUpdVersion = new HtmlInputTextarea("codigo_upd_version", $codUpdVersionFinal);
                $campo = new HtmlEtiquetaValor("Copiar código y pegar en updversion", $objUpdVersion->toHtml());
                $btnOkCancel = new HtmlBotonOkCancel(true, false);
                $tab->agregarSolapa("Generar código", "", $campo->toHtml() . $btnOkCancel->toHtml());
            } else {
                $btnOkCancel = new HtmlBotonOkCancel();
                $tab->agregarSolapa("Generar código", "", $btnOkCancel->toHtml());
            }

            ////////////////////////////////////////////////////////////////////////////////
            // TODO: Agregar controles a medida.
        ?>   

        <form id="form1" action="sc-mod-generar-codigo.php" method="post">
            <input type="hidden" id="mid" name="mid" value="<?php echo $mid; ?>" />
            
            <!-- TODO: Desarrollar el HTML con el diseño del formulario 
                Se recomienda utilizar las clases de W3.CSS para armar el layout.
            -->
            <div class="div-botones">
                <?php echo $tab->toHtml(); ?>
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
            TODO: A continuación desarrollar el javascript.
                El javascript se puede referenciar a un archvo js o bien desarrollarlo acá mismo.
        -->
        <script type="text/javascript">
            <?php echo $req->toScript(); ?>

            function submitForm() {
                pleaseWait2();
                document.forms[0].submit();
            }
        </script>
    </body>
</html>
