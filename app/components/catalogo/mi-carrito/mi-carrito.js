/**
 * Esta clase permite desplegar el modal donde se va a mostrar la
 * grilla con el pedido que se encuentra abierto actualmente.
 */

class MiCarritoModalComponent extends ComponentManager {
    constructor (xidModal) {
        super();
        this.__idModal = xidModal;
        this.__objDivFondo = null;
        this.__objDivModal = null;
        this.__idBotonCerrar = "btn-" + this.__idModal;
    }

    /**
     * Permite generar el componente.
     */
    generateComponent() {
        var objContainer = document.createElement("div");

        objContainer.id = this.__idModal + "-contenido";

        this.__generarDivFondo();
        this.__generarDivModal();
        this.__generarHeader();
        this.__objDivModal.appendChild(objContainer);       
        this.__generarFooter();
        this.__objDivFondo.appendChild(this.__objDivModal);
        document.body.appendChild(this.__objDivFondo);

        // Agrego eventos.
        document.getElementById(this.__idBotonCerrar).addEventListener("click", () => {
            document.getElementById(this.__idModal + "_fondo").style.display = "none";
        });

        /*document.getElementById(this.__idModal + "_fondo").addEventListener("click", () => {
            document.getElementById(this.__idModal + "_fondo").style.display = "none";
        });*/
    }

    /**
     * Permite crear el div de fondo para que no se pueda
     * hacer click en otra parte del sistema hasta no cerrar el modal.
     */
    __generarDivFondo() {
        this.__objDivFondo = document.createElement("div");
        this.__objDivFondo.id = this.__idModal + "_fondo";
        this.__objDivFondo.classList.add("modal-div-fondo");
    }

    /**
     * Permite crear el div modal en donde se va a mostrar la grilla
     * de artículos.
     */
    __generarDivModal() {
        this.__objDivModal = document.createElement("div");
        this.__objDivModal.id = this.__idModal;
        this.__objDivModal.classList.add("modal");
    }

    /**
     * Permite generar el header del modal.
     */
    __generarHeader() {
        var objHeaderContainer = document.createElement("div");
        var objTituloContainer = document.createElement("div");
        var objBotonCerrarContainer = document.createElement("div");
        var objTitulo = document.createElement("h5");
        var objImgCarrito = new Image(50,50);
        objImgCarrito.src = "assets/imagenes/icons/carrito.png";
        var objBotonCerrar = document.createElement("button");

        // Contenedor del header
        objHeaderContainer.id = this.__idModal + "-header";
        objHeaderContainer.classList.add("modal-header");

        // Título
        objTituloContainer.id = this.__idModal + "-header-titulo-container"
        objTituloContainer.classList.add("modal-titulo-container")
        objTitulo.innerHTML = "Mi Carrito";
        objTituloContainer.appendChild(objImgCarrito);
        objTituloContainer.appendChild(objTitulo);

        // Contenedor del botón de cerrar.
        objBotonCerrarContainer.id = this.__idModal + "-boton-cerrar";
        objBotonCerrarContainer.classList.add("boton-cerrar-container");
        
        // Boton para cerrar el carrito.
        objBotonCerrar.id = this.__idBotonCerrar;
        objBotonCerrar.classList.add("modal-boton-cerrar");
        objBotonCerrar.innerHTML = "<i class=\"fa-solid fa-xmark\"></i>";
        objBotonCerrar.title = "Cerrar mi carrito";
        objBotonCerrarContainer.appendChild(objBotonCerrar);

        // Vinculación de divs
        objHeaderContainer.appendChild(objTituloContainer);
        objHeaderContainer.appendChild(objBotonCerrarContainer);
        this.__objDivModal.appendChild(objHeaderContainer);
    }

    /**
     * Genera el footer del modal mi carrito.
     */
    __generarFooter() {
        var objDivFooter = document.createElement("div");
        var objBotonFinalizarPedido = document.createElement("button");
        objDivFooter.id = this.__idModal + "_footer";
        objDivFooter.classList.add("row");
        objDivFooter.classList.add("modal-div-footer");
        
        objBotonFinalizarPedido.id = "btn-finalizar-pedido";
        objBotonFinalizarPedido.name = "btn-finalizar-pedido";
        objBotonFinalizarPedido.innerHTML = "<span>Finalizar Pedido</span>";
        objBotonFinalizarPedido.classList.add("btn");
        objBotonFinalizarPedido.classList.add("btn-primary");

        objDivFooter.appendChild(objBotonFinalizarPedido);
        this.__objDivModal.appendChild(objDivFooter);
    }

    open() {
        document.getElementById(this.__idModal + "_fondo").style.display = "block";       
    }
}
