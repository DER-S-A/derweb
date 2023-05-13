class Buscador extends ComponentManager {
    constructor(boxText, longitud, url, metodo = 'GET', session = '', paginado = -1) {
        super();
        this.boxText = boxText; //input q contiene la frase.
        this.filter = 'frase=';
        this.url = url;
        this.session = session;
        this.lengthMin = longitud;
        this.pagina = paginado;
        this.method = metodo;
        this.data;
    }

    /**
     * Inicio el componente, se usa con async ya que tiene una promesa adentro q resolver.
     */
    async initComponent() {
        this.__validarLongitud();
        this.data = this.filter + this.boxText.value;
        this.pagina > -1 ? this.data += `&pagina=${this.pagina.toString()}` : null;
        this.session != '' ? this.data += `&sesion=${this.session}` : null;
        try {
            const response = await this.__promesa(); 
            return response;
        } catch (error) {
            console.error(error);
            return null;
        }
    }

    /**
     * Valido la longitud minima que debe tener la frase a buscar.
     */
    __validarLongitud() {
        if(this.boxText.value.length < this.lengthMin) {
            swal('ERROR...!', 'Debes poner mas caracteres para que funcione.', 'error')
            .then( () => {
                return;
            })
        }
    }

    /**
     * Promesa que devuelve el resultado del CALL(fetch).
     */
    __promesa() {
        return new Promise((resolve, reject) => {
            new APIs().call(this.url, this.data, this.method, response => {
                resolve(response);
            }, error => {
                reject(error);
            });
        });
    }

}