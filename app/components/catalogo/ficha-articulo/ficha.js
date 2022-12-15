class FichaArticulo extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");

        this.getTemplate((new App()).getUrlTemplate("ficha-articulo"), html => {
            document.getElementById("app-container").innerHTML = html;
        });
    }
    generateComponent() {
      alert("hola");
    }
}

