/**
 * https://www.w3schools.com/howto/howto_js_autocomplete.asp
 * Fecha: dic-2021
 * Autor Marcos C.
 * www.sc3.com.ar
 * Similar a la del selector, pero usa aOperaciones como arreglo global de operaciones
 */


function startMenuDesplegable(xidcontrol, xArr) {

	inp = document.getElementById(xidcontrol);

	var currentFocus;

	//evento cuando se tipea en el buscador
	inp.addEventListener("input", function (e) {

		var a, b, i, val = this.value;
		/*close any already open lists of autocompleted values*/
		closeAllLists();
		if (!val) {
			return false;
		}
		currentFocus = -1;

		/*create a DIV element that will contain the items (values):*/
		a = domCreateDIV(this.id + "autocomplete-list", "autocomplete-items opacity95", "");

		/*append the DIV element as a child of the autocomplete container:*/
		this.parentNode.appendChild(a);
		/*for each item in the array...*/
		for (i = 0; i < aOperaciones.length; i++) {

			/*check if the item starts with the same letters as the text field value:*/
			var valorActual = aOperaciones[i][0];
			var ayuda = aOperaciones[i][4];

			if (valorActual.toUpperCase().includes(val.toUpperCase())) {
				/*create a DIV element for each matching element:*/
				b = document.createElement("DIV");
				/*make the matching letters bold:*/
				var comienza = valorActual.toUpperCase().indexOf(val.toUpperCase())
				var icono = '<img src="' + aOperaciones[i][1] + '">';

				//ilumina el tramo que coincide
				b.innerHTML = htmlSpan("autocomplete-code", icono) + " " + valorActual.substr(0, comienza);
				b.innerHTML += "<strong>" + valorActual.substr(comienza, val.length) + "</strong>";
				b.innerHTML += valorActual.substr(comienza + val.length);
				if (ayuda != '')
					b.innerHTML += '<br><small>' + ayuda + '</small>';

				//guara valor visible, url y target
				b.innerHTML += "<input type='hidden' value='" + valorActual + "'>";
				b.innerHTML += "<input type='hidden' value='" + aOperaciones[i][2] + "'>";
				b.innerHTML += "<input type='hidden' value='" + aOperaciones[i][3] + "'>";

				/* el click abre el url o tambien es invocado desde el ENTER */
				b.addEventListener("click", function (e) {

					inp.value = this.getElementsByTagName("input")[0].value;

					url = this.getElementsByTagName("input")[1].value;
					target = this.getElementsByTagName("input")[2].value;
					if (target == 'contenido')
						window.parent.document.getElementById('contenido').src = url;
					else
						openInNewTab(url);

					/*close the list of autocompleted values*/
					closeAllLists();

				});
				a.appendChild(b);
			}
		}
	});

	/*execute a function presses a key on the keyboard:*/
	inp.addEventListener("keydown", function (e) {
		var x = document.getElementById(this.id + "autocomplete-list");
		if (x) 
			x = x.getElementsByTagName("div");

		if (e.keyCode == 40) {
			/*If the arrow DOWN key is pressed,
			increase the currentFocus variable:*/
			currentFocus++;
			/*and and make the current item more visible:*/
			addActive(x);
		}
		else if (e.keyCode == 38) { //up
			/*If the arrow UP key is pressed,
			decrease the currentFocus variable:*/
			currentFocus--;
			/*and and make the current item more visible:*/
			addActive(x);
		}
		else if (e.keyCode == 13) {
			/*If the ENTER key is pressed, prevent the form from being submitted,*/
			e.preventDefault();
			if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) 
					x[currentFocus].click();
			}
		}
	});

	function addActive(x) {
		/*a function to classify an item as "active":*/
		if (!x) 
			return false;
		/*start by removing the "active" class on all items:*/
		removeActive(x);
		if (currentFocus >= x.length)
			currentFocus = 0;
		if (currentFocus < 0)
			currentFocus = (x.length - 1);
		/*add class "autocomplete-active":*/
		x[currentFocus].classList.add("autocomplete-active");
	}

	function removeActive(x) {
		/*a function to remove the "active" class from all autocomplete items:*/
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
		}
	}

	function closeAllLists(elmnt) {
		/*close all autocomplete lists in the document,
		except the one passed as an argument:*/
		var x = document.getElementsByClassName("autocomplete-items");
		for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}

	/*execute a function when someone clicks in the document:*/
	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}

