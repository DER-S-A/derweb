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

     constructor() {
        this.__objApp = new App();
    }

    /**
     * Permite generar el tab con los formularios para loguearse o registrarse.
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
            "components/seguridad/form-login.html", 
            "components/seguridad/form-register.html"
        ];

        // Creo el tab
        tabs = new HTMLTabsV2("tab-login", titulos, contenidos);
        tabs.toHtml();

        // Agrego el evento blur en donde se ingresa el usuario para detectar
        // si tengo que mostrar sucursales o no.
        document.getElementById("txtNroCliente").addEventListener("blur", () => {
            let url = (new App()).getUrlApi("app-entidades-get");
            let filter = "filter=\"usuario = '" + document.getElementById("txtNroCliente").value + "'\"";

            (new APIs).call(url, filter, "GET", (response) => {
                console.log(response)
                // Me fijo si tengo alguna sucursal asiganda
                if (response[0]["sucursales"].length !== 0) {
                    document.getElementById("selector-sucursales-container").style = "display:block";

                    // Cargo el select de sucursales.
                    document.getElementById("cboSucursales").innerHTML = '';
                    response[0]["sucursales"].forEach(element => {
                        let opciones = document.createElement("option");
                        opciones.value = element["id"];
                        opciones.textContent = element["nombre"];

                        if (element["predeterminado"] === 1)
                            opciones.selected = true;

                        document.getElementById("cboSucursales").appendChild(opciones);
                    });
                } else
                    document.getElementById("selector-sucursales-container").style = "display:none";
            });

            // Prendo el input clave.
            if (document.getElementById("txtNroCliente").value === "")
                document.getElementById("txtPassword").setAttribute("readonly", "true");
            else
                document.getElementById("txtPassword").removeAttribute("readonly");
        });

        // Agrego el evento change en donde se ingresa el usuario para limpiar la contraseña y hacer q hagan tab y
        // la vuelvan a poner y que genere sucursal si es q la tiene.
        document.getElementById("txtNroCliente").addEventListener("change", () => {
            console.log("aca cambiar")
            document.getElementById("txtPassword").value = "";
        })
    }

    /**
     * Permite verificar usuario y contraseña.
     */
    loginCliente() {
        var usuario = document.getElementById("txtNroCliente").value;
        var clave = document.getElementById("txtPassword").value;
        var objApp = new App();
        var aResultado;

        getAPI(objApp.getUrlApi("login") + "?usuario=" + usuario + "&clave=" + clave, 
            (xresponse) => {
                let form = document.getElementById("frmLogin");
                aResultado = JSON.parse(xresponse);

                if (aResultado.result == "OK") {
                    aResultado["id_sucursal"] = parseInt(document.getElementById("cboSucursales").value);
                    sessionStorage.setItem("derweb_sesion", JSON.stringify(aResultado));
                    console.log(aResultado)
                    /*form.action = this.sendToMainPage(aResultado["tipo_login"]);
                    form.method = "POST";
                    form.submit();*/
                }
                else {
                    alert(aResultado.mensaje);
                }

            });
    }

    /**
     * En base al tipo de login devuelve rutea al script que corresponde.
     * @param {string} xtipoLogin 
     * @returns 
     */
    sendToMainPage(xtipoLogin) {
        let page = "";
        switch (xtipoLogin) {
            case 'C':
                page = "main-clientes.php";
                break;
            case 'V':
                page = "main-vendedores.php";
                break;
            case 'T':
                page = "main-vendedores.php";
                break;
        }

        return page;
    }

    /**
    * Permite cambiar la clave en la base de datos mediante
    * la API correspondiente.
    */
    cambiarClave(xIDcliente,xClaveNueva,xClaveActual) {
        let xurlapi = this.__objApp.getUrlApi("app-entidades-cambiarClave") + "?reset-pass=" + JSON.stringify(xClaveNueva) + "&id=" + JSON.stringify(xIDcliente) + "&pass-actual=" + JSON.stringify(xClaveActual);
        let objAPI = new APIs();
        objAPI.put(xurlapi, (xResponse) => {
            this.aResponse = JSON.parse(xResponse);
        });
        return this.aResponse;
    }

    /**
    * Permite recuperar la clave en la base de datos mediante
    * la API correspondiente.
    */
    recuperarClave() {
        swal({
            title: 'Recuperar contraseña',
            content: {
              element: "div",
              attributes: {
                id: "container-recoveryPass",
                innerHTML: `
                  <input type="text" placeholder="Usuario" class="swal-content__input my-4">
                  <input type="email" placeholder="Email" class="swal-content__input my-4">
                  <input type="text" placeholder="Cuit" class="swal-content__input my-4">
                `,
              },
            },
        })
        .then(value => {
            if(value) {
                const arrayInput = document.querySelectorAll('#container-recoveryPass input');
                const url = (new App()).getUrlApi("app-entidades-olvide_mi_contrasenia");
                const argumentos = 'usuario=' + arrayInput[0].value + '&mail=' + arrayInput[1].value + '&cuit=' + arrayInput[2].value;
                //const argumentos = 'usuario=' + 'c23900' + '&mail=' + 'PETRUCCI@TALLERESPETRUCCI.COM.AR' + '&cuit=' + arrayInput[2].value;
                (new APIs()).call(url, argumentos, "POST", (xdatos) => {
                    console.log(xdatos[0].clave);
                    swal("Tu contraseña", xdatos[0].clave)
                });
            }
        })  
    }
}

function recoveryPass() {
    new Seguridad().recuperarClave();
}