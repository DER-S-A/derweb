<?php
/*
	
	Copyright (c) Reece Pegues
	sitetheory.com

    Reece PHP Calendar is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
	any later version if you wish.

    You should have received a copy of the GNU General Public License
    along with this file; if not, write to the Free Software
    Foundation Inc, 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/


/*

	This file defines the phrases and words used throughout the program.
	It is seperated into 2 main sections:
		1) general words and errors used throughout the program
		2) words/phrases/errors/confirmations used by specifiec sections
	
	To add new languages, simply translate this file and place it into the "languages" folder.
	Once there, it will be an option in the admin menu for you to choose.
	Please note though that the file extension *must* be "php"

	If you do translate this file, please email it to me at:  reece.pegues@gmail.com
	Also, please post a link to it on the project forum at sourceforge so others can use it!

*/








/*
	THIS STARTS THE SECTION THAT LISTS THE COMMON WORDS AND ERRORS
	USED BY THE ENTIRE PROGRAM
*/

########## QUERY ERRORS ###########
define("CAL_QUERY_GETEVENT_ERROR", "Database Error: Failed fetching event by ID");
define("CAL_QUERY_SETEVENT_ERROR", "Database Error: Failed to Set Event Data");
########## SUBMENU ITEMS ###########
define("CAL_SUBM_LOGOUT", "Log Out");
define("CAL_SUBM_LOGIN", "Log In");
define("CAL_SUBM_ADMINPAGE", "Administrador");
define("CAL_SUBM_SEARCH", "Buscar");
define("CAL_SUBM_BACK_CALENDAR", "Volver al calendario");
define("CAL_SUBM_VIEW_TODAY", "Ver los eventos de hoy");
define("CAL_SUBM_ADD", "Agregar evento hoy");
########## NAVIGATION MENU ITEMS ##########
define("CAL_MENU_BACK_CALENDAR", "Volver al Calendario");
define("CAL_MENU_NEWEVENT", "Nuevo Evento");
define("CAL_MENU_BACK_EVENTS", "Ir a Eventos");
define("CAL_MENU_GO", "Ir");
define("CAL_MENU_TODAY", "Hoy");
########## USER PERMISSION ERRORS ##########
define("CAL_NO_READ_PERMISSION", "Ud. no tiene permisos para ver el evento.");
define("CAL_NO_WRITE_PERMISSION", "Ud. no tiene permisos para agregar o editar eventos.");
define("CAL_NO_EDITOTHERS_PERMISSION", "Ud. no tiene permisos para editar los eventos de otro usuario.");
define("CAL_NO_EDITPAST_PERMISSION", "Ud. no tiene permisos para agregar o editar eventos del pasado.");
define("CAL_NO_ACCOUNTS", "This calendar does not allow accounts; only root can log on.");
define("CAL_NO_MODIFY", "no se puede modificar");
define("CAL_NO_ANYTHING", "You don't have permission to do anything on this page");
define("CAL_NO_WRITE", "Ud. no tiene permisos para crear nuevos eventos");
############ DAYS ############
define("CAL_MONDAY", "Lunes");
define("CAL_TUESDAY", "Martes");
define("CAL_WEDNESDAY", "Miércoles");
define("CAL_THURSDAY", "Jueves");
define("CAL_FRIDAY", "Viernes");
define("CAL_SATURDAY", "Sábado");
define("CAL_SUNDAY", "Domingo");
############ MONTHS ############
define("CAL_JANUARY", "Enero");
define("CAL_FEBRUARY", "Febrero");
define("CAL_MARCH", "Marzo");
define("CAL_APRIL", "Abril");
define("CAL_MAY", "Mayo");
define("CAL_JUNE", "Junio");
define("CAL_JULY", "Julio");
define("CAL_AUGUST", "Agosto");
define("CAL_SEPTEMBER", "Septiembre");
define("CAL_OCTOBER", "Octubre");
define("CAL_NOVEMBER", "Noviembre");
define("CAL_DECEMBER", "Diciembre");






