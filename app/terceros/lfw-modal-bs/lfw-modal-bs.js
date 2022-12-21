/**
 * Clase: LFWModalBS
 * Descripción:
 *  Esta clase permite generar una ventana modal basado en bootstrap.
 */

class LFWModalBS {
    /**
     * Crea una ventana modal utilziando el framework bootstrpa.
     * @param {string} xidContainer Id. del contenedor <div> donde se escribirá el código HTML del modal.
     * @param {string} xid Id. que identifica al modal dentro del HTML
     * @param {string} xtitle Título del modal para mostrar en el header.
     * @param {mixed} xBodyContent Contenido del modal, puede ser un DOM o un HTML.
     * @param {string} xancho Ancho expresado en pixels. Ejemplo: "500px".
     * @param {string} xbuttonFooterTitle Título del botón del footer. Default null.
     * @param {callback} cb_clicCustomButton Define la función callback del evento click del botón personalizado.
     */
    constructor(xidContainer, xid, xtitle, xBodyContent = null, xancho = "500px", xbuttonFooterTitle = null, cb_clicCustomButton = null) {
        // Defino las propiedades privadas.
        this.__idModal = xid;
        this.__idBody = "body-" + this.__idModal;
        this.__idCustomButton = "btn-" + this.__idModal;

        this.__title = xtitle;
        this.__objModal = null;
        this.__objModalDialog = null;
        this.__objModalContent = null;
        this.__objModalHeader = null;
        this.__objModalBody = null;
        this.__objModalFooter = null;
        this.__bodyContent = xBodyContent;
        this.__objButtonFooter = null;
        this.__buttonFooterTitle = xbuttonFooterTitle;
        this.__idModalContainer = xidContainer;
        this.__ancho = xancho;
        
        this.__createModal();

        if (document.getElementById(this.__idCustomButton) !== null)
            document.getElementById(this.__idCustomButton).addEventListener("click", () => {
                cb_clicCustomButton();
                this.close();
            });
    }
    
    /**
      * Devuelve el Id. del modal
      * @returns {string}
      */
    getIdModal() {
        return this.__idModal;
    }

    /**
     * Retorna div correspondiente al cuerpo del modal.
     * @returns {DOM Element}
     */
    getModalBody() {
        return this.__objModalBody;
    }

    /**
     * Crea el modal en HTML y lo inserta en el body.
     */
    __createModal() {
        let objModal = document.getElementById(this.__idModal);
        let objContainer = document.getElementById(this.__idModalContainer);
        
        if (objModal !== null)
            objContainer.removeChild(document.getElementById(this.__idModal));

        this.__createMainContainer();
        this.__createModalDialog();
        this.__createModalContent();
        this.__createModalHeader();
        this.__createModalBody();
        this.__createModalFooter();
        objContainer.appendChild(this.__objModal);
    }

    /**
     * Crea el contenedor principal del modal.
     */
    __createMainContainer() {
        // Configuro el contenedor principal.
        this.__objModal = document.createElement("div");
        this.__objModal.id = this.__idModal;
        this.__objModal.classList.add("modal");
        this.__objModal.classList.add("fade");
        this.__objModal.tabIndex = -1;
        this.__objModal.setAttribute("aria-labellebdy", this.__idModal);
        this.__objModal.setAttribute("aria-hidden", "true");
    }

    /**
     * Crea el modal dialog
     */
    __createModalDialog() {
        this.__objModalDialog = document.createElement("div");
        this.__objModalDialog.classList.add("modal-dialog");

        if (this.__ancho !== 0)
            this.__objModalDialog.setAttribute("style", "width: " + this.__ancho);

        this.__objModal.appendChild(this.__objModalDialog);
    }

    /**
     * Crea el contenedor del modal.
     */
    __createModalContent() {
        this.__objModalContent = document.createElement("div");
        this.__objModalContent.classList.add("modal-content");
        this.__objModalContent.setAttribute("style", "width: " + this.__ancho);
        this.__objModalDialog.appendChild(this.__objModalContent);
    }

    /**
     * Crea la cabecera del modal.
     */
    __createModalHeader() {
        let objTitle = document.createElement("h5");
        let objCloseButton = document.createElement("button");

        objTitle.id = this.__idModal;
        objTitle.classList.add("modal-title");
        objTitle.innerHTML = this.__title;

        objCloseButton.type = "button";
        objCloseButton.classList.add("btn-close");
        objCloseButton.setAttribute("data-bs-dismiss", "modal");
        objCloseButton.setAttribute("aria-label", "Close");

        this.__objModalHeader = document.createElement("div");
        this.__objModalHeader.classList.add("modal-header");
        
        this.__objModalHeader.appendChild(objTitle);
        this.__objModalHeader.appendChild(objCloseButton);
        this.__objModalContent.appendChild(this.__objModalHeader);
    }
    
    /**
     * Crea el cuerpo del modal.
     */
    __createModalBody() {
        this.__objModalBody = document.createElement("div");
        this.__objModalBody.classList.add("modal-body");
        this.__objModalBody.id = this.__idModal + "-body";
        
        // Verifico si usa DOM o HTML
        if (this.__bodyContent !== null) {
            if (typeof(this.__bodyContent) === "object")
                this.__objModalBody.appendChild(this.__bodyContent);
            else
                this.__objModalBody.innerHTML = this.__bodyContent;
        }

        this.__objModalContent.appendChild(this.__objModalBody);
    }

    /**
     * Crea el pie del modal.
     */
    __createModalFooter() {
        this.__objModalFooter = document.createElement("div");
        this.__objModalFooter.classList.add("modal-footer");

        if (this.__buttonFooterTitle !== null)
            this.__createCustomButton();

        this.__createBottonFooter();
        this.__objModalContent.appendChild(this.__objModalFooter);
    }

    /**
     * Crea el botón cerrar del modal.
     */
    __createBottonFooter() {
        let objCloseButton = document.createElement("button");
        objCloseButton.id = this.__idModal + "_btnclose";
        objCloseButton.type = "button";
        objCloseButton.classList.add("btn");
        objCloseButton.classList.add("btn-secondary");
        objCloseButton.setAttribute("data-bs-dismiss", "modal");
        objCloseButton.innerHTML = "Cerrar";
        this.__objModalFooter.appendChild(objCloseButton);
    }

    /**
     * Crea el botón personalizado del modal.
     */
    __createCustomButton() {
        this.__objButtonFooter = document.createElement("div");
        this.__objButtonFooter.id = this.__idCustomButton;
        this.__objButtonFooter.classList.add("btn");
        this.__objButtonFooter.classList.add("btn-primary");
        this.__objButtonFooter.innerHTML = this.__buttonFooterTitle;
        this.__objModalFooter.appendChild(this.__objButtonFooter);
    }
    
    /**
     * Permite abrir la ventana modal.
     */
    open() {
        const objModal = new bootstrap.Modal("#" + this.__idModal, {
            keyboard: false
        });

        objModal.show();
    }

    /**
     * Permite cerrar la ventana modal.
     */
    close() {
        document.getElementById(this.__idModalContainer).removeChild(document.getElementById(this.__idModal));
        document.body.removeChild(document.getElementsByClassName("modal-backdrop")[0]);
    }
}