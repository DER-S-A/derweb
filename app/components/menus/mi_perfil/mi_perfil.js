class MiPerfil extends ComponentManager {
    constructor (xidAppContainer) {
        super();
        this.idAppContainer = xidAppContainer;
    }

    /**
     * Permite generar el componente.
     */
     generateComponent() {
        console.log(this.idAppContainer)
        this.clearContainer(this.idAppContainer);
     }

}