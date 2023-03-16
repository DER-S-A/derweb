class CarruselFooterComponent {
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
    generateComponent(xImagenes){ 
        let obj2ButtonPrev = document.createElement("button");
        obj2ButtonPrev.setAttribute("aria-label","Anterior");
        obj2ButtonPrev.classList.add("carousel__anterior");
        let obj2ButtonNext = document.createElement("button");
        obj2ButtonNext.classList.add("carousel__siguiente");
        obj2ButtonNext.setAttribute("aria-label","Siguiente");
        let objLeft3I = document.createElement("i");
        objLeft3I.classList.add("fas","fa-chevron-left");
        let objRight3I = document.createElement("i");
        objRight3I.classList.add("fas","fa-chevron-right");
        let objDiv2Carrusel = document.createElement("div");
        objDiv2Carrusel.classList.add("carousel__lista");
        this.objContainerCarrusel.appendChild(objDiv2Carrusel);
        ////////////////////////////////////////////////////////////
        obj2ButtonPrev.appendChild(objLeft3I);
        obj2ButtonNext.appendChild(objRight3I);
        ////////////////////////////////////////////////////////////
        xImagenes.forEach((array, i) => {
            var objDiv3CarruselElemento = document.createElement("div");
            objDiv3CarruselElemento.classList.add("carousel__elemento");
            var objImg = new Image();
            objImg.setAttribute("class","item");
            
            objImg.src = "../admin/ufiles/" + array.imagen;
    
            objDiv2Carrusel.appendChild(objDiv3CarruselElemento).appendChild(objImg);
        });

        this.objContainerCarrusel.appendChild(obj2ButtonPrev);
        this.objContainerCarrusel.appendChild(objDiv2Carrusel);
        this.objContainerCarrusel.appendChild(obj2ButtonNext);
    }
}

