<?php include("sc-cachebuster.php") ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo sc3CacheButer("css/app-style.css");?>" rel="stylesheet">
        <title>DER WEB</title>
        <script src="<?php echo sc3CacheButer("js/utils/funciones.js"); ?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/app.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("js/utils/apis.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/catalogo/catalogo.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("terceros/sweet-alert/sweetalert.min.js");?>" type="text/javascript"></script>
    </head>
    <body id="page-container">
        <div id="content-wrap">
            <header class="home-header">
                        <div class="row">
                            <div id="home-logo" class="col col-md-2">
                                <img src="assets/imagenes/logo.png" alt="" class="home-logo" />
                            </div>
                            <div id="app-header-content" class= "col col-md-1">
                                <a href="https://www.derdistribuciones.com.ar"><img src="assets/imagenes/volver.png" class="img-fluid" alt="Volver"/></a>
                            </div>
                        </div>
            </header>
            <main class="container-fluid">          
                <section id="app-container" class="app-container">

                </section>

            </main>
            
            <footer id="footer" class="home-footer">
                <div class="container home-footer-container">
                    <div class="row justify-content-center">
                        <div class="col-6 border-end" id="borde">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="home-titulo-footer">DER) DISTRIBUCIONES</h5>
                                        <small class="home-texto-footer">Somos un PYME familiar distribuidora
                                            de Autopartes, cuyo foco de negocios está
                                            en el conjunto "bajo capó", con un portafolio
                                            de más de 50.000 artículos en stock.
                                            Ofrecemos día a día a cada uno de
                                            nuestros clientes un eficiente servicio de
                                            abastecimiento a nivel nacional e internacional.
                                        </small>            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="home-titulo-footer">Atención al cliente</h5>
                                        <small class="home-texto-footer"><i class="fa-solid fa-phone"></i> (+5411) 4846 7500 <br/>
                                        <strong>LUN-VIE</strong> 08h/19h - <strong>SAB</strong> 08h/12h</small>                                        
                                    </div>
                                </div>
                                <div class="row align-middle" id="redSocial" style="align-items: center; margin-left: 0; margin-top: 10px">
                                    <div class="col-md-2">
                                        <a href="http://qr.afip.gob.ar/?qr=5Ip-AeTRpniGhP9pY4y5pg,,">
                                            <img class="home-footer-icon" src="assets/imagenes/icons/data-fiscal-8.png" alt=""></a>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="https://www.facebook.com/derdistribuciones/">
                                            <img class="home-footer-icon" src="assets/imagenes/icons/facebook-8.png" alt=""></a>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="https://www.instagram.com/der.distribuciones/">
                                            <img class="home-footer-icon" src="assets/imagenes/icons/instagram-8.png" alt=""></a>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="https://www.youtube.com/user/derdistribuciones/">
                                            <img class="home-footer-icon" src="assets/imagenes/icons/youtube-8.png" alt=""></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </footer>
        </div>

        <script src="<?php echo sc3CacheButer("components/tabs/tabs.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/seguridad/seguridad.js");?>" type="text/javascript"></script>
        <script src="<?php echo sc3CacheButer("components/seguridad/registrar-cliente-potencial.js");?>"></script>
        <script src="<?php echo sc3CacheButer("js/index.js");?>" type="text/javascript"></script>
    </body>
</html>