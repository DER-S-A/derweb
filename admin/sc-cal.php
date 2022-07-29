<?php
include_once("funcionesSConsola.php");

define("CAL_SECURITY_BIT", 1);

// load required files
require('terceros/cal/config.php');
require('terceros/cal/sql_layer.php');
require('terceros/cal/gatekeeper.php');
require('terceros/cal/functions.php');

$cal_db = 0;

/**
 * Determina si está instalado el calendar
 * Retorna un arreglo ["hay_calendar" => 0|1, "eventos_hoy" => 0|n];
 */
function calInicializar()
{
	global $cal_db;

	$aResult = ["hay_calendar" => 0, "eventos_hoy" => 0, "error" => ""];
	$bd = new BDObject();

	$calInstalada = getSession("cal-instalada");
	if (esVacio($calInstalada)) {

		//acá no pasa nada
		if (!$bd->existeTabla("cal_events")) {
			$bd->close();

			setSession("cal-instalada", 0);
			return $aResult;
		}
	}

	// Make the database connection.
	$cal_db = new cal_database(CAL_SQL_HOST, CAL_SQL_USER, CAL_SQL_PASSWD, CAL_SQL_DATABASE, false);
	if (!$cal_db->db_connect_id) {
		$aResult["error"] = "Error al conectar a base de datos " . CAL_SQL_HOST . " " . CAL_SQL_USER;
		$bd->close();
		return $aResult;
	}

	//guarda que si, así no vuelve a preguntar
	setSession("cal-instalada", 1);

	//estamos en contacto con BD y existe la tabla
	$aResult["hay_calendar"] = 1;

	//TODO: guardar en sesion la cantidad de eventos del dia ! revalidar al ir a la agenda

	//Ubica usuario
	$rs = cal_query_getuser(getCurrentUserLogin());
	$t = $cal_db->sql_fetchrow($rs);

	if (!isset($t["id"]))
		$t["id"] = 0;

	$_SESSION['cal_userid'] = $t["id"];
	$_SESSION['cal_user'] = getCurrentUser();
	cal_load_permissions($t["id"]);

	$hoy = getdate(time());

	//consulta eventos de hoy
	$result = cal_query_get_eventlist($hoy["mday"], $hoy["mon"], $hoy["year"]);

	if ($result == NULL) {
		$bd->close();
		return $aResult;
	}

	$cantEventos = $cal_db->sql_numrows($result);
	$aResult["eventos_hoy"] = $cantEventos;

	$bd->close();
	return $aResult;
}


/**
 * Avisa a todos los eventos del dia
 */
