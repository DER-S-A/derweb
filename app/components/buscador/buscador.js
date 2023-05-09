class Buscador extends ComponentManager {
    constructor(boxText, longitud, url, metodo = 'GET', session = '', paginado = -1) {
        super();
        this.boxText = boxText;
        this.filter = 'frase=';
        this.url = url;
        this.session = session;
        this.lengthMin = longitud;
        this.pagina = paginado;
        this.method = metodo;
        this.data;
    }

    async initComponent() {
        this.__validarLongitud();
        this.data = this.filter + this.boxText.value;
        this.pagina > -1 ? this.data += `&pagina=${this.pagina.toString()}` : null;
        this.session != '' ? this.data += `&sesion=${this.session}` : null;
        //console.log(this.url+ this.method+ '?'+this.data)
        try {
            const response = await this.__promesa(); 
            return response;
        } catch (error) {
            console.error(error);
            return null;
        }
    }

    __validarLongitud() {
        if(this.boxText.value < this.lengthMin) {
            swal('ERROR...!', 'Debes poner mas caracteres para que funcione.', 'error')
            .then( () => {
                return;
            })
        }
    }

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