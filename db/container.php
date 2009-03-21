<?php

/**
 * Magic methods:
 * @method array selectByPROPERTY()
 * @method array selectByPROPERTYFirst()
 */
class DB_Container {
	private $recordClass = '';
	private $table = '';
	private $databaseSchema = array();

	public function __construct($table, $recordClass = 'DB_Record') {
		$this->table = $table;
		$this->recordClass = $recordClass;
		$this->loadDatabaseSchema();
	}

	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * @return DB_Record returns only the first fitting record (or null if there is none)
	 */
	public function selectFirst(array $options) {
		$options['limit'] = 1;
		$records = $this->select($options);
		if (count($records))
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
	public function select(array $options) {
		$records = array();

		$query = 'SELECT '.(isset($options['properties'])?$options['properties']:'*').' FROM '.$this->table;
		if (isset($options['conditions'])) {
			$conditions = array();
			foreach ($options['conditions'] as $condition) {
				if (is_object($condition[1]) && $condition[1] instanceof DB_Record) {
					$conditionValue = $condition[1]->getPK();
				}
				else {
					$conditionValue = $condition[1];
				}
				$conditions[] = str_replace('?', '\''.mysql_real_escape_string($conditionValue).'\'', $condition[0]);
			}
			$conditionSQL = implode(') AND (', $conditions);
			$query .= ' WHERE ('.$conditionSQL.')';
		}
		if (isset($options['order']))
			$query .= ' ORDER BY '.$options['order'];
		if (isset($options['limit']))
			$query .= ' LIMIT '.$options['limit'];
		if (isset($options['offset']))
			$query .= ' OFFSET '.$options['offset'];

		$result = DB_Connection::get()->query($query);

		while ($row = mysql_fetch_assoc($result)) {
			$record = new $this->recordClass();
			$record->setContainer($this);
			foreach ($row as $property => $value) {
				$property = Text::underscoreToCamelCase($property);
				$record->$property = $value;
			}
			$records[] = $record;
		}

		return $records;
	}
	
	public function selectByPK($value, array $options = array()) {
		$options['conditions'][] = array($this->databaseSchema['primaryKey'].' = ?', $value);
		return $this->selectFirst($options);
	}
	
	public function count(array $options = array()) {
		$options['properties'] = 'COUNT(*)';
		$result = $this->selectFirst($options)->getAllProperties();
		return (int)array_shift($result);
	}
	
	public function save(DB_Record $record) {
		$properties = array();
		$values = array();
		foreach ($record->getAllProperties() as $property => $value) {
			$properties[] = Text::camelCaseToUnderscore($property);
			if (is_object($value) && $value instanceof DB_Record)
				$value = $value->getPK();
			$values[] = $value;
		}
		if (!$record->getPK()) {
			// insert
			$query = 'INSERT INTO '.$this->table;
			$query .= ' ('.implode(', ', $properties).') VALUES';
			$query .= ' (\''.implode('\', \'', $values).'\')';
			DB_Connection::get()->query($query);
			$record->setContainer($this);
			$databaseSchema = $this->getDatabaseSchema();
			$record->$databaseSchema['primaryKey'] = mysql_insert_id();
		}
		else {
			// update
			$query = 'UPDATE '.$this->table.' SET ';
			$propertiesCount = count($properties);
			$updates = array();
			for ($i = 0; $i < $propertiesCount; $i++) {
				$updates[] = $properties[$i].' = \''.$values[$i].'\'';
			}
			$query .= implode(', ', $updates);
			$databaseSchema = $this->getDatabaseSchema();
			$query .= ' WHERE '.$databaseSchema['primaryKey'].' = \''.$record->getPK().'\'';
			DB_Connection::get()->query($query);
		}
	}
	
	public function delete(DB_Record $record) {
		$query = 'DELETE FROM '.$this->table.' WHERE ';
		$databaseSchema = $this->getDatabaseSchema();
		$query .= $databaseSchema['primaryKey'].' = \''.$record->getPK().'\'';
		DB_Connection::get()->query($query);
	}
	
	private function loadDatabaseSchema() {
		if($this->databaseSchema = $GLOBALS['memcache']->get('SCHEMA_'.$this->table))
			return;
			
		$result = DB_Connection::get()->query('SELECT COLUMN_NAME, CONSTRAINT_NAME FROM information_schema.key_column_usage WHERE TABLE_NAME = \''.$this->table.'\'');
		while ($keyColumn = mysql_fetch_assoc($result)) {
			if ($keyColumn['CONSTRAINT_NAME'] == 'PRIMARY')
				$this->databaseSchema['primaryKey'] = $keyColumn['COLUMN_NAME'];
			else
				$this->databaseSchema['constraints'][$keyColumn['COLUMN_NAME']] = 'foreignKey';
		}
		$GLOBALS['memcache']->set('SCHEMA_'.$this->table, $this->databaseSchema);
	}
	
	public function __call($name, $params) {
		if (preg_match('/^selectBy(.*)First$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array(Text::camelCaseToUnderscore($matches[1]).' = ?', $params[0]);
			return $this->selectFirst($options);
		}
		elseif (preg_match('/^selectBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array(Text::camelCaseToUnderscore($matches[1]).' = ?', $params[0]);
			return $this->select($options);
		}
		else
			throw new Core_Exception('Call to a non existent function or magic method: '.$name);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getDatabaseSchema() {
		return $this->databaseSchema;
	}
}

?>