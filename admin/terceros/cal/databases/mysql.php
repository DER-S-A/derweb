<?php
/***************************************************************************
 *                                 mysql.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: mysql.php,v 1.1 2003/01/18 10:41:50 sproctor Exp $
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if(!defined("CAL_SQL_LAYER"))
{

define("CAL_SQL_LAYER", "mysql");

class cal_database
{

	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;

	//
	// Constructor
	//
	function __construct($sqlserver, $sqluser, $sqlpassword, $database)
	{
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;

		$this->db_connect_id = mysqli_connect($this->server, $this->user, $this->password);
		if($this->db_connect_id)
		{
			if($database != "")
			{
				$this->dbname = $database;
				$dbselect = mysqli_select_db($this->db_connect_id, $this->dbname);
				if(!$dbselect)
				{
					mysqli_close($this->db_connect_id);
					$this->db_connect_id = $dbselect;
				}
			}
			return $this->db_connect_id;
		}
		else
		{
			return false;
		}
	}

	//
	// Other base methods
	//
	function sql_close()
	{
		if($this->db_connect_id)
		{
			if($this->query_result)
			{
				mysqli_free_result($this->query_result);
			}
			$result = mysqli_close($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}

	//
	// Base query method
	//
	function sql_query($query = "", $transaction = FALSE)
	{
		// Remove any pre-existing queries
		unset($this->query_result);
		if($query != "")
		{
			$this->num_queries++;
			$this->query_result = mysqli_query($this->db_connect_id, $query);
		}

		if ($this->query_result)
		{
			unset($this->row[$this->num_queries]);
			unset($this->rowset[$this->num_queries]);
			return $this->query_result;
		}
		else
		{
			//return ( $transaction == END_TRANSACTION ) ? true : false;
			return false;
		}
	}

	//
	// Other query methods
	//
	function sql_numrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = mysqli_num_rows($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_affectedrows()
	{
		if($this->db_connect_id)
		{
			$result = mysqli_affected_rows($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_numfields($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = mysqli_num_fields($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldname($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = mysqli_field_name($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldtype($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = mysqli_field_type($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}

	function sql_fetchrow($query_id = 0)
	{
		if (!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
		//	$this->row[$query_id] = mysqli_fetch_array($query_id);
		//	return $this->row[$query_id];

			$this->row[serialize($query_id)] = mysqli_fetch_array($query_id);
			return $this->row[serialize($query_id)];
		}
		else
		{
			return false;
		}
	}

	function sql_fetchrowset($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			unset($this->rowset[$query_id]);
			unset($this->row[$query_id]);
			while($this->rowset[$query_id] = mysqli_fetch_array($query_id))
			{
				$result[] = $this->rowset[$query_id];
			}
			return $result;
		}
		else
		{
			return false;
		}
	}

	function sql_fetchfield($field, $rownum = -1, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			if($rownum > -1)
			{
				$result = mysqli_result($query_id, $rownum, $field);
			}
			else
			{
				if(empty($this->row[$query_id]) && empty($this->rowset[$query_id]))
				{
					if($this->sql_fetchrow())
					{
						$result = $this->row[$query_id][$field];
					}
				}
				else
				{
					if($this->rowset[$query_id])
					{
						$result = $this->rowset[$query_id][$field];
					}
					else if($this->row[$query_id])
					{
						$result = $this->row[$query_id][$field];
					}
				}
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_rowseek($rownum, $query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = mysqli_data_seek($query_id, $rownum);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_nextid(){
		if($this->db_connect_id)
		{
			$result = mysqli_insert_id($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_freeresult($query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}

		if ( $query_id )
		{
			unset($this->row[$query_id]);
			unset($this->rowset[$query_id]);

			mysqli_free_result($query_id);

			return true;
		}
		else
		{
			return false;
		}
	}
	// RETURNS ANY ERROR FROM THE LAST QUERY
	function sql_error($query_id = 0)
	{
		$result = mysqli_error($this->db_connect_id);
		return $result;
	}
	// THIS ESCAPES A STRING TO STOP SQL INJECTION ATTACKS
	function sql_escapestring($s){
		return mysqli_real_escape_string($this->db_connect_id, $s);
	}
	// THIS FUNCTION RETURNS BLANK SCREEN IF YOUR VERSION IS AT LEAST THE REQUIRED MINIMUM
	// IT RETURNS THE REQUIRED MINIMUM VERSION IF YOU'RE VERSION IS TOO OLD
	function sql_version(){
		return "";
		/*
		// get version.
		$v = mysqli_get_server_info($this->db_connect_id);
		if(!$v) return "(ERROR FETCHING VERSION)";
		$v = substr($v, 0, strpos($v, "-"));
		$a = explode(".",$v);
		// check version.  If they have at least 4.1.1 it's okay, return nothing.
		if($a[0]>=5) return "";
		if($a[0]==4 && $a[1]>1) return "";
		if($a[0]==4 && $a[1]==1 && $a[2]>=1) return "";
		// this is BAD!  This returns the version you need at MINIMUM
		return "4.1.1";
		*/
	}

} // class cal_database

} // if ... define

?>
