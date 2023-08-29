class CtroNot extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    async generateComponent() {
        try {
            const novedades =  this.__getNovedades();
            this.getTemplate(new App().getUrlTemplate("centroNoticias"), html => {
                this.nodoContainer.innerHTML = html;
                const main = document.querySelector(".main-miperfil");
                main.style.marginTop = "20%";
                //console.log(novedades);
            });
        }
        catch {

        }
    }
    __getNovedades() {
        return new Promise((resolve, reject) => {
            const url = new App().getUrlApi("novedades");
            new APIs().call(url, "publicado=1", "GET", novedades => {
                console.log(url + "/get?filter=publicado=1")
                resolve(novedades)
            }, false, error => reject(error))
        })
    }
}