<?php
//funciones de integracion entre SC3 y calendar en /terceros/cal

include("funcionesSConsola.php");

define("CAL_SECURITY_BIT", 1);

// load required files
include('terceros/cal/config.php');
require('terceros/cal/sql_layer.php');
require('terceros/cal/gatekeeper.php');
require('terceros/cal/functions.php');

$CAL_USERS_PASSWORDS = "4321";

// Make the database connection.
$cal_db = new cal_database(CAL_SQL_HOST, CAL_SQL_USER, CAL_SQL_PASSWD, CAL_SQL_DATABASE, false);
if (!$cal_db->db_connect_id)
	die("Failed to connect to database...");

//agrega usuario (si no existe)
$data = array();
$data['username'] = $_SESSION["login"];

$pass = md5($CAL_USERS_PASSWORDS . CAL_SQL_PASSWD_SALT);
$data['password'] = $pass;

cal_query_add_user($data);

//recupera id de usuario para setear permisos
$rs = cal_query_getuser($data['username']);
$t = $cal_db->sql_fetchrow($rs);
$id = $t["id"];

cal_query_change_pass($pass, $id);

$pdata = array();
$pdata['write'] = "y";
$pdata['read'] = "y";
$pdata['edit'] = "y";
// get user's options concerning other's events
$pdata['editothers'] = "y";
//$pdata['editpast'] = "n";
$pdata['readothers'] = "y";
// get user's options about reminders
$pdata['remind_set'] = "y";
$pdata['remind_get'] = "y";
// get user's options for thir account.
$pdata['needapproval'] = "y";
$pdata['admin'] = "y";
//$pdata['disabled'] = "n";
// run the query
$r = cal_query_set_permissions($pdata, $id);
?>
<!doctype html>
<html lang="es">

<head>

	<title> SC3 - Derivando al calendar</title>

	<?php
	include("include-head.php");
	?>

</head>

<body onload="document.getElementById('form1').submit();">

	<div class="info-update">

		Derivando a <img src="images/citanova.png" width="16" height="16" border="0" /> <strong>Calendar SC3</strong> con usuario <?php echo ($_SESSION["login"]); ?> ...

		<form id="form1" name="form1" method="post" action="terceros/cal/index.php">

			<input type="hidden" name="user" value="<?php echo ($_SESSION["login"]); ?>" />
			<input type="hidden" name="pass" value="<?php echo ($CAL_USERS_PASSWORDS); ?>" />

		</form>
	</div>
</body>

</html>