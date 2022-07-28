

function colorEnBlanco(idcombo, idinput)
{
    combo = document.getElementById(idcombo);
    picker = document.getElementById(idinput);
    combo.value = " "
}

function colorCambio(idcombo, idinput)
{
	combo = document.getElementById(idcombo);
	picker = document.getElementById(idinput);
	picker.value = combo.value;
}