/*  
	THIS STARTS THE SECTION THAT LISTS THE WORDS/PHRASES/ERRORS/CONFIRMATIONS
	USED BY SINGLE SECTIONS, OR ONLY A FEW SECTONS.

	IF USED BY MULTIPLE SECTIONS, IT'S A IN A GROUP SPECIFICALLY FOR PHRASES USED BY MULTIPLE SECTIONS
	AND WILL TELL YOU THE SECTIONS IN A COMMENT AFTER THE DEFININITION
*/

// ADMINISTRATOR SECTION RELATED TEXT (admin.php)
define("CAL_ADMIN_TAB_GENERAL", "Opciones Generales");
define("CAL_ADMIN_TAB_EDITUSERS", "Editar Usuarios");
define("CAL_ADMIN_TAB_ADDUSER", "Nuevo Usuario");
define("CAL_ADMIN_TAB_TYPES", "Tipos de Eventos");
define("CAL_CONFIRM_DELETE_EVENTTYPE", "Está seguro de borrar este tipo de evento ?");
define("CAL_CONFIRM_DELETE_EVENTTYPE_EXTRA", "Todos los eventos de este tipo perderan su tipo de evento");
define("CAL_CONFIRM_DELETEUSER", "Está seguro de borrar este usuario ?");
define("CAL_ADMIN_INVALID_DATA", "Los datos son inválidos. La operación se cancelar !");
define("CAL_ADMIN_EVENTTYPE_NAME", "Tipo de Evento");
define("CAL_ADMIN_EVENTTYPE_COLOR", "Color del evento");
define("CAL_ADMIN_EVENTTYPE_DESC", "Descripcion del tipo de evento");
define("CAL_ADMIN_EDIT_EVENTTYPES", "Tipos de eventos actuales");
define("CAL_ADMIN_ADD_EVENTTYPE", "Agregar nuevo tipo de evento");
define("CAL_ADMIN_EDIT_EVENTTYPE", "Modificar tipo de evento");
define("CAL_ADMIN_ENTER_PASSWORD_AGAIN", "Reingresar clave");
define("CAL_ADMIN_ENTER_PASSWORD", "Ingrese nueva clave");
define("CAL_ADMIN_RESET_ROOT_PASSWORD", "Resetear clave root");
define("CAL_ADMIN_SETTINGS_SUCCESS", "The options were set successfully.<br>(skin and language changes take effect next page load)");
define("CAL_ADMIN_SETTINGS_FAILED", "SQL Error - Failed to update the calendar options");
define("CAL_ADMIN_PASSWORD_SUCCESS", "The Password was set successfully");
define("CAL_ADMIN_PASSWORD_NOMATCH", "The passwords you entered did not match");
define("CAL_ADMIN_PASSWORD_LENGTH", "Password Invalid: passwords must be at least 6 characters");
define("CAL_ADMIN_PASSWORD_FAILED", "SQL Error - Failed to update the user's password");
define("CAL_ADMIN_USER_UPDATE_SUCCESS", "El usuario fué actualizado con éxito");
define("CAL_ADMIN_USER_ADD_SUCCESS", "El usuario fué agregado con éxito");
define("CAL_ADMIN_USER_DEL_SUCCESS", "El usuario fué borrado con éxito");
define("CAL_ADMIN_USER_DEL_FAILED", "SQL Error: Deleting the user Failed");
define("CAL_ADMIN_TYPE_UPDATE_SUCCESS", "El tipo de evento fué actualizado con éxito");
define("CAL_ADMIN_TYPE_UPDATE_FAILED", "Updating the Event Type Failed");
define("CAL_ADMIN_TYPE_DEL_SUCCESS", "The Event Type was deleted successfully");
define("CAL_ADMIN_TYPE_DEL_FAILED", "SQL Error when trying to delete event type");
define("CAL_ADMIN_TYPE_ADD_SUCCESS", "The Event Type was added successfully");
define("CAL_ADMIN_TYPE_ADD_FAILED", "Adding the Event Type Failed");
define("CAL_ADMIN_TYPE_COLOR_ERROR", "The color provided was invalid. It must be 6 digit HEX");
define("CAL_ADMIN_TYPE_GET_FAILED", "SQL Error - Failed to get Event Types");
define("CAL_ADMIN_ROOT_RESET_SUCCESS", "Root Password Was Successfully Set");
define("CAL_ADMIN_USERNAME_EXISTS", "El nombre de usuario ya existe");
define("CAL_ADMIN_USERNAME_INVALID", "The Username Must Contain only the following:<br>letters, numbers, underscores, dashes, periods, and the @ symbol");
define("CAL_ADMIN_USERNAME_LENGTH", "Usuario inválido: las claves deben ser entre 3 y 30 caracteres");
define("CAL_ADMIN_USERNAME_FAILED", "DB Error: Failed to add user account");
define("CAL_ADMIN_SETPERMISSIONS_FAILED", "SQL Error - Failed to set user permissions");
define("CAL_ADMIN_GETUSERS_FAILED", "Error recuperando usuarios de la base de datos");
define("CAL_ADMIN_CHANGE_PASSWORD", "Cambiar clave");
define("CAL_ADMIN_DELETE_USER", "Borrar usuario");
define("CAL_ADMIN_ADMINISTRATOR", "Administrador");
define("CAL_ADMIN_DISABLE_ACCOUNT", "Deshabilitar cuenta");
define("CAL_ADMIN_VIEW_OWN_EVENTS", "Ver eventos propios");
define("CAL_ADMIN_ADD_EVENTS", "Agregar eventos");
define("CAL_ADMIN_EDIT_OWN_EVENTS", "Editar eventos propios");
define("CAL_ADMIN_EDIT_OTHERS", "Editar otros");
define("CAL_ADMIN_EDIT_PAST", "Editar del pasado");
define("CAL_ADMIN_VIEW_OTHERS", "Ver otros");
define("CAL_ADMIN_SET_OPTIONS", "Setear opciones");
define("CAL_ADMIN_CREATE_USER", "Crear usuario");
define("CAL_ADMIN_SKIN_INSTRUCT", 'Which skin would you like<br>to use as the default?');
define("CAL_ADMIN_LANG_INSTRUCT", "What language would you like<br>to use as the default?");
define("CAL_ADMIN_TIMES_INSTRUCT", 'Do you want to show the Starting Time with<br>the event subject on the main calendar page?');
define("CAL_ADMIN_CLOCK_INSTRUCT", 'Do you want to use a 12 hour or 24 hour clock?');
define("CAL_ADMIN_STARTDAY_INSTRUCT", 'Do you want the Calendar weeks <br>to start with Monday or Sunday?');
define("CAL_ADMIN_ALIAS_INSTRUCT", 'Do you want to let Anonymous Users specify<br>an alias for the name feild when adding an event?');
define("CAL_ADMIN_ENTER_NEWPASS", "Enter the user's new password");
define("CAL_ADMIN_REENTER_NEWPASS", "Re-enter the new password to confirm");
define("CAL_ADMIN_SUBMIT_OPTIONS", "Aceptar opciones");
define("CAL_ADMIN_RESET_OPTIONS", "Undo Changes");
define("CAL_ADMIN_SUBMIT_ROOTPASS", "Submit New Root Password");
define("CAL_ADMIN_SUBMIT_EVENTTYPE", "Aceptar");
define("CAL_ADMIN_COLORSELECTOR", "Selector");
define("CAL_ADMIN_NO_SKINS", "NO hay hojas de estilos");
define("CAL_ADMIN_NO_LANGS", "Sin lenguajes definidos");
define("CAL_ADMIN_HOUR_CLOCK", "hour clock");
define("CAL_ADMIN_YES", "Si");
define("CAL_ADMIN_NO", "No");
define("CAL_ADMIN_PERMISSIONS_EXPLAIN", "
			Administrator - This gives a user full access to the admin section.<br>
			Disable Account - This does not allow the user to log in<br>
			View Own Events - This allows the user to view the events they created<br>
			Add Events - This allows the user to create new events<br>
			Edit Own Events - This allows the user to edit events they created<br>
			Edit Others - Allows the user to edit events they did NOT create<br>
			Edit Past - Allows the user to edit events in the past (Other permissions set to disallow will override this)<br>
			View Others - Allows the user to view events they did not create.<br>
			<br>
			Note: For the anonymous user, allowing them to 'view own events' or 'edit own events' means they can 
			view and edit all events created by all anonymous users *and also the root user*.  This is because the 
			root user is not actually a user itself - it technically uses the user ID 0, which belonds to anonymous. 
			you should not post events as root - create a user and give them administrator permission for that!
			<br><br>
			");



// SEARCH SECTION RELATED TEXT (search.php)
define("CAL_SEARCH_TITLE", "Buscar eventos");
define("CAL_SUBJECT", "Tema");
define("CAL_SEARCH_NOTE", "Note: The search functionality is very basic at this time.<br>  If the event repeats, the *first* date the event takes place is the one used by the from/to date parameters.");
define("CAL_SEARCH_LIMIT_MESSAGE", "Limit of 200 rows was reached - Some results not displayed");
define("CAL_DESCENDING", "Descendente");
define("CAL_ASCENDING", "Ascendente");
define("CAL_BEST_MATCH", "Mejor resultado");
define("CAL_START_DATE", "Fecha de inicio");
define("CAL_PHRASE", "Frase");
define("CAL_SEARCH_FROM", "Desde");
define("CAL_SEARCH_TO", "Hasta");
define("CAL_SEARCH_ORDER", "Orden");
define("CAL_SEARCH_SORT_BY", "Ordenar por");
define("CAL_SEARCH", "Buscar");
define("CAL_SUBMIT", "Aceptar");
define("CAL_SEARCH_ERROR", "Error: SQL Error when running Search Query");

// SUBMITTING/EDITING EVENT SECTION TEXT (event.php)
define("CAL_MORE_TIME_OPTIONS", "Mas opciones de tiempo");
define("CAL_REPEAT", "Repetir");
define("CAL_EVERY", "Cada");
define("CAL_REPEAT_FOREVER", "Repetir para siempre");
define("CAL_REPEAT_UNTIL", "Repetir hasta");
define("CAL_TIMES", "Veces");
define("CAL_HOLIDAY_EXPLAIN", "This will make the Event Repeat on the");
define("CAL_DURING", "Durante");
define("CAL_EVERY_YEAR", "Cada año");
define("CAL_HOLIDAY_EXTRAOPTION", "Or, since this falls on the last week of the month, Check here to make the event fall on the LAST");
define("CAL_IN", "en");
define("CAL_PRIVATE_EVENT_EXPLAIN", "otros usuarios no lo verán");
define("CAL_SUBJECT", "Tema");
define("CAL_SUBMIT_ITEM", "Aceptar item");
define("CAL_MINUTES", "Minutos");
define("CAL_TIME_AND_DURATION", "Tiempo y duración");
define("CAL_REPEATING_EVENT", "Repetición");
define("CAL_EXTRA_OPTIONS", "Opciones extra");
define("CAL_ONLY_TODAY", "Sólo hoy");
define("CAL_DAILY_EVENT", "diariamente");
define("CAL_WEEKLY_EVENT", "semanalmente");
define("CAL_MONTHLY_EVENT", "mensualmente");
define("CAL_YEARLY_EVENT", "anualmente");
define("CAL_HOLIDAY_EVENT", "cada vacaciones");
define("CAL_UNKNOWN_TIME", "fecha de inicio desconocida");
define("CAL_ADDING_TO", "Creando evento el");
define("CAL_ANON_ALIAS", "Alias Name");
define("CAL_EVENT_TYPE", "Tipo de evento");

// SUBMIT EVENT PROCESSOR (checks data, inserts into DB) RELATED TEXT (eventsub.php)
define("CAL_MISSING_INFO", "Falta información... no puede continuar.");
define("CAL_DESCRIPTION_ERROR", "La descripción debe tener hasta 3000 caracteres.");
define("CAL_SUBJECT_ERROR", "El tema debe tener hasta 100 caracteres.");
define("CAL_EVENT_UPDATE_FAILED", "Imposible completar la actualización.");
define("CAL_EVENT_COUNT_ERROR", "La repetición debe ser entre 1 y 999");
define("CAL_REPEAT_EVERY_ERROR", "La repetición debe ser entre 1 y 9999");
define("CAL_ENDING_DATE_ERROR", "The Ending Date for the Repeating Options was not formatted correctly");
define("CAL_DURATION_ERROR", "Ingrese la duración");

// VIEW EVENT SECTION RELATED TEXT (viewevent.php)
define("CAL_POSTED_BY", "Agregado por");
define("CAL_DELETE_EVENT_CONFIRM", "Está seguro de borrar este evento ?");
define("CAL_STARTING_TIME", "Fecha de inicio");
define("CAL_MINUTES_SHORT", "Min");
define("CAL_HOUR", "Hora");
define("CAL_NO_EVENT_SELECTED", "No hay evento seleccionado.");
define("CAL_DOESNT_EXIST", "No existe el item");
define("CAL_LAST_MODIFIED_ON", "Modificado por última vez");
define("CAL_BY", "por");

// VIEW DATA SECTION RELATED TEXT (viewdate.php)
define("CAL_OPTIONS", "Opciones");

// CALENDAR SECTION RELATED TEXT (calendar.php)
//    (none - all in multi-section text area)

// LOGIN SCREEN RELATED TEXT (login.php)
define("CAL_INVALID_LOGIN", "Usuario incorrecto.");
define("CAL_LOGIN_TITLE", "Login");
define("CAL_USERNAME", "Usuario");
define("CAL_PASSWORD", "Clave");
define("CAL_LOGIN", "Login");
define("CAL_ACCOUNT_DISABLED", "This Account Has Been Disabled");

// DELETE EVENT SECTION RELATED TEXT (delete.php)
define("CAL_DELETE_EVENT_FAILED", "No se puede borrar el evento.");

// MULTI-SECTION RELATED TEXT (used by more than one section, but not everwhere)
define("CAL_DESCRIPTION", "Descripcion"); // (search, view date, view event)
define("CAL_DURATION", "Duracion"); // (view event, view date)
define("CAL_DATE", "Fecha"); // (search, view date)
define("CAL_NO_EVENTS_FOUND", "No se encontraron eventos"); // (search, view date)
define("CAL_NO_SUBJECT", "sin tema"); // (search, view event, view date, calendar)
define("CAL_PRIVATE_EVENT", "Evento privado"); // (search, view event)
define("CAL_DELETE", "Borrar"); // (view event, view date, admin)
define("CAL_MODIFY", "Editar"); // (view event, view date, admin)
define("CAL_NOT_SPECIFIED", "-"); // (view event, view date, calendar)
define("CAL_FULL_DAY", "Todo el día"); // (view event, view date, calendar, submit event)
define("CAL_HACKING_ATTEMPT", "Hacking Attempt - IP address logged"); // (delete)
define("CAL_TIME", "Hora"); // (view date, submit event)
define("CAL_HOURS", "Horas"); // (view event, submit event)
define("CAL_ANONYMOUS", "Anónimo"); // (view event, view date, submit event)
