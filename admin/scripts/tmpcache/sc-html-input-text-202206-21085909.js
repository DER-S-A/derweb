let text = null;
let icoMicro = null;

let rec = null;


function iniciarVoz(idText) {

	if (!("webkitSpeechRecognition" in window)) {
		alert("Reconocimiento de voz no disponible en su navegador.");
	}
	else {
		rec = new webkitSpeechRecognition();
		rec.lang = "es-AR";
		rec.continuous = true;
		rec.interimResults = true;
		
		idMicro = idText + "iconoMicrofono";
		icoMicro = document.getElementById(idMicro);
		text = document.getElementById(idText);

		if (!icoMicro.classList.contains("icono-microfono-encendido")) {

			//desactiva los microfonos encendidos
			let activados = document.getElementsByClassName('icono-microfono-encendido');
			for (let i = 0; i < activados.length; i++) {
				let micro = activados[i];
				micro.classList.remove("icono-microfono-encendido");
			}

			rec.start();
			text.value = text.value + " ";
			icoMicro.classList.add("icono-microfono-encendido");
			console.log("Microfono encendido", idText);
		}
		else {
			rec.abort();
			icoMicro.classList.remove("icono-microfono-encendido");
			//console.log("Microfono apagado", idText);
		}

		rec.onresult = (event) => {
				for (let i = event.resultIndex; i < event.results.length; i++) {
					if (event.results[i]["isFinal"]) {
						text.value += event.results[i][0].transcript;
					}
				}
			}
	}
}