<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
/*
 * dynamische Datenbankklasse, die die gängigsten Operationen ausführen kann
 * (c) 2008 by Alexander Bierbrauer, polyvision
 */
class DBObject
{
	var $id;
	var $id_name;
	var $table_name;
	var $columns = array();
	
	function DBObject($table_name, $id_name, $columns)
	{
		$this->table_name = $table_name;
		$this->id_name = $id_name;
	
		foreach($columns as $key)
	    	$this->columns[$key] = null;
	}
	
	/*
	 * wir überschreiben die default-get methode, und holen uns daher die variablen aus dem array
     */
	function __get($key)
	{
	  return $this->columns[$key];
	}
	
	function __set($key, $value)
	{
		if(array_key_exists($key, $this->columns))
		{
			$this->columns[$key] = $value;
			return true;
	  	}
	  return false;
	}
	
	/**
	*php4 compability layer .... php4 sucks ! oop rules !
	* this function should be called when using php4 instead of $this->"column_name"
	**/
	function getEntry($key)
	{
		return $this->columns[$key];
	}
	
	function setEntry($key,$value)
	{
		$this->columns[$key] = $value;
	}
	
	function addVar($key,$value)
	{
		$this->columns[$key] = $value;
	}
	
	function select(&$adodb,$id)
	{
		$rs= $adodb->GetRow('SELECT * FROM '. $this->table_name .' WHERE '. $this->id_name .' = \''.$id.'\'');
		if($rs)
		{
			$this->id = $rs[$this->id_name];
			$this->fill($rs);
			return true;
		}
		
		return false;
	}
	
	function insert(&$adodb)
	{
		unset($this->columns[$this->id_name]);
		
		$columns = join(', ', array_keys($this->columns));
		$values = '\'' . join('\', \'', $this->columns) . '\'';
		
		$rs = $adodb->Execute('INSERT INTO '. $this->table_name . ' ('.$columns.') VALUES ('.$values.')');
		if($rs == false)
		{
			return 0;
		}
		
		$this->id = $adodb->Insert_ID();
		return $this->id;
	}
	
	function update(&$adodb)
	{

		unset($this->columns[$this->id_name]); // bloss nicht die id überschreiben, daher dies !
		$arrStuff = array();
		foreach($this->columns as $key => $val)
		{
			if(gettype($val) != 'object')
				$arrStuff[] = $key.' = \''.addslashes($val).'\'';
		}
			
			
		$stuff = implode(', ', $arrStuff);
		$sqlstring = 'UPDATE '. $this->table_name.' SET '.$stuff.' WHERE '.$this->id_name.' = \''. $this->id.'\'';
		return $adodb->Execute($sqlstring);
	}
	
	function delete(&$adodb)
	{
	  $adodb->query('DELETE FROM '.$this->table_name.' WHERE '.$this->id_name.' = \''.$this->id.'\'');
	}
	
	/*
	 * füllt das Objekt mit entsprechenden Werten aus einem Array
	 */
	function fill(&$vars,$filter = NULL)
	{
		$ckeys = array_keys($this->columns);
		foreach($ckeys as $key)
		{
			if($filter == NULL)
			{
				if(isset($vars[$key]))
					$this->columns[$key] = stripslashes($vars[$key]);
			}
			else
			{
				$filtered_key = $filter.$key;
				if(isset($vars[$filtered_key]))
					$this->columns[$key] = stripslashes($vars[$filtered_key]);
			}
		}
	}
}
?>