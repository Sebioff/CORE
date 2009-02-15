<?php

class DB_Container
{
	private $recordClass;
	private $table;
	
	public function __construct($table, $recordClass = 'DB_Record') {
		$this->table = $table;
		$this->recordClass = $recordClass;
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * @return returns only the first fitting record (or null if there is none)
	 */
	public function selectFirst($properties = '*', $condition = '', $order = '', $limit = 1) {
		$records = $this->select($properties, $condition, $order, $limit);
		if(count($records))
			return $record[0];
		else
			return null;
	}
	
	/**
	 * Abstraction for MySQL's SELECT.
	 * @param $properties
	 * @param $condition the search condition
	 * @param $order sorting order
	 * @param $limit
	 * @return an array of records fitting to the specified search parameters
	 */
	public function select($properties = '*', $condition = '', $order = '', $limit = '') {
		$records = array();
		
		$query = 'SELECT '.$properties.' FROM '.$this->table;
		if($condition)
			$query .= ' WHERE '.$condition;
		if($order)
			$query .= ' ORDER BY '.$order;
		if($limit)
			$query .= ' LIMIT '.$limit;
		
		$result = DB_Connection::get()->query($query);
		
		while($row = mysql_fetch_assoc($result)) {
			$record = new $this->recordClass();
			foreach($row as $property => $value) {
				$record->$property = $value;
			}
			$records[] = $record;
		}
		
		return $records;
	}
}

?>