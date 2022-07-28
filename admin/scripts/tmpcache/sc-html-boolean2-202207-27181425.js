/**
 * ASIGNA un valor al control boolean
 * @param xid
 * @param valor
 * @returns
 */
function sc3SetBoolean(xid, xvalor)
{
	//pone el inverso para que sc3CambiarBoolean() lo invirta y pinte acorde
	if (xvalor == 0)
		document.getElementById(xid).value = 1;
	else
		document.getElementById(xid).value = 0;
	
	sc3CambiarBoolean(xid, 'boton' + xid);
}


function sc3CambiarBoolean(xid, xbotonid)
{
    if (document.getElementById(xid).value == 0)
    {
        document.getElementById(xid).value = 1;
        document.getElementById(xbotonid).className = "fa fa-toggle-on fa-2x fa-fw w3-text-green";
        document.getElementById(xbotonid).title = "Si";
    }
    else
    {
        document.getElementById(xid).value = 0;
        document.getElementById(xbotonid).className = "fa fa fa-toggle-off fa-2x fa-fw w3-text-grey";
        document.getElementById(xbotonid).title = "No";
    }
}

/**
 * Clase equivalente a la del mismo nombre en PHP
 * @param {} xId 
 * @param {*} xvalue 
 * @param {*} xObj 
 */
function HtmlBoolean2 (xId, xvalue, xObj)
{
    this.id = xId;
    this.value = xvalue;
    this.obj = xObj;
    this.icono = "";
    this.title = "";
    this.jscript = "sc3CambiarBoolean('" + this.id + "', 'boton" + this.id + "')";
    
    this.toHtml = function()
    {
        
        if (this.value == "")
            this.value = 0;
        if (this.value == "1")
            this.value == 1;
        
        if (this.readOnly == 1)
        {
            this.jscript = ""
        }
        
        title = "No";

        if (this.value == 1)
        {
            this.icono = "fa fa-toggle-on  fa-2x fa-fw w3-text-green";
            this.title = "Si";
        }
        else
        {
            this.icono = "fa fa-toggle-off fa-2x fa-fw w3-text-grey";
        }

        var resultado = document.createElement("i");
        resultado.setAttribute("id", "boton" + this.id);
        resultado.setAttribute("class", this.icono);
        resultado.setAttribute("title", this.title);
        resultado.setAttribute("onclick", this.jscript);
        document.getElementById(this.obj).appendChild(resultado);

        var hidden = document.createElement("input");
        hidden.setAttribute("id", this.id);
        hidden.setAttribute("value", this.value);
        hidden.setAttribute("type", "hidden");
        document.getElementById(this.obj).appendChild(hidden);       
    }

}
