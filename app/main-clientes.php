<?php include("sc-cachebuster.php"); ?>
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
        <link href="<?php echo sc3CacheButer("components/catalogo/ficha-articulo/ficha.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/mi-carrito/boton-mi-carrito/boton-mi-carrito.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/mi-carrito/mi-carrito.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/catalogo/mi-carrito/grid-carrito/grid-carrito.css");?>" rel="stylesheet">
        <link href="<?php echo sc3CacheButer("components/menus/mi_perfil/mi_perfil_style.css");?>" rel="stylesheet">
        <link rel="stylesheet" href="terceros/Glider-js/css/normalize.css">
        <link rel="stylesheet" href="terceros/Glider-js/css/glider.css">
        <link rel="stylesheet" href="terceros/Glider-js/css/estilos.css">
        <link href="<?php echo sc3CacheButer("components/catalogo/ficha-articulo/estilo-carrusel.css");?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo sc3CacheButer("components/rentabilidad/style.css");?>">
        <link rel="stylesheet" href="<?php echo sc3CacheButer("components/rentabilidad/style-table.css");?>">

        <title>DER WEB</title>
        <script src="node_modules/jquery/dist/jquery.min.js" type="text/javascript"></script>
        <script src="node_modules/datatables.net/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="node_modules/datatables.net-responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/funciones.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/app.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/apis.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/cache-utils.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/catalogo.js");?>" type="text/javascript"></script>
        <script src="terceros/sweet-alert/sweetalert.min.js" type="text/javascript"></script>
        <script src="terceros/Glider-js/js/glider.js" type="text/javascript"></script>
        
    </head>
    <body id="page-container">
        <div class="transparente" id="transparente" style="display: none"></div>
        <div id="content-wrap">
            
        <header class="clientes-header app-header">
            <div class="cont-buscadorSimple my-2">
                <div class="cont-logoYlista ms-3">
                    <div class="logo"><a href="main-clientes.php"><img src="assets/imagenes/logo_app.png" alt=""></a></div>
                    <div id="lista-articulos-container">
                        <button class="lista_articulos ms-3" id="btnPushListaArticulo" name="btnPushListaArticulo">
                            <i class="fa-solid fa-bars mx-1"></i>
                            <span class="mx-1" >ARTíCULOS</span>
                        </button>
                    </div> 
                </div>
                <div class=cont-buscador>
                <a href="javascript:buscarPorFrase()"><i class="fa-solid fa-magnifying-glass"></i></a><input type="text" id="txtValorBuscado" name="txtValorBuscado" placeholder="Buscar" autocomplete="off">
                </div>
                <div class="cont-perfil me-3" id="menu-container">
                    <div class="nombre-suc me-3" id="num-cliente">SIN NOMBRE</div>
                    <div id="menu-container" class="menu-container">
                        <nav id="top-menu" class="nav-menu">
                            <i class="fa-solid fa-user" id="btnPushMenu"></i> 
                            <div id="menu-options" class="menu_options"></div>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="cont-buscadorAvan">
                <div class="buscadorAvan">
                    <div class="mb-3">
                        <label for="select_marca_repuesto" class="form-label">MARCA DE REPUESTO</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select_marca_repuesto">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="select_marca_vehiculo" class="form-label">MARCA DE VEHICULO</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select_marca_vehiculo">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="select_repuesto" class="form-label">REPUESTO</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select_repuesto">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="select_model" class="form-label">MODELO</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select_modelo">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="select_motor" class="form-label">MOTOR</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select_motor">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="select_anio" class="form-label">AÑO</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select_anio">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                    <button type="button" id="btnBuscar" name="btnBuscar" class="app-header-btnbuscar">
                        <i class="fa-solid fa-magnifying-glass"></i> 
                    </button>
                </div>
            </div>
        </header>




        <!-- ! Header viejo comentado -->
        <!-- <header class="clientes-header">
                <div class="row app-header">
                    <div class="col-md-1">
                        <a href="main-clientes.php"><img src="assets/imagenes/logo_app.png" class="app-header-logo" alt=""></a>
                    </div>
                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="app-header-buscar">
                                    <input type="text" id="txtValorBuscado" name="txtValorBuscado" class="app-input-buscar" placeholder="Buscar">
                                    <a href="javascript:buscarPorFrase()"><i class="fa-solid fa-magnifying-glass"></i></a>
                                </div>
                            </div>

                            <div class="col-md-10 buscador-mas-menu">
                                <div id="barra_busqueda" class="app-home-barra-opciones">
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
                                <div id="num-cliente" class="num-cliente"></div></li>
                            </div>                                
                        </div>
                    </div>
                </div>                    

                 Acá armar el menú principal 
                <div id="toolbar" class="toolbar-container">
                    <ul>
                        <li class="toolbar-col-1"><div id="menu-container" class="menu-container"></div></li>
                        <li class="toolbar-col-2"><div id="lista-articulos-container" class="lista-articulos-container"></div></li>
                         <li class="toolbar-col-3"><div id="num-cliente" class="num-cliente"></div></li> 
                        <li class="toolbar-col-4"><div id="boton-mi-carrito-container" class="boton-mi-carrito-container"></div></li>
                    </ul>
                </div>
            </header> -->

            <main class="container-fluid main-miperfil">
                <section id="app-container">
                    <div id="carrusel-container" class="carousel slide" data-bs-ride="carousel"></div>

                    <div class="iconos-home">
                        <div id="imgCatCta" class= "row">
                            <div class="col-auto catalogo">
                                <img src="assets/imagenes/icons/catalogos-8.png" alt="" aling="left"><b>MIRÁ LOS CATÁLOGOS ONLINE</b>
                            </div>    
                            <div class="col-auto border border-light">
                                <img src="assets/imagenes/icons/estado-cuenta-8.png" alt="" aling="left" id="imgCta"><b>¿TENES CUENTA CORRIENTE? REVISA SU ESTADO AQUI</b>
                            </div>
                        </div>
                        <div id="carrusel-footer" class="contenedor">
                            <div class="carousel">
                                <div class="carousel__contenedor" id="carrusel-container-footer"></div>
                                <div role="tablist" class="carousel__indicadores"></div>
                            </div>
                        </div>              
                    </div>
                </section>
                <div id="boton-mi-carrito-container" class="boton-mi-carrito-container"></div>
            </main>

            <footer class="app-footer">
                <div class="elementos">
                    <div class="redes">
                        <a href="https://www.facebook.com/derdistribuciones/?locale=es_LA" target="_blank"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://www.instagram.com/der.distribuciones/channel/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.youtube.com/@derdistribuciones" target="_blank"><i class="fa-brands fa-youtube"></a></i>
                    </div>

                    <div class="atclientes" style="text-align: center; font-size: 1vmax;">
                            <h2>Atencion al Cliente</h2>
                            <p>LUN - VIE 8h/19h SAB 8h/12h</p>
                    </div>
                    
                    <div class="telefono">
                        <img class="img-footer"src="assets/imagenes/phone.png" alt="telefono">
                        <p>(+5411) 4845-7500 / 69588882</p>
                    </div>

                    <div class="ubicacion">
                        <img class="img-footer" src="assets/imagenes/pin.png" alt="ubicacion">
                        <p>Colectora Este Panamericana 27887</p>
                    </div>

                

                </div>
            </footer>
            
            
            <!-- <footer class="app-footer">
                <div class="app-footer-container container-fluid">
                    <div class="row">
                        <div class="col-lg-3">
                            <ul class="app-social-bar">
                                <li>
                                    <a href="https://www.facebook.com/derdistribuciones/"><i class="fa-brands fa-facebook"></i></a>
                                </li>

                                <li>
                                    <a href="https://www.instagram.com/der.distribuciones/"><i class="fa-brands fa-instagram"></i></a>
                                </li>

                                <li>
                                    <a href="https://www.youtube.com/user/derdistribuciones/"><i class="fa-brands fa-youtube"></i></a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-4 contenedor-info">
                            <p class="app-footer-info">
                                <small class="home-texto-footer"><span class="home-titulo-footer">Atención al cliente</span>
                                    <strong>LUN-VIE</strong> 08h/19h - <strong>SAB</strong> 08h/12h
                                    <i class="fa-solid fa-phone"></i> (+5411) 4846 7500
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>     -->


        </div>

        <script src="<?php echo sc3CacheButer("components/utilities/component-manager.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/tabs/tabs.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/menus/menus.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/lista-articulos/lista-articulos.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/carrusel/carrusel.js");?>" type="text/javascript"></script>
        
        <!-- <script src="https://cdn.jsdelivr.net/npm/glider-js@1.7.3/glider.min.js"></script> -->
        <script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>
        <script src="components/carrusel_footer/carruselfooter.js" type="text/javascript"></script>

        <!-- Referencias para generar la página de catálogo -->
        <script src="<?php echo sc3CacheButer("components/catalogo/panel-opciones/panel-opciones.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/catalogo-gui.js");?>" type="text/javascript"></script>

        <!-- Referencias al componente grilla -->
        <script src="<?php echo sc3CacheButer("components/catalogo/catalogo-funciones.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/grid-articulos/grid-articulos.js");?>" type="text/javascript"></script>

        <!-- Componente de seguridad -->
        <script src="<?php echo sc3CacheButer("components/seguridad/seguridad.js");?>" type="text/javascript"></script>

        <script src="<?php echo sc3CacheButer("components/catalogo/confirmacion-pedido/confirmacion-pedido.js");?>" type="text/javascript"></script>

        <!-- Componente botón mi carrito -->
        <script src="<?php echo sc3CacheButer("components/catalogo/mi-carrito/boton-mi-carrito/boton-mi-carrito.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/mi-carrito/mi-carrito.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/mi-carrito/grid-carrito/grid-carrito.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/ficha-articulo/ficha.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/rentabilidad/rentabilidad.js");?>" type="text/javascript"></script>

        <script src="<?php echo sc3CacheButer("components/menus/mi_perfil/mi_perfil.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/menus/mi_perfil/cambiar_contraseña.js");?>" type="text/javascript"></script>
        
        <script src="<?php echo sc3CacheButer("js/main-clientes.js");?>" type="text/javascript"></script>
    </body>
</html>