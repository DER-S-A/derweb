class AvisoPago extends ComponentManager {
    constructor(){
        super();
    }
    generateComponent() {
        const nodoContainer = document.querySelector("#app-container");
        this.getTemplate(new App().getUrlTemplate("aviso-pago"), html => {
            let containerModal = this.crearElementDom("div", "dockModalAP");
            containerModal.innerHTML = html;
            nodoContainer.append(containerModal);
            const modal = new bootstrap.Modal(document.querySelector('#modalAvisoPago'));
            modal.show();
        })
    }
}