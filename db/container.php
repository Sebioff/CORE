<?php

/**
 * Magic methods:
 * @method array selectByPROPERTY()
 * @method array selectByPROPERTYFirst()
 * @method array deleteByPROPERTY()
 * @method array countByPROPERTY()
 */
class DB_Container {
	private $recordClass = '';
	private $table = '';
	private $databaseSchema = array();
	private $containerCache = array();

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
		if (!empty($records))
			return $records[0];
		else
			return null;
	}

	/**
	 * Abstraction for MySQL's SELECT.
	 * @param $options an options-array which might contain the following elements:
	 * $options['properties'] = the properties that should be selected
	 * $options['conditions'] = array of conditions
	 * $options['order'] = order
	 * $options['limit'] = limit
	 * $options['offset'] = offset
	 * @return an array of records fitting to the specified search parameters
	 */
	public function select(array $options) {
		$records = array();

		$query = 'SELECT '.(isset($options['properties']) ? $options['properties'] : '*').' FROM '.$this->table;
		$query .= $this->buildQueryString($options);
		$databaseSchema = $this->getDatabaseSchema();
		if (isset($this->containerCache[$query]))
			return $this->containerCache[$query];
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
		
		$this->containerCache[$query] = $records;

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
			$values[] = $this->escape($value);
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
				if ($values[$i] === null)
					$updates[] = $properties[$i].' = NULL';
				else
					$updates[] = $properties[$i].' = \''.$values[$i].'\'';
			}
			$query .= implode(', ', $updates);
			$databaseSchema = $this->getDatabaseSchema();
			$query .= ' WHERE '.$databaseSchema['primaryKey'].' = \''.$record->getPK().'\'';
			DB_Connection::get()->query($query);
		}
		
		// clear cache
		$this->containerCache = array();
	}
	
	/**
	 * @param $args either an options-array or a record
	 */
	public function delete($args) {
		if (is_array($args))
			$this->deleteByOptions($args);
		else
			$this->deleteByRecord($args);
			
		// clear cache
		$this->containerCache = array();
	}
	
	protected function deleteByOptions(array $options) {
		$query = 'DELETE FROM '.$this->table;
		$query .= $this->buildQueryString($options);
		DB_Connection::get()->query($query);
	}
	
	protected function deleteByRecord(DB_Record $record) {
		$query = 'DELETE FROM '.$this->table.' WHERE ';
		$databaseSchema = $this->getDatabaseSchema();
		$query .= $databaseSchema['primaryKey'].' = \''.$record->getPK().'\'';
		DB_Connection::get()->query($query);
	}	
	
	protected function buildQueryString(array $options) {
		$query = '';
		if (isset($options['conditions'])) {
			$conditions = array();
			foreach ($options['conditions'] as $condition) {
				$valueCount = count($condition);
				$nextQuestionMark = strpos($condition[0], '?');
				for ($i = 1; $i < $valueCount; $i++) {
					if (is_object($condition[$i]) && $condition[$i] instanceof DB_Record) {
						$conditionValue = $condition[$i]->getPK();
					}
					else {
						$conditionValue = $condition[$i];
					}
					$condition[0] = substr_replace($condition[0], '\''.$this->escape($conditionValue).'\'', $nextQuestionMark, 1);
					$nextQuestionMark = strpos($condition[0], '?', $nextQuestionMark + Text::length($conditionValue) + 1);
				}
				$conditions[] = $condition[0];
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
			
		return $query;
	}
	
	private function loadDatabaseSchema() {
		if($this->databaseSchema = $GLOBALS['cache']->get('SCHEMA_'.$this->table))
			return;
		
		$result = DB_Connection::get()->query('SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.key_column_usage WHERE TABLE_SCHEMA = \''.DB_Connection::get()->getDatabaseName().'\' AND TABLE_NAME = \''.$this->table.'\'');
		while ($keyColumn = mysql_fetch_assoc($result)) {
			if ($keyColumn['CONSTRAINT_NAME'] == 'PRIMARY')
				$this->databaseSchema['primaryKey'] = $keyColumn['COLUMN_NAME'];
			else {
				$this->databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['type'] = 'foreignKey';
				$this->databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['referencedTable'] = $keyColumn['REFERENCED_TABLE_NAME'];
				$this->databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['referencedColumn'] = $keyColumn['REFERENCED_COLUMN_NAME'];
			}
		}

		$GLOBALS['cache']->set('SCHEMA_'.$this->table, $this->databaseSchema);
	}
	
	/**
	 * Does just the same as mysql_real_escape_string(), but without need for an
	 * open database connection.
	 * @param $value the String which is to be escaped
	 */
	public function escape($value) {
		if ($value === null)
			return null;

		return strtr(
			$value, array(
				"\x00" => '\x00',
				"\n" => '\n', 
				"\r" => '\r', 
				'\\' => '\\\\',
				"'" => "\'", 
				'"' => '\"', 
				"\x1a" => '\x1a'
			)
		);
	}
	
	/**
	 * Magic functions
	 */
	public function __call($name, $params) {
		// selectByPROPERTYFirst($propertyValue, $options)
		if (preg_match('/^selectBy(.*)First$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array(Text::camelCaseToUnderscore($matches[1]).' = ?', $params[0]);
			return $this->selectFirst($options);
		}
		// selectByPROPERTY($propertyValue, $options)
		elseif (preg_match('/^selectBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array(Text::camelCaseToUnderscore($matches[1]).' = ?', $params[0]);
			return $this->select($options);
		}
		// deleteByPROPERTY($propertyValue, $options)
		elseif (preg_match('/^deleteBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array(Text::camelCaseToUnderscore($matches[1]).' = ?', $params[0]);
			return $this->delete($options);
		}
		// countByPROPERTY($propertyValue, $options)
		elseif (preg_match('/^countBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array(Text::camelCaseToUnderscore($matches[1]).' = ?', $params[0]);
			return $this->count($options);
		}
		else
			throw new Core_Exception('Call to a non existent function or magic method: '.$name);
	}
	
	/**
	 * If any column of this container references the table of the given container,
	 * references will be resolved using the given container.
	 * It is NOT neccessary to add referenced containers like this (but if you don't,
	 * a standard DB_Container will be used to resolve the reference)
	 */
	// TODO implement "lazy instantiation": except giving a container, give a
	// callback to a method that creates/returns the container. That way the container
	// is only instantiated if really needed
	public function addReferencedContainer(DB_Container $container) {
		foreach ($this->databaseSchema['constraints'] as &$referencedColumn) {
			if ($referencedColumn['referencedTable'] == $container->getTable()) {
				$referencedColumn['referencedContainer'] = $container;
			}
		}
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getDatabaseSchema() {
		return $this->databaseSchema;
	}
	
	public function getTable() {
		return $this->table;
	}
}

?>