<?php


//Retorna si el campo es un string
function esCampoStr($xid)
{
	if ((strcmp($xid, "string") == 0) || ($xid == 253))
		return true;
	else
		return false;
}

/**
 * Retorna si el campo es un Memo
 * en mysql un blob o text
 */
function esCampoMemo($xid)
{
	if (sonIguales($xid, "blob") || (($xid >= 249) && ($xid <= 252)))
		return true;
	else
		return false;
}


//Retorna si el campo es un integer (2 small int y 8 bigint)
function esCampoInt($xid)
{
	if ((strcmp($xid, "int") == 0) || ($xid == 3) || ($xid == 2) || ($xid == 8))
		return true;
	else
		return false;
}


/**
 * Retorna si el campo es una fecha
 * En mysql timestamp, date o datetime
 * Recomendable datetime
 */
function esCampoFecha($xid)
{
	if ((strcmp($xid, "date") == 0) || (strcmp($xid, "datetime") == 0) || (strcmp($xid, "timestamp") == 0) ||
		($xid == 12) || ($xid == 7) || ($xid == 10))
		return true;
	else
		return false;
}

/**
 * Retorna si el campo es un boleano
 * en mysql TINYINT(3)
 */
function esCampoBoleano($xid)
{
	if ((strcmp($xid, "boolean") == 0)  || ($xid == 1))
		return true;
	else
		return false;
}

//Retorna si el campo es un DOUBLE
function esCampoFloat($xid)
{
	if (sonIguales($xid, "real") || ($xid == 246) || ($xid == 5) || ($xid == 4))
		return true;
	else
		return false;
}

function esCampoFoto($xfieldname)
{
	$strPos = strpos($xfieldname, "path");
	if ($strPos === false)
		return false;
	else {
		if (strpos($xfieldname, "path") == 0)
			return true;
		else
			return false;
	}
}


//objeto para manejar la coneccion y consultas a la bd
class BDObject
{
	var $link;
	var $bd;
	var $rsQuery;
	var $row;
	var $eof;
	var $soloAssoc = false;
	var $sucefull;
	var $sql;
	var $affectedRows = 0;

	function __construct()
	{
		global $BD_SERVER;
		global $BD_USER;
		global $BD_PASSWORD;
		global $BD_DATABASE;
		$conn = mysqli_connect($BD_SERVER, $BD_USER, $BD_PASSWORD, $BD_DATABASE) or
			goErrorDb("Error al mysqli_connect().", mysqli_errno($this->link), mysqli_error($this->link));
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$this->link = $conn;
		$this->rsQuery = "0";

		//vamos al mundo utf8
		//OJO: todos los campos retornan el doble de longitud
		mysqli_set_charset($this->link, "latin1");

		mysqli_query($this->link, "SET time_zone = '-3:00'");

		/*
		if (version_compare(PHP_VERSION, '7.0.0') >= 0)
			mysqli_set_charset($this->link, "utf8");
		*/
	}

	/**
	 * Nos vamos !
	 */
	function close()
	{
		if ($this->link)
			mysqli_close($this->link);
	}

	/**
	 * Escapa string con seguriadad
	 */
	function realEscape($xstr)
	{
		return mysqli_real_escape_string($this->link, $xstr);
	}

	function execQuery($strQuery, $xIgnoreEmpty = false, $xSoloAsoc = false, $xInjectUsuario = true)
	{
		$strQuery = str_replace("\'", "'", $strQuery);

		if ($xIgnoreEmpty && esVacio($strQuery))
			return;

		$this->soloAssoc = $xSoloAsoc;

		//inyecta valor de usuario
		if ($xInjectUsuario) {
			$idusuario = getCurrentUser();
			if (esVacio($idusuario))
				$idusuario = "0";
			$strQuery = str_replace(":IDUSUARIO", $idusuario, $strQuery);
		}

		debug("[SQL]: $strQuery");

		$strQuery = str_replace(":IDLOCALIDAD", getCurrentUserLocalidad(), $strQuery);
		$this->sql = $strQuery;

		$this->rsQuery =  mysqli_query($this->link, $strQuery) or goErrorDb($strQuery, mysqli_errno($this->link), mysqli_error($this->link));
		$this->sucefull = $this->rsQuery;

		$this->eof = true;
		if (!is_bool($this->rsQuery)) {
			if ($xSoloAsoc)
				$this->eof = !($this->row = mysqli_fetch_array($this->rsQuery, MYSQLI_ASSOC));
			else
				$this->eof = !($this->row = mysqli_fetch_array($this->rsQuery));
		}

		$this->affectedRows = mysqli_affected_rows($this->link);
	}

