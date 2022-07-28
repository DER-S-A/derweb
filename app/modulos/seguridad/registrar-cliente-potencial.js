/**
 * Esta clase permite registrar a un cliente potencial desde el formulario
 * de registros.
 */

class RegistrarClientePotencial {

    /**
     * En el constructor defino las propiedades de la clase
     */
    constructor() {
        this.eMail = "";
        this.razonSocial = "";
        this.telefono = "";
        this.ubicacion = "";
        this.aIdsRubrosSeleccioandos = new Array();
        this.aResponse = new Array();
    }

    /**
     * Permite llenar el selector de rubros de venta con la posibilidad de tildar varios
     */
     llenarSelectRubros() {
        var objSelect = document.getElementById("cboRubro");
        var objCheckBoxes = document.createElement("div");
        var objOption = document.createElement("option");
        var objCatalogo = new Catalogo();
        var aRubros = objCatalogo.getRubros();
    
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
    }

    /**
     * Permite ingresar un cliente registrado.
     */
     registrarClientePotencial() {
        var aRegistro = new Array();
        var aRubrosSeleccionados = new Array();
        var objCatalogo = new Catalogo();
        var objAPI = new APIs();
        var url = "";
        var respuesta = new Array();
        
        // Recupero la descripción de los rubros de ventas seleccionados para
        // respetar el protocolo del EndPoint.
        this.aIdsRubrosSeleccioandos.forEach((xelement) => {
            let aResultado = objCatalogo.getRubros("id = " + parseInt(xelement));
            aRubrosSeleccionados.push({"id": parseInt(aResultado[0].id), "descripcion": aResultado[0].descripcion});
        });

        // Genero el JSON para enviar al EndPoint
        aRegistro = {
            "apenom": this.razonSocial, 
            "telefono": this.telefono, 
            "email": this.eMail, 
            "ubicacion": this.ubicacion, 
            "rubros" : aRubrosSeleccionados
        }
        console.warn(JSON.stringify(aRegistro));

        url = "services/cliente-potencial.php/registrarCliente?registro=" + JSON.stringify(aRegistro);
        objAPI.put(url, (xResponse) => {
            this.aResponse = JSON.parse(xResponse);
        });

        return this.aResponse;
    }
}