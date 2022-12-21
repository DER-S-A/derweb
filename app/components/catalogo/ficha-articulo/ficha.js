class FichaArticulo extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");

    }
    generateComponent(xid) {
        this.getTemplate((new App()).getUrlTemplate("ficha-articulo"), html => {
            document.getElementById("app-container").innerHTML = html;
            let objtituloArt = document.querySelector(".cuerpo-ficha h3");
            console.log(objtituloArt);

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

