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
    </head>
    <body id="page-container">
        <div id="content-wrap">
            <main class="container-fluid">
                <app-header 
                    template="components/header/header.html" 
                    back-button
                    back-button-url="http://derdistribuciones.com.ar"></app-header>

                <section id="app-container" class="app-container"><p>Diseñar página principal</p></section>
            </main>

            <app-footer id="footer" template="components/footer/footer.html"></app-footer>
        </div>

        <script src="components/tabs/tabs.js" type="text/javascript"></script>
        <script src="modulos/seguridad/seguridad.js" type="text/javascript"></script>
        <script src="js/main-clientes.js" type="text/javascript"></script>
    </body>
</html>