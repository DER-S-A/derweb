/**
 * Desarrollado por SC3, funciones de generacion de HTML
 * Fecha: may-2018
 * Autor: Marcos C.
 */


function htmlP(xtxt) {
	return '<p>' + xtxt + '</p>';
}

function htmlB(xtxt) {
	return '<b>' + xtxt + '</b>';
}

function htmlSmall(xtxt) {
	return '<small>' + xtxt + '</small>';
}

function htmlBR() {
	return '<br>';
}

function htmlH(xnro, xtxt) {
	return '<h' + xnro + '>' + xtxt + '</h' + xnro + '>';
}

function htmlHClass(xnro, xclass, xtxt) {
	return '<h' + xnro + ' class=\'' + xclass + '\'>' + xtxt + '</h' + xnro + '>';
}

function htmlI(xclass, xtxt) {
	return '<i class=\'' + xclass + '\'>' + xtxt + '</i>';
}

function htmlHref(xlink, xtxt, xtarget) {
	return '<a href=\'' + xlink + '\' target=\'' + xtarget + '\'>' + xtxt + '</a>';
}

function htmlImgFa(xclass, xalt) {
	return "<i title=\"" + xalt + "\" class=\"fa " + xclass + "\"></i>";
}

function htmlDiv(xclass, xtxt) {
	return '<div class=\'' + xclass + '\'>' + xtxt + '</div>';
}

function htmlSpan(xclass, xtxt) {
	return '<span class=\'' + xclass + '\'>' + xtxt + '</span>';
}

function htmlDivId(xid, xclass, xdisplay, xtxt) {
	return '<div id=\'' + xid + '\' style="display:' + xdisplay + '" class=\'' + xclass + '\'>' + xtxt + '</div>';
}

function htmlHidden(xid, xvalue) {
	return '<input type="hidden" id="' + xid + '" value="' + xvalue + '">';
}


function htmlAddTableHeader(xtbl, xtitulo, xclass = "") {
	var header = xtbl.createTHead();
	if (header.rows.length == 0)
		var row = header.insertRow(0);
	else
		var row = header.rows[0];

	cell = document.createElement("th");
	cell.innerHTML = xtitulo;
	if (xclass != "") {
		cell.className = xclass;
	}
	cell = row.appendChild(cell);
}