function calAvisarEventosDia()
{
	$idusuario = getCurrentUser();
	if (esVacio($idusuario))
		$idusuario = 0;

	$sqlAvisaTodos = "";
	$avisarTodos = RequestInt("todos");
	//si avisa a todos, son todos los del grupo USUARIOS AGENDA
	if ($avisarTodos == 1)
		$sqlAvisaTodos = " or id in (select gu.idusuario 
								from gen_grupos_usuarios gu
									inner join gen_grupos g on (g.id = gu.idgrupo)
								where g.descripcion = 'USUARIOS AGENDA')";

	$rsUsuarios = getRs("select id, nombre, telefono, email, login 
									from sc_usuarios
									where habilitado = 1 and
										(id = $idusuario $sqlAvisaTodos)");

	while (!$rsUsuarios->EOF()) {
		$idusuario = $rsUsuarios->getId();
		$login = $rsUsuarios->getValue("login");
		$email = $rsUsuarios->getValue("email");
		$cantEventos = 0;

		//analiza si hoy se envia el aviso de las citas del día
		$fechaHoy = Sc3FechaUtils::formatFecha3(false);
		$eventosHoy = getParameter("cal-eventos-hoy", "");
		if (!sonIguales(getParameter("cal-avisado-$idusuario", "nunca"), $fechaHoy)) {
			if ($avisarTodos == 1)
				echo (" analizando $idusuario...");
			saveParameter("cal-avisado-$idusuario", $fechaHoy);

			// Make the database connection.
			$cal_db = new cal_database(CAL_SQL_HOST, CAL_SQL_USER, CAL_SQL_PASSWD, CAL_SQL_DATABASE, false);
			if (!$cal_db->db_connect_id) //die("Failed to connect to database...");
				echo ("(error)...");

			//agrega usuario (si no existe)
			$data = array();
			$data['username'] = $login;

			$rs = cal_query_getuser($data['username']);
			$t = $cal_db->sql_fetchrow($rs);
			$_SESSION['cal_userid'] = $t["id"];

			$hoy = getdate(time());
			$result = cal_query_get_eventlist($hoy["mday"], $hoy["mon"], $hoy["year"]);
			$output = "<font face='verdana'><br>Eventos del dia <b>" . $hoy["mday"] . "/" . $hoy["mon"] . "/" . $hoy["year"] . "</b><hr />";
			$subjectEmail = "Agenda del dia " . $hoy["mday"] . "/" . $hoy["mon"] . "/" . $hoy["year"] . "";
			$hayEventos = false;

			if ($cal_db->sql_numrows($result) < 1 || $result == NULL) {
				echo ("sin eventos en su agenda (usuario: $idusuario). ");
				saveParameter("cal-eventos-hoy", "sin eventos en su agenda (usuario: $idusuario). ");
			} else {
				$hayEventos = true;
				$cantEventos = 0;
				while ($row = $cal_db->sql_fetchrow($result)) {
					// organize username, subject, and description
					$name = htmlspecialchars($row['username']);
					$subject = htmlspecialchars($row['subject']);
					$desc = htmlspecialchars($row['description']);

					// organize the event type color
					if ($row['typecolor'] != "")
						$tcolor = " background-color: #" . $row['typecolor'] . ";";
					else
						$tcolor = "";

					// organize other event options
					$private = $row['private'];
					$alias = $row['alias'];
					// organize event type
					$temp_time = $row['start_since_epoch'];
					if (!cal_option("hours_24"))
						$timeformat = 'g:i A';
					else
						$timeformat = 'G:i';
					$time = date($timeformat, $temp_time);

					// organize event duration
					$durtime = $row['end_since_epoch'] - $temp_time;
					$durmin = ($durtime / 60) % 60;     //minute per 60 seconds, 60 per hour
					$durhr  = ($durtime / 3600) % 24;   //hour per 3600 seconds, 24 per day

					$typeofevent = $row['eventtype'];
					if ($typeofevent == 2)
						$temp_dur = "";
					if ($typeofevent == 4)
						$temp_dur = "";
					else
						$temp_dur = "$durhr hours, $durmin min";

					// print anonymous alias if enabled to do so.
					if (cal_option("anon_naming") && $alias != "")
						$name = "<i>$alias</i>";

					// print event data that was organized above (for the current event in this loop anyways)
					if ($subject == "")
						$subject = "[" . CAL_NO_SUBJECT . "]";
					if (!$private || !cal_anon()) {
						$output .= "<br/><div style='$tcolor'><b>$subject</b></div><br />$time: $desc ($name)<hr>";
					}
					$cantEventos++;
				} // end while loop
			}
			$output .= "</font>";

			if ($avisarTodos == 0) {
				echo ("<b>$cantEventos</b> evento/s en su agenda de hoy.");
				saveParameter("cal-eventos-hoy", "<b>$cantEventos</b> evento/s en su agenda de hoy.");
			}

			if ($hayEventos)
				enviarEmail($email, $email, "", $subjectEmail, $output, "", true, true);

			if ($avisarTodos == 1)
				echo (" usuario $idusuario avisado con $cantEventos eventos");
		} else {
			if ($avisarTodos == 0)
				echo ($eventosHoy);
		}


		$rsUsuarios->Next();
	}
}
