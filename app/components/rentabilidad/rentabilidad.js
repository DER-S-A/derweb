class Rentabilidad extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");
    }
    generateComponent(section) {console.log(section);
        this.getTemplate(new App().getUrlTemplate("rentabilidad"), html => {
            section.innerHTML = html;
        })
    }

}

function generarRentabilidad() {
    const section = document.querySelector('#app-container');
    new Rentabilidad().generateComponent(section);
}