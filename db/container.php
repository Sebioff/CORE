<?php

class DB_Container {
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
			return $records[0];
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
	
	/**
	 * Magic methods
	 *  - selectByAttribute($properties = '*', $attributeValue = '', $order = '', $limit = '')
	 *  - selectByAttributeFirst($properties = '*', $attributeValue = '', $order = '')
	 */
	public function __call($name, $params) {
		if(preg_match('/^selectBy(.*)First$/', $name, $matches)) {
			$params = array_merge($params, array('', '', ''));
			return $this->selectFirst($params[0], $this->camelCaseToUnderscores($matches[1]).' = '.$this->addQuotes($params[1]), $params[2]);
		}
		elseif(preg_match('/^selectBy(.*)$/', $name, $matches)) {
			$params = array_merge($params, array('', '', '', ''));
			return $this->select($params[0], $this->camelCaseToUnderscores($matches[1]).' = '.$this->addQuotes($params[1]), $params[2], $params[3]);
		}
		else
			throw new Core_Exception('Call to a non existent function or magic method: '.$name);
	}
	
	private function camelCaseToUnderscores($string) {
		return strtolower(preg_replace(array('/[^A-Z^a-z^0-9^\/]+/','/([a-z\d])([A-Z])/','/([A-Z]+)([A-Z][a-z])/'), array('_','\1_\2','\1_\2'), $string));
	}
	
	private function addQuotes($string) {
		return '\''.$string.'\'';
	}
}

?>