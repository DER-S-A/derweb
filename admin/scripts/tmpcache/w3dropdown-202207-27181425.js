/**
 * Tomado de https://www.w3schools.com/howto/howto_js_dropdown.asp 
 */

/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function desplegarMenu(xid) 
{	
	console.log('menu', xid);
	openDropdown = document.getElementById(xid);
    if (openDropdown.classList.contains('show'))
    {
    	//no subir antes del IF ! porque le quita la clase "show" y cambia condicion
    	ocultarMenusTodos();
    }
	else
	{
    	//no subir antes del IF ! porque le quita la clase "show" y cambia condicion
		ocultarMenusTodos();

    	openDropdown.className = 'dropdown-content show';
		console.log('agregado', openDropdown.classList);
	}
}


function ocultarMenusTodos()
{
	var dropdowns = document.getElementsByClassName("dropdown-content");
	var i;
	for (i = 0; i < dropdowns.length; i++) 
	{
		var openDropdown = dropdowns[i];
		if (openDropdown.classList.contains('show')) 
		{
			openDropdown.classList.remove('show');
		}
	}
}


// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) 
{
	if (!event.target.matches('.dropbtn') && !event.target.matches('.dropdown-icon')) 
	{
		ocultarMenusTodos();
	}
} 



