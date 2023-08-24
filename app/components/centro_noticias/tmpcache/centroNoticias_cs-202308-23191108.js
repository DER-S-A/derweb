class CtroNot extends ComponentManager {
    constructor(idContainer) {
        
        super();
        this.nodoContainer = document.querySelector(idContainer);
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