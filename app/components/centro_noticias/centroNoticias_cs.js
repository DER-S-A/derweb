class CtroNot extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    async generateComponent() {
        try {
            const novedades =  await this.__getNovedades();
            const oferta = await this.__getOfertas();
            this.getTemplate(new App().getUrlTemplate("centroNoticias"), html => {
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "20%";
                console.log(novedades);
                this.__controlEventos(novedades, oferta);
                this.__generarCarruselNov(novedades);
                //this.__generarCarruselOff(oferta);
            });
        }
        catch(error) {
            console.error("Error en generateComponent:", error);
        }
    }

    /**
     * Trae la lista de centro de noticias filtrado por novedades, es una promesa que devuelve el resultado del CALL(fetch).
     */
    __getNovedades() {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("novedades");
            new APIs().call(url, "filter=publicado=1 AND es_oferta=0", "GET", novedades => {
                resolve(novedades)
            }, false, error => reject(error))
        });
    }

    /**
     * Trae la lista de centro de noticias filtrado por ofertas, es una promesa que devuelve el resultado del CALL(fetch).
     */
    __getOfertas() {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("novedades");
            new APIs().call(url, "filter=publicado=1 AND es_oferta=1", "GET", ofertas => {
                resolve(ofertas)
            }, false, error => reject(error))
        });
    }
    __generarCarruselNov(novedades) {
        const carruselInner = document.querySelector("#carousel-novedades .carousel-inner");
        const carouselIndicators = document.querySelector("#carousel-novedades .carousel-indicators");
        novedades.forEach((ima, i) => {
            let setAtri = ["type", "button", "data-bs-target", "#carousel-novedades", "data-bs-slide-to", i.toString(), "aria-label","Slide " + (i+1).toString()];
            const boton = this.crearElementDom("button", null, null, setAtri);

            const url = "../admin/ufiles/" + ima.imagen;
            let carruselItem = this.crearElementDom("div", "carousel-item");
            if(ima.url == "") {
                carruselItem.innerHTML = `<img src=${url} class="d-block w-100" alt="imagen novedades">`
            } else {
                carruselItem.innerHTML = `<a href=${ima.url} target="_blank"><img src=${url} class="d-block w-100" alt="imagen novedades"></a>`
            }

            if(i == 0) {
                boton.className = "active";
                boton.setAttribute("aria-current", "true");
                carruselItem.classList.add("active");
            }
            carouselIndicators.append(boton);
            carruselInner.append(carruselItem);
        });
    }

    __generarCarruselOff(ofertas) {
        const carruselInner = document.querySelector("#carousel-ofertas .carousel-inner");
        const carouselIndicators = document.querySelector("#carousel-ofertas .carousel-indicators");
        ofertas.forEach((ima, i) => {
            let setAtri = ["type", "button", "data-bs-target", "#carousel-ofertas", "data-bs-slide-to", i.toString(), "aria-label","Slide " + (i+1).toString()];
            const boton = this.crearElementDom("button", null, null, setAtri);

            const url = "../admin/ufiles/" + ima.imagen;
            let carruselItem = this.crearElementDom("div", "carousel-item");
            if(ima.url == "") {
                carruselItem.innerHTML = `<img src=${url} class="d-block w-100" alt="imagen ofertas">`
            } else {
                carruselItem.innerHTML = `<a href=${ima.url} target="_blank"><img src=${url} class="d-block w-100" alt="imagen ofertas"></a>`
            }
            

            if(i == 0) {
                boton.className = "active";
                boton.setAttribute("aria-current", "true");
                carruselItem.classList.add("active");
            }
            carouselIndicators.append(boton);
            carruselInner.append(carruselItem);
        });
    }

    /**
     * Tengo todos los eventos que usa el componente.
     */
    __controlEventos(novedades, oferta) {
        document.getElementById("novedades-tab").addEventListener("click", () => {
            const carruselInner = document.querySelector("#carousel-ofertas .carousel-inner");
            const carouselIndicators = document.querySelector("#carousel-ofertas .carousel-indicators");
            carruselInner.innerHTML = "";
            carouselIndicators.innerHTML = "";
            this.__generarCarruselNov(novedades);
        });
        document.getElementById("ofertas-tab").addEventListener("click", () => {
            const carruselInner = document.querySelector("#carousel-novedades .carousel-inner");
            const carouselIndicators = document.querySelector("#carousel-novedades .carousel-indicators");
            carruselInner.innerHTML = "";
            carouselIndicators.innerHTML = "";
            this.__generarCarruselOff(oferta);
        });
    }
}