<?php
include("funcionesSConsola.php");
checkUsuarioLogueadoRoot();

$key = Request("key");
$key = md5($key);
$sql = Request("sql");

if (!sonIguales($key, "3d863b367aa379f71c7afc0c9cdca41d"))
	$sql = "select 1, 2 from other_table limit 10";

function tieneFormato()
{
	if (strcmp(Request("formato"), "html") == 0)
		return true;
	else
		return false;
}


if (tieneFormato())
	$separator = "</td><td>";
else
	$separator = Request("separator");

?>
<!doctype html>
<html lang="es">

<head>
	<title>SQL - por SC3</title>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

	<style>
		body {
			background-color: #616161 !important;
		}
	</style>


</head>

<body>
	<?php

	//Tratamiento del SQL
	$rsPpal = new BDObject();
	$sql = str_replace("\\", "", $sql);

	$sql = trim($sql);
	$sqlResto = $sql;
	while (strcmp($sqlResto, "") != 0) {
		if (strpos($sqlResto, ";") === false) {
			$sqlActual = $sqlResto;
			$sqlResto = "";
		} else {
			$sqlActual =  substr($sqlResto, 0, strpos($sqlResto, ";"));
			$sqlResto =  substr($sqlResto, strpos($sqlResto, ";"));
			if (strlen($sqlResto) == 1)
				$sqlResto = "";
			else
				$sqlResto =  substr($sqlResto, 1);
		}

		$rsPpal->execQuery2($sqlActual);
		if (strcmp(Request("sql"), "") != 0) {
			if ($rsPpal->EOF()) {
				$sqlIns = strtolower($sqlActual);
				if (($rsPpal->resultado()) && (strpos($sqlIns, "insert") !== false)) {
	?>
					<hr size=1>
					<font face=sans-serif size=2 color=DarkBlue>Resultado de la consulta <br><b>"<?php echo ($sqlActual) ?>"</b> <br> Insert exitoso (<?php echo ($rsPpal->cant()); ?> registros)</font>
					<hr size=1>
				<?php
				} else
			if (($rsPpal->resultado()) && (strpos($sqlIns, "delete") !== false)) {
				?>
					<hr size=1>
					<font face=sans-serif size=2 color=DarkBlue>Resultado de la consulta <br> <b>"<?php echo ($sqlActual) ?>"</b> <br> Delete exitoso (<?php echo ($rsPpal->cant()); ?> registros)</font>
					<hr size=1>
					<?php
				} else {
					if ($rsPpal->resultado()) {
					?>
						<hr size=1>
						<font face=sans-serif size=2 color=DarkBlue>Resultado de la consulta <br> <b>"<?php echo ($sqlActual); ?>"</b> <br> Exitosa</font>
						<hr size=1>
				<?php } else
						echo ("Resultado vacio");
				}
			} else {
				?>
				<hr size=1>

				<div class="w3-light-grey">
					Resultado de la consulta <br> <b>"<?php echo ($sqlActual); ?>"</b>
					<br> <b><?php echo ($rsPpal->cant()); ?></b> registros encontrados
				</div>

				<div class="w3-responsive div-grilla">

		<?php
				echo ("<table width='100%' class=\"sc3-grilla-datos w3-striped w3-hoverable\">\n");
				echo ("<thead><tr>");

				$i = 0;
				if (!tieneFormato())
					echo ("<textarea cols=120 rows=20>");

				while ($i < $rsPpal->cantF()) {
					if (tieneFormato())
						echo ("<th>" . $rsPpal->getFieldName($i) . "</th>");
					else
						echo ($rsPpal->getFieldName($i) . $separator);
					$i++;
				}

				if (tieneFormato())
					echo ("</tr></thead>");
				else
					echo ("\n");

				while (!$rsPpal->EOF()) {
					if (tieneFormato())
						echo ("\n<tr class=td_dato>");

					$i = 0;
					while ($i < $rsPpal->cantF()) {
						$valor = $rsPpal->getValue($i);
						if (tieneFormato())
							echo ("<td valign='top'>");
						if (esCampoFecha($rsPpal->getFieldType($i))) //tipo fecha
						{
							if (esVacio($valor))
								echo ("(null)");
							else {
								$Day = getdate(toTimestamp($rsPpal->getValue($i)));
								echo ($Day["mday"] . "-" . $Day["mon"] . "-" . $Day["year"] . " " . $Day["hours"] . ":" . $Day["minutes"]);
							}
						} else
							echo ($rsPpal->getValue($i));

						$i++;
						if (tieneFormato())
							echo ("</td>");
						else
							echo ($separator);
					}

					if (tieneFormato())
						echo ("</td></tr>");
					else
						echo ("\n");

					$rsPpal->Next();
				}

				if (!tieneFormato())
					echo ("</textarea>");
			}
		}
		if (tieneFormato()) {
			echo ("</table>");
			echo ("</div>");
		}
	} //while para varios sql			
		?>
</body>

</html>