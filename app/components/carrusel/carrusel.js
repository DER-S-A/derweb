class CarruselComponent {
    /**
     * Constructor de clase
     * @param {string} xidContainer Id. del contenedor para el Carrusel.
     */
    constructor(xidContainer) {
        this.idContainer = xidContainer;
        this.objContainerCarrusel = document.getElementById(this.idContainer);
    }

    /**
     * Creo los div que necesito para el carrusel    
     */
    __generateComponent(xImagenes) {
        var objDiv2CarruselInner = document.createElement("div");
        objDiv2CarruselInner.classList.add("carousel-inner");
        
        var objbuttonPrevCarrusel = document.createElement("button");
        objbuttonPrevCarrusel.classList.add("carousel-control-prev");
        objbuttonPrevCarrusel.setAttribute("type","button");
        objbuttonPrevCarrusel.setAttribute("data-bs-target",'#' + this.idContainer);
        objbuttonPrevCarrusel.setAttribute( "data-bs-slide","prev");
        var objSpan1ButtonCarrusel = document.createElement("span");
        objSpan1ButtonCarrusel.classList.add("carousel-control-prev-icon");
        objSpan1ButtonCarrusel.setAttribute("aria-hidden","true");
        var objSpan2ButtonCarrusel = document.createElement("span");
        objSpan2ButtonCarrusel.classList.add("visually-hidden");
        
        var objbuttonNextCarrusel = document.createElement("button");
        objbuttonNextCarrusel.classList.add("carousel-control-next");
        objbuttonNextCarrusel.setAttribute("type","button");
        objbuttonNextCarrusel.setAttribute("data-bs-target",'#' + this.idContainer);
        objbuttonNextCarrusel.setAttribute( "data-bs-slide","next");
        var objSpan1ButtonNextCarrusel = document.createElement("span");
        objSpan1ButtonNextCarrusel.classList.add("carousel-control-next-icon");
        objSpan1ButtonNextCarrusel.setAttribute("aria-hidden","true");
        var objSpan2ButtonNextCarrusel = document.createElement("span");
        objSpan2ButtonNextCarrusel.classList.add("visually-hidden");
        
        objSpan2ButtonCarrusel.innerHTML = "Previous";
        objSpan2ButtonNextCarrusel.innerHTML = "Next";

        xImagenes.forEach((array, i) => {
            var objDiv3CarruselItem = document.createElement("div");
            var objImg = new Image();
            objImg.src = "../admin/ufiles/" + array.imagen;
        
            if (i == 1)
                objDiv3CarruselItem.classList.add("carousel-item", "active");
            else
                objDiv3CarruselItem.classList.add("carousel-item");
            
            objImg.setAttribute("class","d-block w-100");
            
            objDiv2CarruselInner.appendChild(objDiv3CarruselItem).appendChild(objImg);
        });

        objbuttonPrevCarrusel.appendChild(objSpan1ButtonCarrusel);
        objbuttonPrevCarrusel.appendChild(objSpan2ButtonCarrusel);
        objbuttonNextCarrusel.appendChild(objSpan1ButtonNextCarrusel);
        objbuttonNextCarrusel.appendChild(objSpan2ButtonNextCarrusel);
        
        this.objContainerCarrusel.appendChild(objDiv2CarruselInner);   
        this.objContainerCarrusel.appendChild(objbuttonPrevCarrusel);  
        this.objContainerCarrusel.appendChild(objbuttonNextCarrusel);    
    }

    /**
     * Genera el componente carrusel de banner-portada.
     */
    generarCarrusel() {
        const url_bannerPortada = app.getUrlApi("app-banner-portada");
        (new APIs()).call(url_bannerPortada, "", "GET", response => {
            this.__generateComponent(response, this.idContainer);
        });
    }
}