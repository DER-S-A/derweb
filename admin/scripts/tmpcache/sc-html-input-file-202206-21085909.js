/**
 * Manejo de la subida de archivos por FTP
 * Por SC3
 */

function upload_file(e, path, input, imagen) 
{
	var direccion = path;
	e.preventDefault();
	fileobj = e.dataTransfer.files[0];

	ajax_file_upload(fileobj, direccion, input, imagen);
}


function ajax_file_upload(file_obj, direccion, idinput, imagen) 
{
	input = document.getElementById(idinput);
	divImagen = document.getElementById(imagen);
	if (file_obj != undefined) 
	{
		var form_data = new FormData();
		form_data.append('path', direccion)                 
		form_data.append('file', file_obj);
		connUpload = getHTTPObject();
		// Preparando la función de respuesta
		connUpload.onreadystatechange = respuestaArchivo;
		// Realizando la petición HTTP con método POST
		connUpload.open('POST', 'sc-html-enviararchivo-ajax.php');
		connUpload.send(form_data);
	}
}


function respuestaArchivo() 
{
	if (connUpload.readyState == 4) 
	{
		archivo = connUpload.responseText;
		input.value = archivo;
		var img = document.createElement('img');

		nombreArchivo = '';
		aArchivo = archivo.split('/');
		if (aArchivo.size > 0)
			nombreArchivo = aArchivo[1];
		else
			nombreArchivo = aArchivo[1];

		divImagen.innerHTML = '';
		divImagen.appendChild(img);
		img.title = nombreArchivo;

		if (validateImage(nombreArchivo))
		{
			img.src = "ufiles/" + archivo;
			img.width = 60;
			img.height = 60;
		}
		else
		{
			var span = document.createElement('span');
			span.innerHTML = archivo;
			divImagen.appendChild(span);

			img.src = "images/file.gif";
			img.width = 20;
			img.height = 20;
		}
		finalizarProgressBar();
	}
}

var gProgress = null;
var gIntervalFn = null;

function comenzarProgressBar(xid) 
{
	var pb1 = document.getElementById('progressBar' + xid);
	var pb2 = document.getElementById('progressBarActual' + xid);
	gProgress = pb2;

	pb1.classList.remove("oculto");
	//arrancamos en 10%, quien sabe porqué
	var width = 10;
	pb2.style.width = width + '%';
	gIntervalFn = setInterval(function () 
						{
							if (width < 100)
							{
								width += 1;
								pb2.style.width = width + '%';
							}
							else
								width = 1;
						}, 
			100);
}


function finalizarProgressBar(xid) 
{
	if (gProgress != null)
		gProgress.style.width = '100%';
	gProgress = null;
	clearInterval(gIntervalFn);
}

function validateImage(xfile) 
{
	var allowedExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
	var fileExtension = xfile.split('.').pop().toLowerCase();

	for(var index in allowedExtension) 
	{
		if(fileExtension === allowedExtension[index]) 
		{
			return true; 
		}
	}

	return false;
}

function cargaLink(id)
{
	input = document.getElementById(id);
	input.type = "text";
	input.focus();
}