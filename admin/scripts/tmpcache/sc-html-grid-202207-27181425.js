/**
 * Scripts de grilla, por SC3
 * Autor: Ezequiel A
 * Fecha: abril 2020
 */



function abrirCerrarGrupo(evt, clase, icono)
{  
    icon = document.getElementById(icono);
    fila = document.getElementsByClassName(clase);
    
    if (evt.currentTarget.classList.contains("grid_grupo_abierto"))
    {
        if (icon != null)
            icon.className = "fa fa-angle-double-down fa-lg";
            
        for (let i = 0, len = fila.length; i < len; i++) 
        {  
            if (i > 10)
                cerrarGrupo(fila[i]);
            else
                //las primeras celdas esperan un poco, efecto cortina
                setTimeout(function() {cerrarGrupo(fila[i]);}, i * 15);
        }
    
        evt.currentTarget.classList.remove("grid_grupo_abierto");
        evt.currentTarget.classList.add("grid_grupo_cerrado");
    }
    else
    {
        if (evt.currentTarget.classList.contains("grid_grupo_cerrado"))
        {
            if (icon != null)
                icon.className = "fa fa-angle-double-up fa-lg";

            for (let i = 0, len = fila.length; i < len; i++)
            {
                if (i > 10)
                    setTimeout(function() {abrirGrupo(fila[i]);}, 150);
                else
                    //las primeras celdas esperan un poco, efecto cortina
                    setTimeout(function() {abrirGrupo(fila[i]);}, i * 15);
            }
            
            evt.currentTarget.classList.remove("grid_grupo_cerrado");
            evt.currentTarget.classList.add("grid_grupo_abierto");        
        }
    }
}


function cerrarGrupo(elemento)
{    
    elemento.style["display"] = "none"
}

/**
 * Muestra la fila y analiza si es un subgrupo para decirle que está abierto
 * @param {tr} elemento 
 */
function abrirGrupo(elemento)
{
    elemento.style["display"] = "";

    //analiza si estoy haciendo visible la cabecera de un subgrupo
    if (elemento.classList.contains("grid_grupo_cerrado"))
    {
        elemento.classList.remove("grid_grupo_cerrado");
        elemento.classList.add("grid_grupo_abierto");        
    }
}