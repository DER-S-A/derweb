class CtroNot extends ComponentManager {
    constructor() {
        super();
    }

    async generateComponent() {
        try {
            const nodoContainer = document.querySelector("#app-container");
            this.getTemplate(new App().getUrlTemplate("centroNoticias"), html => {
                nodoContainer.innerHTML = html;
            })
        }
        catch {

        }
    }
}