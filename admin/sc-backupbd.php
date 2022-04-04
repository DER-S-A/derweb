<?php
include("config.php");
include("sc-zipfile.php");


function exportDatabase($host, $user, $pass, $name, $tables = array(), $backup_folder = false)
{
	$mysqli = new mysqli($host, $user, $pass, $name);
	$mysqli->select_db($name);
	$mysqli->query("SET NAMES 'utf8'");

	$queryTables    = $mysqli->query('SHOW TABLES');
	while ($row = $queryTables->fetch_row()) {
		$target_tables[] = $row[0];
	}

	if (count($tables) > 0) {
		$target_tables = array_intersect($target_tables, $tables);
	}
	$content = "";

	foreach ($target_tables as $table) {
		$result         =  $mysqli->query("SELECT * FROM $table limit 60000");
		$fields_amount  =  $result->field_count;
		$rows_num       =  $mysqli->affected_rows;
		$res            =  $mysqli->query('SHOW CREATE TABLE ' . $table);
		$TableMLine     =  $res->fetch_row();
		$content        =  $content . "\n\n" . $TableMLine[1] . ";\n\n";

		for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
			while ($row = $result->fetch_row()) {
				//when started (and every after 100 command cycle):
				if ($st_counter % 100 == 0 || $st_counter == 0) {
					$content .= "\nINSERT INTO " . $table . " VALUES";
				}

				$content .= "\n\t(";
				for ($j = 0; $j < $fields_amount; $j++) {
					$row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
					if (isset($row[$j])) {
						$content .= '"' . $row[$j] . '"';
					} else {
						$content .= '""';
					}
					if ($j < ($fields_amount - 1)) {
						$content .= ', ';
					}
				}

				$content .= ")";
				//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
				if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
					$content .= ";";
				} else {
					$content .= ", ";
				}
				$st_counter = $st_counter + 1;
			}
		}
		$content .= "\n\n\n";
	}

	//nro de semana del año, para no generar tantos archivos
	$filename = $backup_folder . $name . "_" . date("W") . ".sql";

	/*
	header('Content-Type: application/octet-stream');   
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"".$backup_folder."\"");  
	echo $content; exit;
	*/
	$handle = fopen($filename, "w+");
	fwrite($handle, $content);
	fclose($handle);
	echo ("Archivo creado: $filename con " . count($target_tables) . " tablas");

	//------- Arma ZIP con todo los archivos --------------------------------------------------------------------------
	//------  NO pone fecha para poder descargarlo
	$zipName = $backup_folder . $name . ".zip";
	$zipfile = new zipfile();
	$zipfile->add_file(implode("", file($filename)), basename($filename));
	$handle = fopen($zipName, "w");
	fwrite($handle, $zipfile->file());
	fclose($handle);
	echo (" - Creado: $zipName");
}

$tables = array();
exportDatabase($BD_SERVER, $BD_USER, $BD_PASSWORD, $BD_DATABASE, $tables, "backups/");
