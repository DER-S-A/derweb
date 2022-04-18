<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/app-style.css" rel="stylesheet">
        <title>DER WEB</title>
        <script src="js/utils/funciones.js" type="text/javascript"></script>
        <script src="js/utils/app.js" type="text/javascript"></script>
        <script src="components/tabs/tabs.js" type="text/javascript"></script>
        <script src="modulos/seguridad/seguridad.js" type="text/javascript"></script>
        <script src="js/main-clientes.js" type="text/javascript"></script>        
    </head>
    <body id="page-container">
        <div id="content-wrap">
            <main class="container-fluid">
                <header>         
                    <div class="row app-header">
                        <div class="col-md-1">
                            <img src="assets/imagenes/logo.png" class="app-header-logo" alt="" />
                        </div>
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="app-header-buscar">
                                        <input type="text" id="txtValorBuscado" name="txtValorBuscado" class="app-input-buscar" placeholder="Buscar">
                                        <a href="javascript:buscar();"><i class="fa-solid fa-magnifying-glass"></i></a>
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

                                    <div class="menu-perfil-container">
                                        <button type="button" id="btn_perfil" name="btn_perfil" class="boton-perfil">
                                            <i class="fa-solid fa-user"></i><i class="fa-solid fa-chevron-down"></i>
                                        </button>

                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>                    

                    <!-- Acá armar el menú principal -->
                    <nav class="row" style="background-color: lightgray;">
                        <div class="col-md-12">
                            <P>Menu</P>
                        </div>
                    </nav>                    
                </header>

                <section id="app-container" class="app-container"><p>Contenedor de aplicación</p></section>
            </main>

            <footer class="app-footer">
                <div class="app-footer-container container">
                    <div class="row">
                        <div class="col-md-3">
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
    </body>
</html>