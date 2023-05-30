<?php include("sc-cachebuster.php") ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link href="node_modules/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="node_modules/datatables.net-responsive/css/responsive.dataTables.min.css" rel="stylesheet">

        <link href="<?php echo sc3CacheButer("css/app-style.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/menus/menus.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/lista-articulos/lista-articulos.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/catalogo-gui.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/panel-opciones/panel-opciones.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/grid-articulos/grid-articulos.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/mi-carrito/boton-mi-carrito/boton-mi-carrito.css");?>" rel="stylesheet">
        <!--<link href="components/catalogo/mi-carrito/mi-carrito.css" rel="stylesheet">
        <link href="components/catalogo/mi-carrito/grid-carrito/grid-carrito.css" rel="stylesheet">-->
        <link href="<?php echo sc3CacheButer("components/catalogo/vendedores/pedidos-vendedores-gui.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/menus/mi_perfil/mi_perfil_style.css");?>" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glider-js@1.7.3/glider.min.css">
        <link rel="stylesheet" href="terceros/Glider-js/css/estilos.css">
        <link rel="stylesheet" href="terceros/lfw-datagrid/lfwdatagrid.css">
        <link rel="stylesheet" href="<?php echo sc3CacheButer("components/catalogo/ingreso-pedidos-rapidos/ingreso-pedidos-rapido.css");?>">

        <title>DER WEB</title>
        <script src="node_modules/jquery/dist/jquery.min.js" type="text/javascript"></script>
        <script src="node_modules/datatables.net/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="node_modules/datatables.net-responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
        <script src="node_modules/datatables.net-keytable/js/dataTables.keyTable.min.js" type="text/javascript>"></script>
        <script src="node_modules/datatables.net-editor/js/dataTables.editor.min.js" type="text/javascript"></script>

        <script src="<?php echo sc3CacheButer("js/utils/funciones.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/app.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/apis.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/cache-utils.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/catalogo.js");?>" type="text/javascript"></script>
        <script src="terceros/sweet-alert/sweetalert.min.js" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("terceros/lfw-datalist-bs/lfw-datalist-bs.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/confirmacion-pedido/confirmacion-pedido.js");?>"></script>
    </head>
    <body id="page-container">
        <div id="content-wrap">
            <header>
                <div class="row app-header">
                    <div class="col-md-1">
                        <a href="main-vendedores.php"><img src="assets/imagenes/logo_app.png" class="app-header-logo" alt=""></a>
                    </div>
                    <div class="col-md-11">
                        <div class="row app-header-vendedor-row" id="app-header-vendedor-row">
                            <div class="col-lg-12 buscador-mas-menu buscador-mas-menu-vendedor">
                                <div id="barra_busqueda" class="app-home-barra-opciones" style="display: none;">
                                    <ul>
                                        <li>
                                            <label for="select_marca_repuesto">MARCA DE REPUESTO</label>
                                            <select id="select_marca_repuesto" name="select_marca_repuesto">
                                                <option>Seleccionar</option>
                                            </select>
                                        </li>

                                        <li>
                                            <label for="select_marca_vehiculo">MARCA DE VEHICULO</label>
                                            <select id="select_marca_vehiculo" name="select_marca_vehiculo">
                                                <option>Seleccionar</option>
                                            </select>
                                        </li>

                                        <li>
                                            <label for="select_repuesto">REPUESTO</label>
                                            <select id="select_repuesto" name="select_repuesto">
                                                <option>Seleccionar</option>
                                            </select>
                                        </li>

                                        <li>
                                            <label for="select_modelo">MODELO</label>
                                            <select id="select_modelo" name="select_modelo">
                                                <option>Seleccionar</option>
                                            </select>
                                        </li>

                                        <li>
                                            <label for="select_motor">MOTOR</label>
                                            <select id="select_motor" name="select_motor">
                                                <option>Seleccionar</option>
                                            </select>
                                        </li>

                                        <li>
                                            <label for="select_anio">AÑO</label>
                                            <select id="select_anio" name="select_anio">
                                                <option>Seleccionar</option>
                                            </select>
                                        </li>

                                        <li style=" width: auto">
                                            <button type="button" id="btnBuscar" name="btnBuscar" class="app-header-btnbuscar">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div id="menu-container" class="menu-container"></div>
                            </div>                                
                        </div>
                    </div>
                </div>                    

                <!-- Acá armar el menú principal -->
                <div id="toolbar" class="toolbar-container">
                    <ul>
                        <!-- <li class="toolbar-col-1"><div id="menu-container" class="menu-container"></div></li> -->
                        <li class="toolbar-col-2"><div id="lista-articulos-container" class="lista-articulos-container"></div></li>
                        <li class="toolbar-col-3"><div id="boton-mi-carrito-container" class="boton-mi-carrito-container"></div></li>
                    </ul>
                </div>
            </header>

            <main class="container">
                <section id="app-container">
                    <form id="formulario">
                        <div class="row">
                            <div class="col-md-12 detalle-pedido-row" id="app_grid_container"></div>
                        </div>
                    </form>

                    <div id="modal-pedidos"></div>
                </section>
            </main>

            <footer id="app-footer" class="app-footer" style="bottom: 0; position:fixed;">
                <div class="app-footer-container container-fluid">
                    <div class="row">
                        <div class="col-lg-3">
                            <ul class="app-social-bar">
                            <li>
                                    <!-- <a href="https://www.facebook.com/derdistribuciones/">
                                        <img class="home-footer-icon" src="assets/imagenes/icons/facebook-8.png" alt=""></a> -->
                                    <a href="https://www.facebook.com/derdistribuciones/"><i class="fa-brands fa-facebook"></i></a>
                                </li>

                                <li>
                                    <!-- <a href="https://www.instagram.com/der.distribuciones/">
                                        <img class="home-footer-icon" src="assets/imagenes/icons/instagram-8.png" alt=""></a> -->
                                    <a href="https://www.instagram.com/der.distribuciones/"><i class="fa-brands fa-instagram"></i></a>
                                </li>

                                <li>
                                    <!-- <a href="https://www.youtube.com/user/derdistribuciones/">
                                        <img class="home-footer-icon" src="assets/imagenes/icons/youtube-8.png" alt=""></a> -->
                                    <a href="https://www.youtube.com/user/derdistribuciones/"><i class="fa-brands fa-youtube"></i></a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-lg-4">
                            <ul class="app-footer-info">
                                <li>
                                    <small class="home-texto-footer"><span class="home-titulo-footer">Atención al cliente</span><br>
                                        <strong>LUN-VIE</strong> 08h/19h - <strong>SAB</strong> 08h/12h</small>
                                </li>

                                <li>
                                    <small class="home-texto-footer"><i class="fa-solid fa-phone"></i> (+5411) 4846 7500</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>    


        </div>

        <script src="<?php echo sc3CacheButer("components/utilities/component-manager.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/tabs/tabs.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/menus/menus.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/lista-articulos/lista-articulos.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/carrusel/carrusel.js");?>" type="text/javascript"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/glider-js@1.7.3/glider.min.js"></script>
        <script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>
        <script src="<?php echo sc3CacheButer("components/carrusel_footer/carruselfooter.js");?>" type="text/javascript"></script>

        <!-- Referencias a las clases APIs. -->
        <script src="<?php echo sc3CacheButer("derweb-apis-library/pedidos-api.js");?>" type="text/javascript"></script>

        <!-- Referencias para generar la página de catálogo -->
        <script src="<?php echo sc3CacheButer("components/catalogo/panel-opciones/panel-opciones.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/catalogo-gui.js");?>" type="text/javascript"></script>

        <!-- Referencias al componente grilla -->
        <script src="<?php echo sc3CacheButer("components/catalogo/grid-articulos/grid-articulos.js");?>" type="text/javascript"></script>

        <script src="<?php echo sc3CacheButer("components/seguridad/seguridad.js");?>" type="text/javascript"></script>

        <!-- Componente botón mi carrito -->
        <script src="<?php echo sc3CacheButer("components/catalogo/mi-carrito/boton-mi-carrito/boton-mi-carrito.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/mi-carrito/mi-carrito.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/mi-carrito/grid-carrito/grid-carrito.js");?>" type="text/javascript"></script>


        <script src="<?php echo sc3CacheButer("components/catalogo/vendedores/pedidos-pendientes/pedidos-pendientes.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/vendedores/pedidos-pendientes/edicion-pedidos-pendientes.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/ingreso-pedidos-rapidos/ingreso-pedidos-rapidos-gui.js");?>" type="text/javascript"></script>
        <script src="components/buscador/buscador.js" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/ingreso-pedidos-rapidos/funciones.js");?>" type="text/javascript"></script>

        <script src="components/aviso_pago/avisoPago.js" type="text/javascript"></script>

        <script src="<?php echo sc3CacheButer("components/menus/mi_perfil/mi_perfil.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/menus/mi_perfil/cambiar_contraseña.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("terceros/lfw-datagrid/lfwdatagrid.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("terceros/lfw-modal-bs/lfw-modal-bs.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("terceros/lfw-controls-bs/html-input.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/main-vendedores.js");?>" type="text/javascript"></script>
    </body>
</html>