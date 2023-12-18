

function openSolapa(evt, id, numSolapa) {
	var i, x, btnSolapa;
	clase = "solapa" + id;
	var x = document.getElementsByClassName(clase);
	for (i = 0; i < x.length; i++) {
		x[i].style.display = "none";
	}
	
	boton = "boton-solapa" + id;
	btnSolapa = document.getElementsByClassName(boton);
	for (i = 0; i < x.length; i++) {
		btnSolapa[i].className = btnSolapa[i].className.replace("solapa-activa", "");
	}
	document.getElementById(id + numSolapa).style.display = "block";
	evt.currentTarget.className += " solapa-activa";
}