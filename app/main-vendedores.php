<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link href="css/app-style.css" rel="stylesheet">
        <link href="components/menus/menus.css" rel="stylesheet">
        <link href="components/lista-articulos/lista-articulos.css" rel="stylesheet">
        <link href="components/catalogo/catalogo-gui.css" rel="stylesheet">
        <link href="components/catalogo/panel-opciones/panel-opciones.css" rel="stylesheet">
        <link href="components/catalogo/grid-articulos/grid-articulos.css" rel="stylesheet">
        <link href="components/catalogo/mi-carrito/boton-mi-carrito/boton-mi-carrito.css" rel="stylesheet">
        <link href="components/catalogo/mi-carrito/mi-carrito.css" rel="stylesheet">
        <link href="components/catalogo/mi-carrito/grid-carrito/grid-carrito.css" rel="stylesheet">
        <link href="components/menus/mi_perfil/mi_perfil_style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.1/normalize.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glider-js@1.7.3/glider.min.css">
        <link rel="stylesheet" href="terceros/Glider-js/css/estilos.css">
        <link rel="stylesheet" href="terceros/lfw-datagrid/lfwdatagrid.css">

        <title>DER WEB</title>
        <script src="js/utils/funciones.js" type="text/javascript"></script>
        <script src="js/utils/app.js" type="text/javascript"></script>
        <script src="js/utils/apis.js" type="text/javascript"></script>
        <script src="js/utils/cache-utils.js" type="text/javascript"></script>
        <script src="components/catalogo/catalogo.js" type="text/javascript"></script>
        <script src="terceros/sweet-alert/sweetalert.min.js" type="text/javascript"></script>
    </head>
    <body id="page-container">
        <div id="content-wrap">
            <header>
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

                            <div class="col-md-10">
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

                                        <li>
                                            <button type="button" id="btnBuscar" name="btnBuscar" class="app-header-btnbuscar">
                                                <i class="fa-solid fa-magnifying-glass"></i> BUSCAR
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>                                
                        </div>
                    </div>
                </div>                    

                <!-- Acá armar el menú principal -->
                <div id="toolbar" class="toolbar-container">
                    <ul>
                        <li class="toolbar-col-1"><div id="menu-container" class="menu-container"></div></li>
                        <li class="toolbar-col-2"><div id="lista-articulos-container" class="lista-articulos-container"></div></li>
                        <li class="toolbar-col-3"><div id="boton-mi-carrito-container" class="boton-mi-carrito-container"></div></li>
                    </ul>
                </div>
            </header>

            <main class="container-fluid">
                <section id="app-container">
                    <form id="form-seleccion-clientes">
                        <div class="row">
                            <div class="col-md-12" id="grid_clientes"></div>
                        </div>
                    </form>
                </section>
            </main>

            <footer class="app-footer" style="bottom: 0; position:fixed;">
                <div class="app-footer-container container">
                    <div class="row">
                        <div class="col-lg-3">
                            <ul class="app-social-bar">
                                <li>
                                    <a href="https://www.facebook.com/derdistribuciones/">
                                        <img class="home-footer-icon" src="assets/imagenes/icons/facebook-8.png" alt=""></a>
                                </li>

                                <li>
                                    <a href="https://www.instagram.com/der.distribuciones/">
                                        <img class="home-footer-icon" src="assets/imagenes/icons/instagram-8.png" alt=""></a>
                                </li>

                                <li>
                                    <a href="https://www.youtube.com/user/derdistribuciones/">
                                        <img class="home-footer-icon" src="assets/imagenes/icons/youtube-8.png" alt=""></a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-9">
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

        <script src="components/utilities/component-manager.js" type="text/javascript"></script>
        <script src="components/tabs/tabs.js" type="text/javascript"></script>
        <script src="components/menus/menus.js" type="text/javascript"></script>
        <script src="components/lista-articulos/lista-articulos.js" type="text/javascript"></script>
        <script src="components/carrusel/carrusel.js" type="text/javascript"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/glider-js@1.7.3/glider.min.js"></script>
        <script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>
        <script src="components/carrusel_footer/carruselfooter.js" type="text/javascript"></script>

        <!-- Referencias para generar la página de catálogo -->
        <script src="components/catalogo/panel-opciones/panel-opciones.js" type="text/javascript"></script>
        <script src="components/catalogo/catalogo-gui.js" type="text/javascript"></script>

        <!-- Referencias al componente grilla -->
        <script src="components/catalogo/grid-articulos/grid-articulos.js" type="text/javascript"></script>

        <script src="components/seguridad/seguridad.js" type="text/javascript"></script>

        <!-- Componente botón mi carrito -->
        <script src="components/catalogo/mi-carrito/boton-mi-carrito/boton-mi-carrito.js" type="text/javascript"></script>
        <script src="components/catalogo/mi-carrito/mi-carrito.js" type="text/javascript"></script>
        <script src="components/catalogo/mi-carrito/grid-carrito/grid-carrito.js" type="text/javascript"></script>
        <script src="components/menus/mi_perfil/mi_perfil.js" type="text/javascript"></script>
        <script src="components/menus/mi_perfil/cambiar_contraseña.js" type="text/javascript"></script>
        <script src="terceros/lfw-datagrid/lfwdatagrid.js" type="text/javascript"></script>
        <script src="js/main-vendedores.js" type="text/javascript"></script>
    </body>
</html>