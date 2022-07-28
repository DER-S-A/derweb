/**
 * Funciones de SC3 para validar CBU
 * www.sc3-app.com.ar
 * Tomado de: https://gist.github.com/delucas/4526176
**/


function cbuValidarLargo(cbu) 
{
	if (cbu.length != 22)
		return false;
    return true;
}

/**
 * Valida bloque inicial de 8 dígitos que incluye dígito verificador
 * @param {string} codigo 
 */
function cbuValidarCodigoBanco(codigo) 
{
	if (codigo.length != 8) 
		return false;

	var banco = codigo.substr(0, 3)
	var digitoVerificador1 = codigo[3];
	var sucursal = codigo.substr(4, 3);
	var digitoVerificador2 = codigo[7];

	var suma = banco[0] * 7 + banco[1] * 1 + banco[2] * 3 + digitoVerificador1 * 9 + sucursal[0] * 7 + sucursal[1] * 1 + sucursal[2] * 3;
	var diferencia = (10 - (suma % 10)) % 10;

	return diferencia == digitoVerificador2;
}

/**
 * Valida bloque de 14 nros que incluyen digito verificador
 * @param {string} cuenta 
 */
function cbuValidarCuenta(cuenta) 
{
	if (cuenta.length != 14) 
		return false;
	var digitoVerificador = cuenta[13];
	var suma = cuenta[0] * 3 + cuenta[1] * 9 + cuenta[2] * 7 + cuenta[3] * 1 + cuenta[4] * 3 + cuenta[5] * 9 + cuenta[6] * 7 + cuenta[7] * 1 + cuenta[8] * 3 + cuenta[9] * 9 + cuenta[10] * 7 + cuenta[11] * 1 + cuenta[12] * 3;
	var diferencia = 10 - (suma % 10);
	return diferencia == digitoVerificador;
}

function cbuValidar(idInput) 
{
	cbu = document.getElementById(idInput).value;
	// caso especial de banco que agrega dos al cbu 
	if (cbu.length == 24)
		cbu = cbu.substr(2, 22);
	if (cbu.length == 23)
		cbu = cbu.substr(1, 22);
		
	input = document.getElementById(idInput);
	valido = cbuValidarLargo(cbu) && cbuValidarCodigoBanco(cbu.substr(0, 8)) && cbuValidarCuenta(cbu.substr(8, 14));

	if (cbu.length == 0)
	{
		input.classList.remove("valido");
		input.classList.remove("invalido");
		return;
	}

	if (valido == false)
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

function cbuValidarCargado(idInput)
{
	document.addEventListener("load", cbuValidar(idInput));
}
