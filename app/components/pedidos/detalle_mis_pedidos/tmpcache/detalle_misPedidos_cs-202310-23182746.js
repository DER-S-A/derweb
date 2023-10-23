class Detalle_MisPedidos extends ComponentManager {
    constructor(idContainer) {
        super();
        this.nodoContainer = document.querySelector(idContainer);
    }

    generateComponent() {
        this.nodoContainer.innerHTML = "";

    }
}