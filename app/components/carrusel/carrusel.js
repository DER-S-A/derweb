class CarruselComponent {
    /**
     * Constructor de clase
     * @param {string} xidContainer Id. del contenedor para el Carrusel.
     */
    constructor(xidContainer) {
        this.objContainerCarrusel = document.getElementById(xidContainer);
    }

    /**
     * Creo los div que necesito para el carrusel    
     */
    generateComponent(xImagenes) {console.log(xImagenes)
        var objDiv2CarruselInner = document.createElement("div");
        objDiv2CarruselInner.classList.add("carousel-inner");
        
        xImagenes.forEach((array, i) => {
            var objDiv3CarruselItem = document.createElement("div");
            var objImg = new Image();
            objImg.src = "../admin/ufiles/" + array.imagen;
        
            if (i == 1) {
                objDiv3CarruselItem.classList.add("carousel-item", "active");
            } else {
                objDiv3CarruselItem.classList.add("carousel-item");
            }
            
            objImg.setAttribute("class","d-block w-100");
            
            objDiv2CarruselInner.appendChild(objDiv3CarruselItem).appendChild(objImg);
        });
        
        this.objContainerCarrusel.appendChild(objDiv2CarruselInner);        
    }
}