	/**
	 * Lo que se que haya ejecutado, hazlo nuevamente
	 */
	function requery()
	{
		if (!esVacio($this->sql)) {
			$this->rsQuery =  mysqli_query($this->link, $this->sql) or goErrorDb($this->sql, mysqli_errno($this->link), mysqli_error($this->link));
			$this->sucefull = $this->rsQuery;

			$this->eof = true;
			if (!is_bool($this->rsQuery)) {
				$this->eof = !($this->row = mysqli_fetch_array($this->rsQuery));
			}

			$this->affectedRows = mysqli_affected_rows($this->link);
		} else {
			$this->affectedRows = 0;
			$this->eof = true;
		}
	}

	function getCharsetName()
	{
		return mysqli_character_set_name($this->link);
	}

	/*
	Ejecuta todos los querys del array
	*/
	function execQuerysInArray($xaquerys, $xids)
	{
		foreach ($xaquerys as $sql) {
			$sql = str_replace(array_keys($xids), array_values($xids), $sql);
			$this->execQuery($sql, true);
		}
	}

	/**
	 * Ejecuta el SQL y asocia campos de ID según el ARRAY provisto
	 **/
	function execQuerysIdsInArray($xsql, $xids)
	{
		$sql = str_replace(array_keys($xids), array_values($xids), $xsql);
		$this->execQuery($sql);
	}

	/*
	Ejecuta el query y retorna el valor del ultimo autonumerico
	*/
	function execInsert($strQuery, $xids = "")
	{
		if (is_array($xids))
			$strQuery = str_replace(array_keys($xids), array_values($xids), $strQuery);
		$this->execQuery($strQuery);

		//$error = error_reporting(0);
		$id = mysqli_insert_id($this->link);
		//error_reporting($error);

		return $id;
	}


	function execQuery2($strQuery)
	{
		debug("[SQL-2]: " . $strQuery);

		$this->sql = $strQuery;
		$this->rsQuery =  mysqli_query($this->link, $strQuery) or die("\n<br>execQuery2(): Error al ejecutar consulta [$strQuery]: " . mysqli_errno($this->link) . ": " . mysqli_error($this->link));
		$this->sucefull = $this->rsQuery;
		$this->eof = true;
		if (!is_bool($this->rsQuery))
			$this->eof = !($this->row = mysqli_fetch_array($this->rsQuery));
	}

	/**
	 * Retorna el valor de la columna dada. Si no existe da vacio
	 * @param {string} $strCampo 
	 */
	function getValue($strCampo)
	{
		if (isset($this->row[$strCampo]))
			return $this->row[$strCampo];

		return "";
	}

	/**
	 * Retorna el valor del campo codificado con utf8_encode() para ser enviado v�a AJAX
	 * @param string $strCampo
	 * @return string
	 */
	function getValueUTF8($strCampo)
	{
		if (isset($this->row[$strCampo]))
			return utf8_encode($this->row[$strCampo]);

		return "";
	}

	function getValueInt($xField)
	{
		return (int) $this->getValue($xField);
	}

	function getId()
	{
		return $this->getValueInt("id");
	}

	function getValueFloat($xField, $xDecimales = 2)
	{
		return round((float) $this->getValue($xField), $xDecimales);
	}

	function getValueFechaFormateada($xField, $xshowDate = true, $xshowTime = false)
	{
		return Sc3FechaUtils::formatFechaFromRs($this->getValue($xField), $xshowTime, $xshowDate);
	}

	function getValueFechaToRs($xField, $xshowDate = true, $xshowTime = false)
	{
		return Sc3FechaUtils::formatFechaFromRsToRs($this->getValue($xField), $xshowTime, $xshowDate);
	}

