

/**
 * Retorna si un CUIT/CUIL es válido
 * @param {} sCUIT 
 */
function validaCuit(sCUIT)
{    
	var aMult = '5432765432';
	var aMult = aMult.split('');

	if (sCUIT && sCUIT.length == 11)
	{
		aCUIT = sCUIT.split('');
		var iResult = 0;

		for(i = 0; i <= 9; i++)
		{
			iResult += aCUIT[i] * aMult[i];
		}

		iResult = (iResult % 11);
		iResult = 11 - iResult;
		
		if (iResult == 11) iResult = 0;
		if (iResult == 10) iResult = 9;

		if (iResult == aCUIT[10])
		{
			return true;
		}
	}    
	return false;
}

/**
 * Pinta el CUIT como valido o inválido
 * @param {string} idInput 
 */
function cuitVerificar(idInput)
{    
	var sCUIT = document.getElementById(idInput).value;
	sCUIT = sCUIT.replace("-", "");
	document.getElementById(idInput).value = sCUIT;
	
	var input = document.getElementById(idInput);
	if (sCUIT.length == 0)
	{
		input.classList.remove("valido");
		input.classList.remove("invalido");
		return;
	}

	if (!validaCuit(sCUIT))
	{
		input.classList.remove("valido");
		input.classList.add("invalido");
	}
	else
	{
		input.classList.remove("invalido");
		input.classList.add("valido");
	}
}

/**
 * Sólo nros, sin guines ni letras
 * @param {string} string 
 */
function cuitRestringirLetra(string)
{
	var out = '';
	//Caracteres validos
	var filtro = '1234567890';

	//Recorrer el texto y verificar si el caracter se encuentra en la lista de validos 
	for (var i=0; i < string.length; i++)
	{
		if (filtro.indexOf(string.charAt(i)) != -1) 
			//Se añaden a la salida los caracteres validos
			out += string.charAt(i);
	}
	//Retornar valor filtrado
	return out;
}


function cuitValidarCargado(idInput)
{
	document.addEventListener("load", cuitVerificar(idInput));
}