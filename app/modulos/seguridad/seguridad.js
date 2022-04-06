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

    llenarSelectRubros() {
        getAPI("services/rubros.php/get", (xresponse) => {
            var objSelect = document.getElementById("cboRubro");
            var objCheckBoxes = document.createElement("div");
            var objOption = document.createElement("option");
            var aRubros = JSON.parse(xresponse);

            objOption.value = -1;
            objOption.innerText = "Hace un click para mostrar opciones y/o ocultar";
            objSelect.appendChild(objOption);

            aRubros.forEach((xElement) => {
                let objSpan = document.createElement("span");
                let objLabel = document.createElement("label");
                let objInput = document.createElement("input");

                objSpan.innerHTML = xElement.descripcion;
                objInput.type = "checkbox";
                objInput.id = xElement.id;
                objInput.name = xElement.id;
                objInput.classList.add("form-check-input");
                
                objLabel.appendChild(objInput);
                objLabel.appendChild(objSpan);
                objCheckBoxes.id = "checkboxes";
                objCheckBoxes.appendChild(objLabel);
            });
            document.getElementById("cboRubros_Multiselect").appendChild(objCheckBoxes);
        });
    }
}