	function getValueFechaAsArray($xField)
	{
		$valor = $this->getValue($xField);
		return getdate(toTimestamp($valor));
	}


	/**
	 * retorna la fecha en formato AAAA-MM-DD para poder compararla con otra por < o >
	 * @param string $xField
	 * @return string
	 */
	function getValueFechaParaComparar($xField)
	{
		$valor = $this->getValue($xField);
		$hoy = getdate(toTimestamp($valor));
		return $hoy["year"] . "-" . str_pad($hoy["mon"], 2, "0", STR_PAD_LEFT) . "-" . str_pad($hoy["mday"], 2, "0", STR_PAD_LEFT);
	}

	function getValueFechaAAAAMMDD($xField)
	{
		$valor = $this->getValue($xField);
		$hoy = getdate(toTimestamp($valor));
		return $hoy["year"] . str_pad($hoy["mon"], 2, "0", STR_PAD_LEFT) . str_pad($hoy["mday"], 2, "0", STR_PAD_LEFT);
	}

	/**
	 * En un campo boolean, retonra Si si es 1, else No
	 */
	function getValueIntSiNo($xField)
	{
		$rta = "No";
		if ($this->getValueInt($xField) == 1)
			$rta = "Si";
		return $rta;
	}

	/**
	 * Retorna la fila actual en un arreglo asociativo.
	 * Reemplaza NULL por ""
	 * @return array
	 */
	function getRow()
	{
		$aTemporal = [];
		foreach ($this->row as $key => $value) {
			if ($value == null) {
				$value = "";
			}
			$aTemporal[$key] = $value;
		}
		$this->row = $aTemporal;
		return $this->row;
	}

	/**
	 * Retorna el ROW actual pero sin Caracteres espciales
	 * @return string[]
	 */
	function getRowSafe()
	{
		$aResult = $this->row;
		$aResult = sinCaracteresEspecialesArray($aResult);
		return $aResult;
	}

	/**
	 * Retorna un array con su contenido, fila a fila
	 * @return multitype:multitype:
	 */
	function getAsArray($xarrayKeyField = "")
	{
		$aResult = array();
		while (!$this->EOF()) {
			if (esVacio($xarrayKeyField))
				$aResult[] = $this->getRow();
			else
				$aResult[$this->getValue($xarrayKeyField)] = $this->getRow();
			$this->Next();
		}
		return $aResult;
	}

	function getSql()
	{
		return $this->sql;
	}

	/**
	 * Avanza una posición en el cursor
	 */
	function Next()
	{
		if ($this->soloAssoc)
			$this->eof = !($this->row = mysqli_fetch_array($this->rsQuery, MYSQLI_ASSOC));
		else
			$this->eof = !($this->row = mysqli_fetch_array($this->rsQuery));
	}

	function EOF()
	{
		return $this->eof;
	}

	function getAffectedRows()
	{
		return $this->affectedRows;
	}

	function clear()
	{
		mysqli_close($this->link);
	}

	function cant()
	{
		if (!is_string($this->rsQuery) && !is_bool($this->rsQuery))
			return mysqli_num_rows($this->rsQuery);
		else
			return 0;
	}

	function getFieldName($i)
	{
		$fieldName = mysqli_fetch_field_direct($this->rsQuery, $i)->name;
		return $fieldName;
	}

	/**
	 * Cantidad de columnas en el query actual
	 */
	function cantF()
	{
		if (is_bool($this->rsQuery))
			return 0;
		return mysqli_num_fields($this->rsQuery);
	}


	function getFieldSize($i)
	{
		$fieldSize = mysqli_fetch_field_direct($this->rsQuery, $i)->length;
		return $fieldSize;
	}


	function resultado()
	{
		return $this->sucefull;
	}


	function getFieldLength($i)
	{
		$properties = mysqli_fetch_field_direct($this->rsQuery, $i);
		return is_object($properties) ? $properties->length : 0;
	}

	/**
	 * Retorna la cantidad de decimales del campo
	 * @param int $i
	 * @return number
	 */
	function getFieldDecimals($i)
	{
		$properties = mysqli_fetch_field_direct($this->rsQuery, $i);
		return is_object($properties) ? $properties->decimals : 0;
	}

