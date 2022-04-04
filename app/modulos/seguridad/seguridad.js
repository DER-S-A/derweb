/**
 * Contiene la clase correspondiente al módulo de seguridad.
 */

class Seguridad {
    /**
     * Permite crear la pantalla de login y registro que se muestra en la pantalla
     * inicial.
     * 
     * Dependencia: components/tabs/tabs-v2.js
     */

    generateFormLogin() {
        var tabs = null;
        
        // Establezco los títulos de las pestañas para pasarle al control tab.
        var titulos = [
            "SOY CLIENTE", 
            "Quiero ser cliente"
        ];

        // Armo el array con la url donde están los formularios que se van
        // a mostrar en el tab.
        var contenidos = [
            "modulos/seguridad/form-login.html", 
            "modulos/seguridad/form-register.html"
        ];

        // Creo el tab
        tabs = new HTMLTabsV2("tab-login", titulos, contenidos);
        tabs.toHtml();        
    }
}