<?php

/**
 * DB_Container is an abstraction of a database table
 * Magic methods:
 * @method array selectByPROPERTY()
 * @method array selectByPROPERTYFirst()
 * @method array deleteByPROPERTY()
 * @method array countByPROPERTY()
 */
class DB_Container {
	private static $globalReferencedContainers = array();
	private static $containerCache = array();
	private static $databaseSchemata = array();
	
	private $recordClass = '';
	private $table = '';
	private $insertCallbacks = array();
	private $updateCallbacks = array();
	private $deleteCallbacks = array();
	private $filters = array();
	private $optimisticallyLockedProperties = null;
	private $connection = null;

	public function __construct($table, $recordClass = 'DB_Record') {
		$this->table = $table;
		$this->recordClass = $recordClass;
	}

	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * @return DB_Record returns only the first fitting record (or null if there is none)
	 */
	public function selectFirst(array $options = array()) {
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
	 * $options['group'] = group by
	 * $options['order'] = order
	 * $options['limit'] = limit
	 * $options['offset'] = offset
	 * $options['join'] = array of tables
	 * @return an array of records fitting to the specified search parameters
	 */
	public function select(array $options = array()) {
		$records = array();

		$query = 'SELECT '.(isset($options['properties']) ? $options['properties'] : '`'.$this->table.'`.*').' FROM `'.$this->table.'`';
		$query .= $this->buildQueryString($options);
		if (isset(self::$containerCache[$this->getTable()][$this->getRecordClass()][$query]))
			return self::$containerCache[$this->getTable()][$this->getRecordClass()][$query];
			
		$result = $this->getConnection()->query($query);

		// create records from query result
		while ($row = mysql_fetch_assoc($result)) {
			$record = new $this->recordClass();
			$record->setContainer($this);
			foreach ($row as $property => $value) {
				$property = Text::underscoreToCamelCase($property);
				$record->$property = $value;
			}
			$records[] = $record;
		}
		
		self::$containerCache[$this->getTable()][$this->getRecordClass()][$query] = $records;
		
		return $records;
	}
	
	/**
	 * @return DB_Record the record belonging to the given primary key
	 */
	public function selectByPK($value, array $options = array()) {
		$databaseSchema = $this->getDatabaseSchema();
		$options['conditions'][] = array('`'.$databaseSchema['primaryKey'].'` = ?', $value);
		return $this->selectFirst($options);
	}
	
	/**
	 * @return int the number of rows that match the conditions specified in the
	 * given options array
	 */
	public function count(array $options = array()) {
		$options['properties'] = 'COUNT(*) AS core_count_result';
		return (int)$this->selectFirst($options)->coreCountResult;
	}
	
	/**
	 * Saves an record into the database
	 * If the record hasn't been saved before it is inserted, otherwise it is updated
	 */
	public function save(DB_Record $record) {
		$properties = array();
		$values = array();
		if (!$record->getPK()) {
			// insert
			foreach ($record->getAllProperties() as $property => $value) {
				$properties[] = Text::camelCaseToUnderscore($property);
				$values[] = self::escape($value);
			}
			$query = 'INSERT INTO `'.$this->table.'`';
			$query .= ' (`'.implode('`, `', $properties).'`) VALUES';
			$query .= ' (\''.implode('\', \'', $values).'\')';
			$this->insertByQuery($query, $record);
		}
		else {
			// update
			$options = array();
			$usesOptimisticLocking = false;
			foreach ($record->getModifiedProperties() as $property => $oldValue) {
				$propertyDBName = Text::camelCaseToUnderscore($property);
				$propertyValue = self::escape($record->get($property));
				$properties[] = $propertyDBName;
				$values[] = $propertyValue;
				if ($this->optimisticallyLockedProperties !== null && (empty($this->optimisticallyLockedProperties) || in_array($property, $this->optimisticallyLockedProperties))) {
					$options['conditions'][] = array('`'.$propertyDBName.'` = ?', $oldValue);
					$usesOptimisticLocking = true;
				}
			}
			
			if (empty($properties))
				return;
				
			$query = 'UPDATE `'.$this->table.'` SET ';
			$propertiesCount = count($properties);
			$updates = array();
			for ($i = 0; $i < $propertiesCount; $i++) {
				if ($values[$i] === null)
					$updates[] = '`'.$properties[$i].'` = NULL';
				else
					$updates[] = '`'.$properties[$i].'` = \''.$values[$i].'\'';
			}
			$query .= implode(', ', $updates);
			$databaseSchema = $this->getDatabaseSchema();
			$options['conditions'][] = array('`'.$databaseSchema['primaryKey'].'` = ?', $record->getPK());
			$query .= $this->buildQueryString($options);
			$this->update($query, $record);
			// count
			if ($usesOptimisticLocking) {
				if (mysql_affected_rows() <= 0)
					throw new Core_Exception('Concurrent version modification.');
				
				// record is now up to date
				$modifiedProperties = &$record->getModifiedProperties();
				$modifiedProperties = array();
			}
		}
	}
	
	/**
	 * @param $args either an options-array or a record
	 */
	public function delete($args = array()) {
		if (is_array($args))
			$this->deleteByOptions($args);
		else
			$this->deleteByRecord($args);
	}
	
	/**
	 * Removes the entries specified by the $options array from the database
	 */
	protected function deleteByOptions(array $options) {
		$query = 'DELETE FROM `'.$this->table.'`';
		$query .= $this->buildQueryString($options);
		$this->deleteByQuery($query);
	}
	
	/**
	 * Removes a given record from the database
	 */
	protected function deleteByRecord(DB_Record $record) {
		$query = 'DELETE FROM `'.$this->table.'` WHERE ';
		$databaseSchema = $this->getDatabaseSchema();
		$query .= '`'.$databaseSchema['primaryKey'].'` = \''.$record->getPK().'\'';
		$this->deleteByQuery($query, $record);
	}
	
	/**
	 * Executes an insert query.
	 * NOTE: it should usually not be neccessary to use this method! Use save() instead.
	 */
	protected function insertByQuery($query, DB_Record $record) {
		$result = $this->getConnection()->query($query);
		$record->setContainer($this);
		$databaseSchema = $this->getDatabaseSchema();
		if (isset($databaseSchema['primaryKey']))
			$record->$databaseSchema['primaryKey'] = mysql_insert_id();
		
		// clear cache
		self::$containerCache[$this->getTable()] = array();
		
		// execute insertCallbacks
		foreach ($this->insertCallbacks as $insertCallback)
			call_user_func($insertCallback, $record);
		
		return $result;
	}
	
	/**
	 * Updates the entries specified by the $options array
	 * NOTE: it should usually not be neccessary to use this method! Use save() instead.
	 */
	public function updateByOptions(array $options) {
		$query = 'UPDATE `'.$this->table.'` SET '.$options['properties'];
		$query .= $this->buildQueryString($options);
		$this->update($query);
	}
	
	/**
	 * Executes an update query.
	 * NOTE: it should usually not be neccessary to use this method! Use save() instead.
	 */
	public function update($query, DB_Record $record = null) {
		$result = $this->getConnection()->query($query);
		
		// clear cache
		self::$containerCache[$this->getTable()] = array();
		
		// execute updateCallbacks
		foreach ($this->updateCallbacks as $updateCallback)
			call_user_func($updateCallback, $record);
		
		return $result;
	}
	
	/**
	 * Executes an delete query.
	 * NOTE: it should usually not be neccessary to use this method! Use delete() instead.
	 */
	public function deleteByQuery($query, DB_Record $record = null) {
		$result = $this->getConnection()->query($query);
		
		// clear cache
		self::$containerCache[$this->getTable()] = array();
		
		// execute deleteCallbacks
		foreach ($this->deleteCallbacks as $deleteCallback)
			call_user_func($deleteCallback, $record);
		
		return $result;
	}
	
	/**
	 * @return string MySQL query string, build from the given array of options
	 */
	protected function buildQueryString(array $options) {
		if (!empty($this->filters)) {
			foreach ($this->filters as $filter)
				$options = self::mergeOptions($options, $filter);
		}
		
		$query = '';
		if (isset($options['join']))
			$query .= ', `'.implode('`, `', $options['join']).'`';
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
					$condition[0] = substr_replace($condition[0], '\''.self::escape($conditionValue).'\'', $nextQuestionMark, 1);
					$nextQuestionMark = strpos($condition[0], '?', $nextQuestionMark + Text::length($conditionValue) + 1);
				}
				$conditions[] = $condition[0];
			}
			$conditionSQL = implode(') AND (', $conditions);
			$query .= ' WHERE ('.$conditionSQL.')';
		}
		if (isset($options['group']))
			$query .= ' GROUP BY '.$options['group'];
		if (isset($options['order']))
			$query .= ' ORDER BY '.$options['order'];
		if (isset($options['limit']))
			$query .= ' LIMIT '.$options['limit'];
		if (isset($options['offset']))
			$query .= ' OFFSET '.$options['offset'];
			
		return $query;
	}
	
	/**
	 * Magic functions
	 */
	public function __call($name, $params) {
		// selectByPROPERTYFirst($propertyValue, $options)
		if (preg_match('/^selectBy(.*)First$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array('`'.Text::camelCaseToUnderscore($matches[1]).'` = ?', $params[0]);
			return $this->selectFirst($options);
		}
		// selectByPROPERTY($propertyValue, $options)
		elseif (preg_match('/^selectBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array('`'.Text::camelCaseToUnderscore($matches[1]).'` = ?', $params[0]);
			return $this->select($options);
		}
		// deleteByPROPERTY($propertyValue, $options)
		elseif (preg_match('/^deleteBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array('`'.Text::camelCaseToUnderscore($matches[1]).'` = ?', $params[0]);
			return $this->delete($options);
		}
		// countByPROPERTY($propertyValue, $options)
		elseif (preg_match('/^countBy(.*)$/', $name, $matches)) {
			$options = isset($params[1]) ? $params[1] : array();
			$options['conditions'][] = array('`'.Text::camelCaseToUnderscore($matches[1]).'` = ?', $params[0]);
			return $this->count($options);
		}
		else
			throw new Core_Exception('Call to a non existent function or magic method: '.$name);
	}
	
	/**
	 * If the given column of this container references the table of the given container,
	 * the reference will be resolved using the given container. Note that if you
	 * specify $referencedColumn as well this can also be used if the database doesn't
	 * support foreign keys or no foreign keys are defined.
	 * If no column is given all references of this container to the table of
	 * the given container will be resolved with the given container (only possible
	 * if the database supports foreign keys).
	 * It is NOT neccessary to add referenced containers like this (but if you don't,
	 * a standard DB_Container will be used to resolve the reference)
	 * @param $column string name of the column that references the given container
	 * or null if all references to the table of the given container should be
	 * resolved with the given container
	 * @param $referencedColumn string name of the referenced column. Only needed
	 * in combination with $column and if database doesn't support foreign keys
	 * or no foreign keys are defined.
	 */
	// TODO implement "lazy instantiation": instead of giving a container, give a
	// callback to a method that creates/returns the container. That way the container
	// is only instantiated if really needed
	public function addReferencedContainer(DB_Container $container, $column = null, $referencedColumn = null) {
		$databaseSchema = &$this->getDatabaseSchema();
		if ($column === null) {
			foreach ($databaseSchema['constraints'] as &$referencedColumn) {
				if ($referencedColumn['referencedTable'] == $container->getTable()) {
					$referencedColumn['referencedContainer'] = $container;
				}
			}
		}
		else {
			$databaseSchema['constraints'][$column]['referencedContainer'] = $container;
			if ($referencedColumn !== null) {
				$databaseSchema['constraints'][$column]['type'] = 'foreignKey';
				$databaseSchema['constraints'][$column]['referencedTable'] = $container->getTable();
				$databaseSchema['constraints'][$column]['referencedColumn'] = $referencedColumn;
			}
		}
	}
	
	/**
	 * Defines that the given container should be used to resolve ALL references
	 * of all other containers to the table the given container encapsulates with
	 * the given container. Can be overriden by DB_Container::addReferencedContainer()
	 * for single containers.
	 * @param $container the container which is to be used to resolve all references
	 * to the table of the container
	 */
	public static function addReferencedContainerGlobal(DB_Container $container) {
		self::$globalReferencedContainers[$container->getTable()] = $container;
		
		// handle self-references
		$databaseSchema = &$container->getDatabaseSchema();
		if (isset($databaseSchema['constraints'])) {
			foreach ($databaseSchema['constraints'] as &$properties) {
				if ($properties['referencedTable'] == $container->getTable()) {
					$properties['referencedContainer'] = $container;
				}
			}
		}
	}
	
	/**
	 * Adds a callback that is executed whenever a new record is added to this
	 * container.
	 * The callback receives the inserted DB_Record as first parameter.
	 */
	public function addInsertCallback($callback) {
		$this->insertCallbacks[] = $callback;
	}
	
	/**
	 * Adds a callback that is executed whenever a record is updated in this
	 * container.
	 * The callback receives the updated DB_Record as first parameter (optional).
	 */
	public function addUpdateCallback($callback) {
		$this->updateCallbacks[] = $callback;
	}
	
	/**
	 * Adds a callback that is executed whenever a record is deleted in this
	 * container.
	 * The callback receives the deleted DB_Record as first parameter (optional).
	 */
	public function addDeleteCallback($callback) {
		$this->deleteCallbacks[] = $callback;
	}
	
	/**
	 * A filtered container is a container with filters that are applied to every
	 * single query. A filter is nothing more than a usual options-array
	 * @return DB_Container
	 */
	public function getFilteredContainer(array $filterOptions) {
		$clone = clone $this;
		$clone->filters[] = $filterOptions;
		return $clone;
	}
	
	/**
	 * Merges two options-arrays. Note that options defined in $majorOptions
	 * will override options set in $minorOptions if they conflict.
	 */
	public static function mergeOptions(array $minorOptions, array $majorOptions) {
		// multidimensional arrays have to be merged manually, otherwise the array
		// of the $majorOptions would totally overwrite the array of the $minorOptions
		if (isset($minorOptions['conditions'])) {
			if (isset($majorOptions['conditions']))
				$majorOptions['conditions'] = array_merge($minorOptions['conditions'], $majorOptions['conditions']);
			else
				$majorOptions['conditions'] = $minorOptions['conditions'];
		}
		if (isset($minorOptions['join'])) {
			if (isset($majorOptions['join']))
				$majorOptions['join'] = array_merge($minorOptions['join'], $majorOptions['join']);
			else
				$majorOptions['join'] = $minorOptions['join'];
		}
		return array_merge($minorOptions, $majorOptions);
	}
	
	/**
	 * Does just the same as mysql_real_escape_string(), but without need for an
	 * open database connection.
	 * @param $value the string which is to be escaped
	 */
	public static function escape($value) {
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
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * Automatically loads the schema of this containers' table.
	 * This way, primary keys and foreign keys can be resolved easily.
	 */
	public function &getDatabaseSchema() {
		$databaseSchema = &self::$databaseSchemata[$this->getTable()];
		/*
		 * We COULD always return the version from the cache, but the database schema
		 * is pretty often needed, so it's even faster to just keep a copy of it
		 * in this object.
		 */
		if ($databaseSchema || $databaseSchema = $GLOBALS['cache']->get('SCHEMA_'.$this->table)) {
			return $databaseSchema;
		}
			
		$result = $this->getConnection()->query('SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.key_column_usage WHERE TABLE_SCHEMA = \''.$this->getConnection()->getDatabaseName().'\' AND TABLE_NAME = \''.$this->table.'\'');
		while ($keyColumn = mysql_fetch_assoc($result)) {
			$keyColumn['COLUMN_NAME'] = Text::underscoreToCamelCase($keyColumn['COLUMN_NAME']);
			if ($keyColumn['CONSTRAINT_NAME'] == 'PRIMARY') {
				$databaseSchema['primaryKey'] = $keyColumn['COLUMN_NAME'];
			}
			else {
				$databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['type'] = 'foreignKey';
				$databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['referencedTable'] = $keyColumn['REFERENCED_TABLE_NAME'];
				$databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['referencedColumn'] = $keyColumn['REFERENCED_COLUMN_NAME'];
				if (isset(self::$globalReferencedContainers[$keyColumn['REFERENCED_TABLE_NAME']]))
					$databaseSchema['constraints'][$keyColumn['COLUMN_NAME']]['referencedContainer'] = self::$globalReferencedContainers[$keyColumn['REFERENCED_TABLE_NAME']];
			}
		}

		$GLOBALS['cache']->set('SCHEMA_'.$this->table, $databaseSchema);
		return $databaseSchema;
	}
	
	/**
	 * @return string the name of the table this container encapsulates
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * @return string the name of the record class from which this container returns
	 * objects
	 */
	public function getRecordClass() {
		return $this->recordClass;
	}
	
	/**
	 * @param $properties array of properties that should be locked optimistically
	 * or an empty array if all properties should use optimistic locking
	 * NOTE: don't use optimistic locking on properties that can't be compared
	 * properly (e.g. floats, doubles, ...)!
	 */
	public function enableOptimisticLockingForProperties($properties = array()) {
		$this->optimisticallyLockedProperties = $properties;
	}
	
	/**
	 * @return DB_Connection the connection that has been specifically set to be
	 * used by this container or the default connection available via DB_Connection::get()
	 * if none has been set.
	 */
	public function getConnection() {
		return ($this->connection !== null) ? $this->connection : DB_Connection::get();
	}
	
	public function setConnection(DB_Connection $connection) {
		$this->connection = $connection;
	}
}

?>