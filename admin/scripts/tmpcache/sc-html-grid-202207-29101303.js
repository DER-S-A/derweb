/**
 * Scripts de grilla, por SC3
 * Autor: Ezequiel A
 * Fecha: abril 2020
 */



function abrirCerrarGrupo(evt, clase, icono) {
	icon = document.getElementById(icono);
	fila = document.getElementsByClassName(clase);

	if (evt.currentTarget.classList.contains("grid_grupo_abierto")) {
		if (icon != null)
			icon.className = "fa fa-angle-double-down fa-lg";

		for (let i = 0, len = fila.length; i < len; i++) {
			if (i > 10)
				cerrarGrupo(fila[i]);
			else
				//las primeras celdas esperan un poco, efecto cortina
				setTimeout(function () { cerrarGrupo(fila[i]); }, i * 15);
		}

		evt.currentTarget.classList.remove("grid_grupo_abierto");
		evt.currentTarget.classList.add("grid_grupo_cerrado");
	}
	else {
		if (evt.currentTarget.classList.contains("grid_grupo_cerrado")) {
			if (icon != null)
				icon.className = "fa fa-angle-double-up fa-lg";

			for (let i = 0, len = fila.length; i < len; i++) {
				if (i > 10)
					setTimeout(function () { abrirGrupo(fila[i]); }, 150);
				else
					//las primeras celdas esperan un poco, efecto cortina
					setTimeout(function () { abrirGrupo(fila[i]); }, i * 15);
			}

			evt.currentTarget.classList.remove("grid_grupo_cerrado");
			evt.currentTarget.classList.add("grid_grupo_abierto");
		}
	}
}


function cerrarGrupo(elemento) {
	elemento.style["display"] = "none"
}

/**
 * Muestra la fila y analiza si es un subgrupo para decirle que está abierto
 * @param {tr} elemento 
 */
function abrirGrupo(elemento) {
	elemento.style["display"] = "";

	//analiza si estoy haciendo visible la cabecera de un subgrupo
	if (elemento.classList.contains("grid_grupo_cerrado")) {
		elemento.classList.remove("grid_grupo_cerrado");
		elemento.classList.add("grid_grupo_abierto");
	}
}


//-----------------------Funciones de suma-excel en grillas ---------------------------------------------------

var suma = 0.0;
var sumando = 0;
var sumObjetos = new Array(100);

function sumSetSumado(xobj) {
	xobj.bgColor = "#99FF99";
	sumObjetos[sumando] = xobj;
}


function sumSetNoSumado(xobj) {
	xobj.bgColor = "";
}

function sumEsNumero(xvalor) {
	if (isNaN(parseFloat(xvalor)))
		return false;
	return true;
}

function sumSetStatus(input) {
	if (document.getElementById('sumtotal') != null) {
		document.getElementById('sumtotal').innerHTML = input;
	}
}

/**
 * Vuelve todo a cero
 */
function sumResetStatus() {

	if (document.getElementById('sumtotal') != null) {
		document.getElementById('sumtotal').innerHTML = "";
		document.getElementById('sumtotal').classList.add("oculto");
	}
}


/**
 * Comienza a sumar
 * @param {event} e 
 * @param {*} obj 
 * @returns 
 */
function sumStart(e, obj) {

	var evt = window.event || e;
	if (evt.ctrlKey) {

		//muestra div con la suma
		document.getElementById('sumtotal').classList.remove("oculto");

		valorCelda = (obj.innerHTML);
		valorCelda = valorCelda.replace(",", "");
		valorCelda = valorCelda.replace("<font color=\"red\">", "");
		valorCelda = valorCelda.replace("</font>", "");
		valorCelda = valorCelda.replace("<b>", "");
		valorCelda = valorCelda.replace("</b>", "");
		valorCelda = valorCelda.replace(",", "");
		valorCelda = valorCelda.replace(",", "");

		if (sumEsNumero(valorCelda))
			suma += (valorCelda) * 1.0;
		else {
			//intenta sumar valores de la forma: "$ 45.3" o "U$S 100.3"
			valoresCeldas = valorCelda.split(" ");
			if (sumEsNumero(valoresCeldas[1]))
				suma += valoresCeldas[1] * 1.0;
			else {
				return true;
			}
		}
		suma = Math.round(suma * 100) / 100;
		sumando++;
		statusText = "<i class=\"fa fa-calculator fa-lg\" title=\"Suma\"></i>";
		statusText += " = " + suma + " (" + sumando + ")";
		sumSetSumado(obj);
		sumSetStatus(statusText);
	}
	return true;
}

function sumEnd(e, obj) {
	sumResetStatus();
	suma = 0;
	while (sumando > 0) {
		//quita el color a todos los objetos sumados
		sumSetNoSumado(sumObjetos[sumando]);
		sumando--;
	}
}

//FIN: Funciones de suma-excel en grillas ------------------------------------------------------------------------------