	/**
	 * Retorna el tipo de datos del campo (por índice)
	 */
	function getFieldType($i)
	{
		$xType = mysqli_fetch_field_direct($this->rsQuery, $i)->type;
		return $xType;
	}

	/**
	 * Retorna el tipo de dato buscando a columna por nombre
	 */
	function getFieldTypeByName($xname)
	{
		$i = 0;
		$nombre = "";
		$type = "";
		$cant = $this->cantF();
		while ($i < $cant && !sonIguales($nombre, $xname)) {
			$nombre = mysqli_fetch_field_direct($this->rsQuery, $i)->name;
			$type = mysqli_fetch_field_direct($this->rsQuery, $i)->type;
			$i++;
		}
		return $type;
	}


	function escapeSql($xstr)
	{
		return str_replace("'", "\'", $xstr);
	}

	function breakFlags($xstr, $xtype)
	{
		$str = strtolower($xstr);
		$str = str_replace("blob", "", $str);
		$str = str_replace("binary", "", $str);

		$result = "";
		if (strpos($str, "unsigned") !== FALSE && !sonIguales($xtype, "timestamp"))
			$result .= " unsigned";

		if (strpos($str, "not_null") !== FALSE)
			$result .= " not null";

		if (strpos($str, "primary_key") !== FALSE)
			$result .= " primary key";

		return $result;
	}


	/**
	 * Retorna si existe la tabla en la BD
	 *
	 * @param unknown_type $xtabla
	 * @return unknown
	 */
	function existeTabla($xtabla)
	{
		global $BD_DATABASE;

		$sql = "SHOW TABLES FROM " . $BD_DATABASE;
		$result = mysqli_query($this->link, $sql);
		if (!$result) {
			echo "DB Error, could not list tables\n";
			echo 'MySQL Error: ' . mysqli_error($this->link);
			exit;
		}
		while ($row = mysqli_fetch_row($result)) {
			if (sonIguales($row[0], $xtabla))
				return true;
		}

		return false;
	}

	/**
	 * Retorna si existe el indice en la tabla
	 *
	 * @param string $xtable
	 * @param string $xindex
	 */
	function existeIndex($xtable, $xindex)
	{
		$xindex = "`$xindex`";
		$sql = "show create table $xtable";
		$this->execQuery($sql);
		$create = $this->getValue("Create Table");
		if (strContiene($create, $xindex))
			return true;
		return false;
	}

	/**
	 * Borra el ?ndice de la tabla
	 *
	 * @param string $xtable
	 * @param string $xindex
	 */
	function dropIndex($xtable, $xindex)
	{
		echo ("<br>borrando indice <b>$xtable.$xindex</b>...");
		$sql = "alter table $xtable drop index $xindex;";
		$this->execQuery($sql);
	}

	function dropUnique($xtable, $xindex)
	{
		echo ("<br>borrando UNIQUE <b>$xtable.$xindex</b>...");
		$sql = "drop index $xindex on $xtable";
		$this->execQuery($sql);
	}

	/**
	 * Borra el indice de la tabla
	 *
	 * @param string $xtable
	 * @param string $xindex
	 */
	function dropFk($xtable, $xfk)
	{
		$sql = "alter table $xtable drop foreign key $xfk;";
		$this->execQuery($sql);
	}

	/**
	 * Determina si existe un FK en la tabla dada.
	 * Lo hace con el resultado del Create table, buscando el string con el nombre del FK
	 * @param string $xtable
	 * @param string $xfk
	 */
	function existeFk($xtable, $xfk)
	{
		$sql = "show create table $xtable";
		$this->execQuery($sql);
		$create = $this->getValue("Create Table");
		if (strpos($create, $xfk) === false)
			return false;
		return true;
	}

	/*
	Prepares statement and returns it
	*/
	function prepare($xsql)
	{
	}


	function beginT()
	{
		//START TRANSACTION;
		$this->execQuery("START TRANSACTION;");
	}

	function commitT()
	{
		//COMMIT;
		$this->execQuery("COMMIT;");
	}

	function rollbackT()
	{
		//ROLLBACK;
		$this->execQuery("ROLLBACK;");
	}

