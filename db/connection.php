<?php

/**
 * Encapsulates a connection to the database.
 */
class DB_Connection {
	private $connectionOptions = null;
	private $connection = null;
	private static $instance = null;
	
	/**
	 * NOTE: you should only create new objects of this class if you really need
	 * a fresh connection to the database (e.g. if you want to connect to another
	 * database than the default one). Use DB_Connection::get() in every other case.
	 * @see DB_Connection::get()
	 * @param $connectionOptions a String of the format: mysql://username[:passwort]@server[:port]?database
	 */
	public function __construct($connectionOptions = DB_CONNECTION) {
		$this->connectionOptions = parse_url($connectionOptions);
	}

	/**
	 * For low-level-queries.
	 * NOTE: you should use this function as rare as possible (depending on how
	 * extensive our database abstraction/ORM will be even NEVER).
	 * Every database query has to use this function.
	 * @return the queries result
	 */
	public function query($query) {
		if (!$this->connection) {
			// connect to database server
			$server = isset($this->connectionOptions['port'])?$this->connectionOptions['host'].':'.$this->connectionOptions['port']:$this->connectionOptions['host'];
			if (isset($this->connectionOptions['pass']))
				$this->connection = mysql_connect($server, $this->connectionOptions['user'], $this->connectionOptions['pass']);
			else
				$this->connection = mysql_connect($server, $this->connectionOptions['user']);
				
			if (!$this->connection)
				throw new Core_Exception('Can\'t connect to database server: '.mysql_error());
			
			// set active database
			if (!mysql_select_db($this->connectionOptions['query'], $this->connection))
				throw new Core_Exception('Can\'t connect to database: '.mysql_error());
		}

		$result = mysql_query($query, $this->connection);
		if (!$result)
			throw new Core_Exception('MySQL Query failed: '.mysql_error());
			
		return $result;
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
	
	/**
	 * Deletes all tables in the database
	 */
	public function deleteTables() {
		$tables = $this->query('SHOW TABLES');
		while ($table = mysql_fetch_row($tables))
			foreach ($table as $tableName)
				$this->query(sprintf('DROP TABLE %s', $tableName));
	}
}

?>