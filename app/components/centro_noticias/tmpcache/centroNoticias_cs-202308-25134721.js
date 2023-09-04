class CtroNot extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    async generateComponent() {
        try {
            const novedades = await this.__getNovedades();
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
            new Api().call(url, "filter=publicado=1", "GET", novedades => {
                console.log(novedades)
                resolve(novedades)
            }, false, error => reject(error))
        })
    }
}