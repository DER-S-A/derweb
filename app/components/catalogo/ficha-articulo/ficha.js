class FichaArticulo extends ComponentManager {
    constructor () {
        super();

        this.clearContainer("app-container");

    }

    /**
     * Creo la pantalla ficha.
     * @param {int} xid_art Id. Articulo.  
     */

    generateComponent(xid_art, panelesOpciones) {
        let url = (new App()).getUrlApi("ficha-articulos");
        let id_cli = JSON.parse(sessionStorage.getItem("derweb_sesion")).id_cliente;
        let parametro = "id_articulo=" + xid_art + "&id_cliente=" + id_cli;  
        (new APIs()).call(url, parametro, "GET", (xdatos) => {
        console.log(xdatos);
            this.getTemplate((new App()).getUrlTemplate("ficha-articulo"), html => {
                let arrayConRubroYSub = this.extraerCodigoRubroYSub();
                html = this.completarTemplate(xdatos,html, xid_art, arrayConRubroYSub);
                html = this.checkedGrilla(html, panelesOpciones);
                document.getElementById("app-container").innerHTML = html;
                let objCodigosOriginales = document.querySelector("#ficha-codigos-originales");
                let objContenedorImgLogo = document.querySelector(".ficha .logo-marca");
                let objContenedorCarruselEquiva = document.querySelector("#carrusel-equivalencias .contenedor-carrusel-equivalencias");
                let objCarruselInner = document.querySelector("#carousel-ficha .carousel-inner");
                let objCarruselIndicador = document.querySelector("#carousel-ficha .carousel-indicators");
                let objPrecioLista = document.querySelector(".precio-lista");
                let objPrecioCosto = document.querySelector(".precio-costo");
                let objPrecioVenta = document.querySelector(".precio-venta");
                let objOpcionLista = document.querySelector("#ficha-opcion-lista");
                let objOpcionCosto = document.querySelector("#ficha-opcion-costo");
                let objOpcionVenta = document.querySelector("#ficha-opcion-venta");
                let objinput = document.querySelector("#txtcantidad_"+xid_art);
                
                
                console.log(panelesOpciones);

                this.mostrarPrecios(objPrecioLista, objPrecioCosto, objPrecioVenta, objOpcionLista, objOpcionCosto, objOpcionVenta);
                this.pintarFotosArticulosCarrusel(objCarruselInner, xdatos.fotos, objCarruselIndicador);
                this.cargarLogo(objContenedorImgLogo, xdatos);
                this.pintarCodigosOriginales(objCodigosOriginales, xdatos);
                this.pintarEquivalentes(xdatos.equivalencias, objContenedorCarruselEquiva);
                this.agregarAlCarritoConEnter(objinput, xid_art);
                
            });
        });

    }

    completarTemplate(xdatos,html, xid_art, arrayConRubroYSub) {
        html = this.setTemplateParameters(html, "id_art", xid_art);
        html = this.setTemplateParameters(html, "id_articulo", xid_art);
        html = this.setTemplateParameters(html, "descripcion", xdatos.informacion[0].Descripcion);
        html = this.setTemplateParameters(html, "precio_lista", xdatos.informacion[0].Precio_lista);
        html = this.setTemplateParameters(html, "precio_costo", xdatos.informacion[0].Precio_costo);
        html = this.setTemplateParameters(html, "precio_venta", xdatos.informacion[0].Precio_venta);
        html = this.setTemplateParameters(html, "codigo", xdatos.informacion[0].Codigo);
        html = this.setTemplateParameters(html, "informacion_general", xdatos.informacion[0].Informacion_general);
        html = this.setTemplateParameters(html, "datos_tecnicos", xdatos.informacion[0].Datos_tecnicos);
        html = this.setTemplateParameters(html, "diametros", xdatos.informacion[0].Diametro);
        html = this.setTemplateParameters(html, "unidades_de_venta", xdatos.informacion[0].Unidades_de_venta);
        html = this.setTemplateParameters(html, "rubroysub", arrayConRubroYSub);
        return html;
    }

    pintarCodigosOriginales(objCodigosOriginales, xdatos) {
        let cantidad = xdatos.codigos_originales.length
        for(let i=0; i<cantidad; i++) {
            let objSpan = document.createElement("span");
            if(i===0&&cantidad>1){
                objSpan.innerText = " " + xdatos.codigos_originales[i].codigo + " | ";
            } else {
                if(i===cantidad-1){
                    objSpan.innerText = xdatos.codigos_originales[i].codigo;
                } else{
                    objSpan.innerText = xdatos.codigos_originales[i].codigo + " | ";
                }
            }
            
            objCodigosOriginales.append(objSpan);
        }
    }

    cargarLogo(objContenedorImgLogo, xdatos) {
        let objImg = new Image();
        objImg.alt = "logo marca";
        objImg.src = "../admin/ufiles/" + xdatos.informacion[0].Logo;
        objContenedorImgLogo.append(objImg);
    }

    pintarEquivalentes(xEquivalencias, objContenedorCarruselEquiva) {
        let cantidad = xEquivalencias.length;        
        for(let i=0;i<cantidad;i++){
            let objDivCol = document.createElement("div");
            objDivCol.className = 'col-lg-2';
            let objDivCard = document.createElement("div");
            objDivCard.className = "card";
            let objDivLogo = document.createElement("div");
            objDivLogo.className = "logo-equi";
            let objImg = new Image();
            objImg.className = "card-img-top";
            objImg.alt = "logo";
            objImg.src=  "../admin/ufiles/" + xEquivalencias[i].Logo;
            let objH6 = document.createElement("h6");
            objH6.textContent = xEquivalencias[i].Codigo;
            objDivCard.appendChild(objDivLogo).appendChild(objImg);
            objDivCard.append(objH6);
            for(let x=0;x<3;x++) {
                let objDivPrecioEqui = document.createElement("div");
                let objDescrip = document.createElement("p");
                objDescrip.className = "descrip";
                let objImp = document.createElement("p");
                objImp.className = "importe";
                switch(x){
                    case 0: {
                        objDivPrecioEqui.className = "precio-lista-equi";
                        objDescrip.textContent = "PRECIO DE LISTA";
                        objImp.textContent = xEquivalencias[i].Precio_lista;

                    }
                    break;
                    case 1: {
                        objDivPrecioEqui.className = "precio-costo-equi";
                        objDescrip.textContent = "PRECIO DE COSTO";
                        objImp.textContent = xEquivalencias[i].Precio_costo;
                    }
                    break;
                    case 2: {
                        objDivPrecioEqui.className = "precio-venta-equi";
                        objDescrip.textContent = "PRECIO DE VENTA";
                        objImp.textContent = xEquivalencias[i].Precio_venta;
                    }
                }
                objDivPrecioEqui.append(objDescrip);
                objDivPrecioEqui.append(objImp);
                objDivCard.append(objDivPrecioEqui);
            }

            objDivCol.append(objDivCard);

            /*objDivCol.innerHTML = 
            `
                <div class="card">
                    <div class="logo-equi">
                        <img src="../admin/ufiles/${xEquivalencias[i].Logo}" alt="" class="card-img-top">
                    </div>
                    <h6>${xEquivalencias[i].Codigo}</h6>
                    <div class="precio-lista-equi">
                        <p class="descrip">PRECIO DE LISTA</p>
                        <p class="importe">${xEquivalencias[i].Precio_lista}</p>
                    </div>
                    <div class="precio-costo-equi">
                        <p class="descrip">PRECIO DE COSTO</p>
                        <p class="importe">${xEquivalencias[i].Precio_costo}</p>
                    </div>
                    <div class="precio-venta-equi">
                        <p class="descrip">PRECIO DE VENTA</p>
                        <p class="importe">${xEquivalencias[i].Precio_venta}</p>
                    </div>
                </div>
            `*/
            if(i===0) {
                var objCarrusel = document.createElement("div");
                objCarrusel.className = 'carousel-item active';
                var objRow = document.createElement("div");
                objRow.className = 'row contenedor-pagina';
                objRow.append(objDivCol);
                objCarrusel.append(objRow);
                objContenedorCarruselEquiva.append(objCarrusel);
                
            } else {
                if(i%5===0){
                    var objCarrusel = document.createElement("div");
                    objCarrusel.className = 'carousel-item';
                    var objRow = document.createElement("div");
                    objRow.className = 'row contenedor-pagina';
                    objRow.append(objDivCol);
                    objCarrusel.append(objRow);
                    objContenedorCarruselEquiva.append(objCarrusel);
                } else {
                    objRow.append(objDivCol);
                    objCarrusel.append(objRow);
                    objContenedorCarruselEquiva.append(objCarrusel);

                }
            }
        }
        
    }
    pintarFotosArticulosCarrusel(objCarruselInner, xdatos, objCarruselIndicador) {
        
        xdatos.forEach((foto,index)=>{
            let objBoton = document.createElement("button");
            objBoton.type = "button";
            objBoton.setAttribute("data-bs-target", "#carousel-ficha");
            objBoton.setAttribute("data-bs-slide-to", index);
            objBoton.setAttribute("aria-label", "Slide " + index+1);
            
            let objDivCarruselItem = document.createElement("div");
            let objImg = new Image();
            objImg.className = "d-block w-100";
            objImg.alt = "foto-producto";
            objImg.src = "../admin/ufiles/" + foto.archivo;
            if(index ===0){
                objBoton.className = "active";
                objBoton.setAttribute("aria-current", "true");
                objDivCarruselItem.className = "carousel-item active";
            } else objDivCarruselItem.className = "carousel-item";
            
            objCarruselIndicador.appendChild(objBoton);
            
            objCarruselInner.appendChild(objDivCarruselItem).appendChild(objImg);
        })
    }

    checkedGrilla(html, panelesOpciones) {
        if(panelesOpciones.precioLista) html = this.setTemplateParameters(html, "lista", 'checked=' + panelesOpciones.precioLista);
        if(panelesOpciones.precioCosto) html = this.setTemplateParameters(html, "costo", 'checked=' + panelesOpciones.precioCosto);
        if(panelesOpciones.precioVenta) html = this.setTemplateParameters(html, "venta", 'checked=' + panelesOpciones.precioVenta);
        
        return html;
    }

    mostrarPrecios(objPrecioLista, objPrecioCosto, objPrecioVenta, objOpcionLista, objOpcionCosto, objOpcionVenta) 
    {
        this.mostrarPrecioLista(objPrecioLista, objOpcionLista, objOpcionCosto, objOpcionVenta);
        this.mostrarPrecioCosto(objPrecioCosto, objOpcionCosto, objOpcionLista, objOpcionVenta);
        this.mostrarPrecioVenta(objPrecioVenta, objOpcionVenta, objOpcionLista, objOpcionCosto);
        
        
    }

    mostrarPrecioLista(objPrecioLista, objOpcionLista, objOpcionCosto, objOpcionVenta) {
        if(!objOpcionLista.checked){
            objPrecioLista.classList.toggle("ocultar-precio");
        }
        objOpcionLista.addEventListener("change", ()=>{            
            if(this.validarAlMenosUnoSeleccionado(objOpcionLista, objOpcionCosto, objOpcionVenta)){
                objPrecioLista.classList.toggle("ocultar-precio");
            } else {
                swal("Oops!", "Debe seleccionar otro campo antes de quitar este", "error");
                objOpcionLista.checked = "true";
            }
        });
    }

    mostrarPrecioCosto(objPrecioCosto, objOpcionCosto, objOpcionLista, objOpcionVenta) {
        if(!objOpcionCosto.checked){
            objPrecioCosto.classList.toggle("ocultar-precio");
        }
        objOpcionCosto.addEventListener("change", ()=>{   
            if(this.validarAlMenosUnoSeleccionado(objOpcionLista, objOpcionCosto, objOpcionVenta)){
                objPrecioCosto.classList.toggle("ocultar-precio");
            } else {
                swal("Oops!", "Debe seleccionar otro campo antes de quitar este", "error");
                objOpcionCosto.checked = "true";
            }
        });
    }

    mostrarPrecioVenta(objPrecioVenta, objOpcionVenta, objOpcionLista, objOpcionCosto) {
        if(!objOpcionVenta.checked){
            objPrecioVenta.classList.toggle("ocultar-precio");
        }
        objOpcionVenta.addEventListener("change", ()=>{  
            if(this.validarAlMenosUnoSeleccionado(objOpcionLista, objOpcionCosto, objOpcionVenta)){
                objPrecioVenta.classList.toggle("ocultar-precio");
            } else {
                swal("Oops!", "Debe seleccionar otro campo antes de quitar este", "error");
                objOpcionVenta.checked = "true";
            }         
        });
    }

    validarAlMenosUnoSeleccionado(objOpcionLista, objOpcionCosto, objOpcionVenta) {
        if(!objOpcionLista.checked&&!objOpcionCosto.checked&&!objOpcionVenta.checked){
            return false;
        } else return true;
    }

    agregarAlCarritoConEnter(objinput, xid_art) {
        objinput.addEventListener("keydown", (e)=>{
            if(e.keyCode === 13){
                agregarAlCarrito(xid_art);
            }
        })
    }
    extraerCodigoRubroYSub() {
        let xDato = sessionStorage.getItem("derweb_id_rubro_seleccionado");
        let xSubRubro = sessionStorage.getItem("derweb_id_subrubro_seleccionado");
        xDato = xDato + "," + xSubRubro;
        return xDato;
    }    
}

function agrandarFoto(){
    let objDivfoto = document.createElement("div");
    let objImg = new Image();
    objImg.src = "assets/imagenes/embrague.jpg"
    let objContainer = document.querySelector("#content-wrap");
    objDivfoto.className = "foto-grande-ficha";
    objDivfoto.append(objImg);
    objContainer.append(objDivfoto);
    objDivfoto.addEventListener("click", ()=>{
        objDivfoto.style.display = "none";
    })
}

