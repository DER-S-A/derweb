<?php

//objeto para manejar la coneccion y consultas a la bd
class BDObjectPdo
{
	var $rsQuery;
	var $row;
	var $eof;
	var $soloAssoc = false;
	var $successful;
	var $sql;
	var $affectedRows = 0;

	var $pdo;

	function BDObject()
	{
		global $mysql_host;
		global $mysql_username;
		global $mysql_passwd;
		global $mysql_database;

		$this->pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_username, $mysql_passwd);
		$this->rsQuery = "0";
	}

	function execQuery($strQuery, $xIgnoreEmpty = false, $xSoloAsoc = false, $xInjectUsuario = true)
	{
		$strQuery = str_replace("\'", "'", $strQuery);	
		
		$this->soloAssoc = $xSoloAsoc;
		
		$this->sql = $strQuery;
		
		$this->rsQuery = $this->pdo->query($strQuery);
		if($strQuery === FALSE) {
			error_log($strQuery ." - ".  json_encode($this->pdo->errorInfo()));
			echo("<pre>".$strQuery ."<br>".  json_encode($this->pdo->errorInfo(), JSON_PRETTY_PRINT)."<pre>");
			die();
		}

		$this->eof = true;
		$this->affectedRows = 0;
		if(($this->rsQuery) instanceof PDOStatement) 
		{
			if($xSoloAsoc) {
				$this->eof = !($this->row = $this->rsQuery->fetch(PDO::FETCH_ASSOC));
			} else	{
				$this->eof = !($this->row = $this->rsQuery->fetch());
			}
			$this->affectedRows = $this->rsQuery->rowCount();
		}
	}

	/*
	Ejecuta todos los querys del array
	*/
	function execQuerysInArray($xaquerys, $xids)
	{
		foreach($xaquerys as $sql) 
		{
			$sql = str_replace(array_keys($xids), array_values($xids), $sql);
			$this->execQuery($sql);
		}
	}
	
	/*
	 Ejecuta todos los querys del array
	*/
	function execQuerysIdsInArray($xsql, $xids)
	{
		$sql = str_replace(array_keys($xids), array_values($xids), $xsql);
		$this->execQuery($sql);
	}
	
	
	/*
	Ejecuta el query y retorna el valor del ultimo autonumerico
	*/
	function execInsert($strQuery)
	{
		$this->execQuery($strQuery);
		$id = $this->pdo->lastInstertId();
		return $id;
	}

	function getValue($strCampo)
	{
		if(isset($this->row[$strCampo])) {
			return $this->row[$strCampo];
		}
		
		return "";
	}

	function prepare($strQuery)
	{
		return $this->pdo->prepare($strQuery);
	}
	
	function getValueInt($xField)
	{
		return(int) $this->getValue($xField);
	}
	
	function getId()
	{
		return $this->getValueInt("id");
	}
	
	function getValueFloat($xField, $xDecimales = 2)
	{
		return round((float) $this->getValue($xField), $xDecimales);
	}
	
	function getRow()
	{
		return $this->row;
	}
	
	
	function Next()
	{	
		if($this->soloAssoc) {
			$this->eof = !($this->row = $this->rsQuery->fetch(PDO::FETCH_ASSOC));
		} else {
			$this->eof = !($this->row = $this->rsQuery->fetch());
		}
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
		if(($this->rsQuery) instanceof PDOStatement) {
			$this->rsQuery->close;
		}
		$this->pdo = null;
	}

	function getFieldName($i)
	{

		return $this->rsQuery->getColumnMeta($i)['name'];
	}

	function cantF()
	{
		return $this->rsQuery->columnCount();
	}

	function resultado()
	{
		return $this->successful;
	}

	function escapeSql($xstr)
	{
		return str_replace("'", "\'", $xstr);
	}

	function startTransaction()
	{
		$this->pdo->beginTransaction() or die("Error al iniciar la transaccion");
	}

	function endTransaction()
	{
		$this->pdo->commit() or die("Error al hacer commit de transaccion");
	}
}

?>