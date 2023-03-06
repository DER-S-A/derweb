class Rentabilidad extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");
    }
    generateComponent(section) {
        this.getTemplate(new App().getUrlTemplate("rentabilidad"), html => {
            section.innerHTML = html;

            let url = new App().getUrlApi('catalogo-marcas-get');
            const marcaPromise = fetch(url, {method:'GET'}).then(response => response.json());
            url = new App().getUrlApi('catalogo-rubros-get');
            const rubroPromise = fetch(url, {method:'GET'}).then(response => response.json());
            url = new App().getUrlApi('catalogo-subrubros-get');
            const subrubroPromise = fetch(url, {method:'GET'}).then(response => response.json());
            
            // Esperar a que todas las promesas se resuelvan
            Promise.all([marcaPromise, rubroPromise, subrubroPromise])
            .then(([marcas, rubros, subrubros]) => {
                this.llenarBoxes(marcas, rubros, subrubros);
            })

        })
    }

    llenarBoxes(marcas, rubros, subrubros) {
        this.__llenarBox(marcas, '#container-rentabilidad #marcas');
        this.__llenarBox(rubros, '#container-rentabilidad #rubros');
        this.__llenarBox(subrubros, '#container-rentabilidad #subrubros');
    }

    __llenarBox(listado, query_selector) {
        const objSelector = document.querySelector(query_selector);
        listado.forEach(lista => {
            let objOption = document.createElement('option');
            objOption.value = lista.id;
            objOption.innerText = lista.descripcion;
            objSelector.append(objOption);
        })
    }

}

function generarRentabilidad() {
    const section = document.querySelector('#app-container');
    new Rentabilidad().generateComponent(section);
}