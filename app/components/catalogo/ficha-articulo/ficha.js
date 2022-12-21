class FichaArticulo extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");

    }
    generateComponent(xid) {
        this.getTemplate((new App()).getUrlTemplate("ficha-articulo"), html => {
            let xxprueba = 'hola';
            console.log(xxprueba);
            console.log(typeof(xxprueba));
            this.setTemplateParameters(html, "imp", xxprueba);
            document.getElementById("app-container").innerHTML = html;
            
            //this.setTemplateParameters(html, "kit", xxprueba);

            let url = (new App()).getUrlApi("ficha-articulos"); 
            (new APIs()).call(url, "", "GET", (xdatos) => {
            console.log(xid);
        });

        });
    }

}

function agrandarFoto(){
    let objDivfoto = document.createElement("div");
    let objImg = new Image();
    objImg.src = "assets/imagenes/embrague.jpg"
    let objContainer = document.querySelector("#content-wrap");
    objDivfoto.className = "foto-grande-ficha";
    objDivfoto.append(objImg);
    objContainer.append(objDivfoto);
    objDivfoto.addEventListener("click", ()=>{
        objDivfoto.style.display = "none";
    })
}

