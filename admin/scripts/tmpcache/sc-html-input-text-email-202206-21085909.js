
function validarCaracteres(id)
{
    input = document.getElementById(id);
    valor = input.value;
    if (valor == '')
        return true;

    pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{3})?(.[a-zA-Z]{2})$/;
    aInput = input.value.split(",");
    
    for (let i = 0; i < aInput.length; i++) 
    {
        mail = aInput[i].replace(/ /g, "");
        if (!mail.match(pattern))
            return false;
    }
    
    return true;
}

function validarMails(idInput)
{   
	input = document.getElementById(idInput);
	valor = input.value;
	if (valor == '')
	{
		input.classList.remove("invalido");
		input.classList.remove("valido");
		return;
	}

	if (validarCaracteres(idInput) == false)
	{
		input.classList.add("invalido");
		input.classList.remove("valido");
	}
	else
	{
		input.classList.remove("invalido");
		input.classList.add("valido");
	}

}