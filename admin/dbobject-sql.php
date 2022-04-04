<?php

//objeto para manejar la coneccion y consultas a la bd
class BDObjectSQL
{
	var $link;
	var $stmt;
	var $bd;
	var $rsQuery;
	var $row;
	var $eof;
	var $soloAssoc = false;
	var $sucefull;
	var $sql;
	var $aMeta = array();
	
	var $affectedRows = 0;
	var $connectionInfo = array();

	function __construct($xserver, $xuser, $xpass, $xdb)
	{
		$this->connectionInfo = array( "Database" => $xdb, "UID" => $xuser, "PWD" => $xpass);
		$conn = sqlsrv_connect($xserver, $this->connectionInfo);
		if( $conn === false ) 
		{
			print_r(sqlsrv_errors());
			die( );
		}
		$this->link = $conn;
		$this->rsQuery = "0";
	}

	function execQuery($strQuery, $xIgnoreEmpty = false, $xSoloAsoc = false, $xInjectUsuario = true)
	{
		debug("[SQL]: " . $strQuery);
		$strQuery = str_replace("\'", "'", $strQuery);	
		
		if ($xIgnoreEmpty && esVacio($strQuery))
			return;
		
		$this->soloAssoc = $xSoloAsoc;
		
		//inyecta valor de usuario
		if ($xInjectUsuario)
			$strQuery = str_replace(":IDUSUARIO", getCurrentUser(), $strQuery);
			
		$this->sql = $strQuery;
		
		$this->rsQuery =  sqlsrv_query($this->link, $strQuery);
		
		if( $this->rsQuery === false )
		{
			print_r(sqlsrv_errors());
			die( );
		}
		$this->sucefull = $this->rsQuery;
		$this->stmt = $this->rsQuery;
		$this->aMeta = $this->transformTypes(sqlsrv_field_metadata($this->stmt));
		
		$this->eof = true;
		if (!is_bool($this->rsQuery)) 
		{
			if ($xSoloAsoc)
				$this->eof = !($this->row = sqlsrv_fetch_array($this->rsQuery, sqlsrv_ASSOC));
			else	
				$this->eof = !($this->row = sqlsrv_fetch_array($this->rsQuery));
		}
		$this->affectedRows = 0;
		//$error = error_reporting(0);
		$this->affectedRows = 0; //sqlsrv_rows_affected($this->link);
		//error_reporting($error);
	}

	/*
	Ejecuta todos los querys del array
	*/
	function execQuerysInArray($xaquerys, $xids)
	{
		foreach ($xaquerys as $sql) 
		{
			$sql = str_replace(array_keys($xids), array_values($xids), $sql);
			$this->execQuery($sql);
		}
	}
	
	/**
	 * Pasa los tipos porque el 12 es varchar y en mysql es date
	 * @param unknown $xArr
	 * @return string[]
	 */
	function transformTypes($xArr)
	{
	    $aResult = array();
	    $i = 0;
	    foreach ($xArr as $field)
	    {
	        if ($field["Type"] == 12)
	            $field["Type"] = "varchar";
            if ($field["Type"] == 4)
                $field["Type"] = "int";

            $aResult[$i] = $field;
            $i++;
	    }
	    
	    return $aResult;
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
		$error = error_reporting(0);
		$id = sqlsrv_insert_id($this->link);
		error_reporting($error);
		debug("[SQL-INSERT]: last id = $id");
		return $id;
	}


	function getValue($strCampo)
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

	function getValueFechaTimestamp($xField)
	{
		$valor = $this->getValue($xField);
		return toTimestamp($valor);
	}
	
	function getValueFechaAsArray($xField)
	{
		$valor = $this->getValue($xField);
		return getdate(toTimestamp($valor));
	}
	
	
	function getValueFechaToRs($xField, $xshowDate = true, $xshowTime = false)
	{
		return Sc3FechaUtils::formatFechaFromRsToRs($this->getValue($xField), $xshowTime, $xshowDate);
	}
	
	function getRow()
	{
		return $this->row;
	}
	
	/**
	 * Retorna un array con su contenido, fila a fila 
	 * @return multitype:multitype:
	 */
	function getAsArray($xarrayKeyField)
	{
		$aResult = array();
		while (!$this->EOF())
		{
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
	
	function Next()
	{	
		if ($this->soloAssoc)
			$this->eof = !($this->row = sqlsrv_fetch_array($this->rsQuery, sqlsrv_ASSOC));
		else
			$this->eof = !($this->row = sqlsrv_fetch_array($this->rsQuery));
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
		sqlsrv_close($this->link);
	}

	function cant()
	{
		if (!is_string($this->rsQuery)) 
			return sqlsrv_num_rows($this->rsQuery);
		else
			return 0;
	}

	function getFieldName($i)
	{
		return $this->aMeta[$i]["Name"];
	}

	function cantF()
	{
		return sqlsrv_num_fields($this->rsQuery);
	}

	function getFieldSize($i)
	{
		return $this->aMeta[$i]["Size"];
	}


	function resultado()
	{
		return $this->sucefull;
	}

	function getFieldLength($i)
	{
		return 0;
	}

	function getFieldType($i)
	{
		return $this->aMeta[$i]["Type"];
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


	/*
	Retorna la cantidad de elementos del query dado
	ejecutando un count(*)
	*/ 	 
	function countRecords($xsql)
	{
		debug("countRecords($xsql)");
		$splitsql = explode(" from ", $xsql);
		$splitsql = explode(" order by ", $splitsql[1]);
		$sql = "select count(*) as cant from " . $splitsql[0];
		$rs = execQuery($strQuery);
		return $rs->getValue("cant");
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
	
		 
}
//fin clase

?>