	function executeCommand($sql)
	{
		$sentencia = mysqli_prepare($this->link, $sql);
		return mysqli_stmt_execute($sentencia);
	}
}
//fin clase


/*
Agrega la condicion y evalua si requiere el AND o el WHERE
*/
function addWhere($xsql, $xwhere, $xcondicion = "and")
{
	$sql = $xsql;
	if (($xwhere != "") && ($xwhere != "=")) {
		if (strcmp($xsql, "") != 0)
			$sql .= " " . $xcondicion . " ";
		$sql .= " (" . $xwhere . ") ";
	}
	return $sql;
}

/*
Agrega las condiciones del where, si es que las hay.
Tambien agrega la palabra WHERE.
Si $xwhere tiene un "select" entonces entiende que es un subquery:
ej: where idcaja in (select id from cajas WHERE...)
*/
function addFilter($xsql, $xwhere)
{
	debug(" addFilter($xsql, $xwhere)");
	$sql = $xsql;
	if ((strcmp($xwhere, "") != 0) && (strcmp($xwhere, "=") != 0)) {
		if ((strpos($sql, " where ") === FALSE)) {
			if (strpos($xwhere, " where ") === FALSE) {
				$sql .= " where ";
			} else {
				if (strpos($xwhere, "select") !== FALSE) {
					$sql .= " where ";
				}
			}
		} else {
			//si hay dos select o mas en el SQL, entonces el sql original tiene un subquery en algun campo
			if (substr_count($xsql,  "select") >= 2) {
				$sql .= " where ";
			}
		}

		$sql .= $xwhere;
	}
	return $sql;
}

function addOrderby($xsql, $xorderby, $xreplace = FALSE)
{
	debug(" addOrderby($xsql, $xorderby)");
	$sql = $xsql;
	if (!sonIguales($xorderby, "")) {
		if (!strContiene($sql, "order by"))
			$sql .= " order by " . $xorderby;
		else {
			//reemplaza el orden existente
			if ($xreplace) {
				$asql = explode("order by", $sql);
				$sql = $asql[0] . " order by " . $xorderby;
			} else
				$sql .= ", " . $xorderby;
		}
	}
	return $sql;
}

function fechaFromRequest($xfield, $xIncludeHr = false)
{
	//analiza si viene el año separado del resto, viejo control de AAAA MM DD
	$anio = RequestInt($xfield . "_a");
	if ($anio != 0) {
		$fecha = RequestInt($xfield . "_a");
		$fecha .= "-";
		$fecha .= RequestInt($xfield . "_m");
		$fecha .= "-";
		$fecha .= RequestInt($xfield . "_d");

		//si tiene fecha diferente al dia de hoy, le saca la hora
		$diaHoy = date("d");
		if ($diaHoy != RequestInt($xfield . "_d"))
			$xIncludeHr = false;

		if ($xIncludeHr)
			$fecha .= " " . date('H:i');

		return $fecha;
	}

	//toma el valor plano, pero le agrega la hr si hace falta
	$fecha = Request($xfield);
	if ($xIncludeHr)
		$fecha .= " " . date('H:i');

	return $fecha;
}

/**
 * retorna la fecha en formato AAAA-MM-DD para poder compararla con > o <
 * @param unknown $xfield
 * @return string
 */
function fechaFromRequestParaComparar($xfield, $xVacio3MesesAnt = false)
{
	$anio = RequestInt($xfield . "_a");
	$fechaReq = Request($xfield);
	if (($anio == 0) && esVacio($fechaReq) && $xVacio3MesesAnt) {
		return Sc3FechaUtils::fechaHace3Meses();
	}

	//analiza nuevo formato
	if ($anio == 0) {
		if (esVacio($fechaReq))
			return "'2015-05-05 00:00'";

		$aFecha = explode("-", $fechaReq);
		if (count($aFecha) == 3)
			return $fechaReq;
	}

	$fecha = RequestInt($xfield . "_a");
	$fecha .= "-";
	$fecha .= str_pad(RequestInt($xfield . "_m"), 2, "0", STR_PAD_LEFT);
	$fecha .= "-";
	$fecha .= str_pad(RequestInt($xfield . "_d"), 2, "0", STR_PAD_LEFT);

	return $fecha;
}
