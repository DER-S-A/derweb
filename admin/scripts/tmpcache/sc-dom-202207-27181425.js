
/**
 * Funciones de DOM, del tipo document.createElement()
 */

/**
 * Setea id, className y innerHTML si no son vacios
 * @param {Element} xelement 
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 */
function domSetIdClassInner(xelement, xid = "", xclass = "", xinnerHtml = "")
{
	if (xid != "")
		xelement.id = xid;
	if (xclass != "")
		xelement.className = xclass;
	if (xinnerHtml != "")
		xelement.innerHTML = xinnerHtml;

	return xelement;
}

/**
 * Dame un obj <table>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateTABLE(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("table");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Dame un obj <tbody>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateTBODY(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("tbody");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Dame un obj <thead>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateTHEAD(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("thead");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Dame un obj <tr>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateTR(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("tr");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Dame un obj <td>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateTD(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("td");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}	

/**
 * Dame un obj <th>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateTH(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("th");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Dame un obj <span>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateSPAN(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("span");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Dame un obj <div>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @returns Element
 */
function domCreateDIV(xid = "", xclass = "", xinnerHtml = "")
{
	obj = document.createElement("div");
	return domSetIdClassInner(obj, xid, xclass, xinnerHtml);
}

/**
 * Crea un <a></a>
 * @param {string} xid 
 * @param {string} xclass 
 * @param {string} xinnerHtml 
 * @param {string} xhref 
 * @returns Element
 */
function domCreateA(xid = "", xclass = "", xinnerHtml = "", xhref = "")
{
	obj = document.createElement("a");
	obj = domSetIdClassInner(obj, xid, xclass, xinnerHtml);
	obj.href = xhref;
	return obj;
}

// --------- CONTROLES --------------------------------------------------------------


/**
 * Crea un INPUT TEXT
 * @param {string} xid 
 * @param {string} xvalue 
 * @param {string} xclass 
 * @returns Element
 */
function domCreateInputTEXT(xid = "", xclass = "", xvalue = "")
{
	obj = document.createElement("input");
	obj.type = "text";
	obj.id = xid;
	obj.className = xclass;
	obj.value = xvalue;
	return obj;
}

/**
 * Crea un INPUT ckeckbox
 * @param {string} xid 
 * @param {string} xvalue 
 * @param {string} xclass 
 * @param {boolean} xchecked 
 * @returns Element
 */
function domCreateInputCHECKBOX(xid = "", xclass = "", xvalue = "", xchecked = false)
{
	obj = document.createElement("input");
	obj.type = "checkbox";
	obj.id = xid;
	obj.className = xclass;
	obj.value = xvalue;
	obj.checked = xchecked;
	return obj;
}
