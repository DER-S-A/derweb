

function accordionToggle(obj, xarrowId) {
	/* Toggle between adding and removing the "active" class,
	to highlight the button that controls the panel */
	obj.classList.toggle("active");

	var spanObj = document.getElementById(xarrowId);

	/* Toggle between hiding and showing the active panel */
	var panel = obj.nextElementSibling;

	if (panel.classList.contains("sc3-accordion-panel-abierta")) {
		obj.classList.remove("active");
		panel.className = "sc3-accordion-panel";
		panel.style.maxHeight = null;
	}
	else {
		if (!obj.classList.contains("active")) {
			spanObj.innerHTML = '<i class="fa fa-angle-double-down fa-lg"></i>';
			panel.className = "sc3-accordion-panel";
		}
		else {
			spanObj.innerHTML = '<i class="fa fa-angle-double-up fa-lg"></i>';
		}

		if (panel.style.maxHeight) {
			panel.style.maxHeight = null;
		}
		else {
			panel.style.maxHeight = panel.scrollHeight + "px";
		}
	}
}

function scrollGradual(id) {
	let bloque = document.getElementById(id);
	//Cuando se clickea en el boton la clase pasa a ser "sc3-accordion active". Para que se aplique el scroll debe tener esa clase
	if (bloque.className != "sc3-accordion") {
		let yOffset = - 116;
		let y = bloque.getBoundingClientRect().top + window.pageYOffset + yOffset;
		setTimeout(function () {
			window.scrollTo({ top: y, behavior: 'smooth' });
		}, 225)
	}
}