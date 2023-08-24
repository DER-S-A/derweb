class CtroNot extends ComponentManager {
    constructor(idContainer) {
        this.nodoContainer = document.querySelector(idContainer);
        super();
    }

    async generateComponent() {
        try {
            this.getTemplate(new App().getUrlTemplate("centroNoticias"), html => {
                this.nodoContainer.innerHTML = html;
            })
        }
        catch {

        }
    }
}