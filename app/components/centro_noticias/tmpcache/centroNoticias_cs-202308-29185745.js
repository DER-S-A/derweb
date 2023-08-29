class CtroNot extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    async generateComponent() {
        try {
            const novedades =  await this.__getNovedades();
            this.getTemplate(new App().getUrlTemplate("centroNoticias"), html => {
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "20%";
                console.log(novedades);
                this.__generarCarrusel(novedades)
            });
        }
        catch {

        }
    }
    __getNovedades() {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("novedades");
            new APIs().call(url, "filter=publicado=1", "GET", novedades => {
                resolve(novedades)
            }, false, error => reject(error))
        })
    }
    __generarCarrusel(novedades) {
        const carruselInner = document.querySelector("#carousel-novedades .carousel-inner");
        const carouselIndicators = document.querySelector("#carousel-novedades .carousel-indicators");
        novedades.array.forEach((ima, i) => {
            let setAtri = ["type", "button", "data-bs-target", "#carousel-novedades", "data-bs-slide-to", i.toString(), "aria-label","Slide " + (i+1).toString()];
            const boton = this.crearElementDom("button", null, null, setAtri);

            const url = "../admin/ufiles/" + ima.imagen;
            let carruselItem = this.crearElementDom("div", "carousel-item");
            carruselItem.innerHTML = `<img src=${url} class="d-block w-100" alt="imagen novedades">`

            if(i == 0) {
                boton.className = "active";
                boton.setAttribute("aria-current", "true");
                carruselItem.classList.add("active");
            }
            carouselIndicators.append(boton);
            carruselInner.append(carruselItem);
        });
    }
}