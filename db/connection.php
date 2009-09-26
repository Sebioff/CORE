<?php

/**
 * Encapsulates a connection to the database.
 * For most of the time it is recommended to use the singleton-like
 * DB_Connection::get() to ensure that all db queries use the same object.
 * It is also possible to have multiple objects of this class though (e.g.
 * when working with multiple databases).
 */
class DB_Connection {
	private static $instance = null;

	private $connectionOptions = null;
	private $connection = null;
	private $transaction = false;
	
	/**
	 * NOTE: you should only create new objects of this class if you really need
	 * a fresh connection to the database (e.g. if you want to connect to another
	 * database than the default one). Use DB_Connection::get() in every other case.
	 * @see DB_Connection::get()
	 * @param $connectionOptions string of the format: mysql://username[:password]@server[:port]?database
	 */
	public function __construct($connectionOptions = DB_CONNECTION) {
		$this->connectionOptions = parse_url($connectionOptions);
	}
	
	/**
	 * Closes the database connection. Usually not really neccessary since the
	 * connection would be closed when the script terminates anyway; only interesting
	 * if there are multiple connections.
	 */
	public function __destruct() {
		mysql_close($this->connection);
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
			if (!mysql_select_db($this->getDatabaseName(), $this->connection))
				throw new Core_Exception('Can\'t connect to database: '.mysql_error());
		}
		
		$result = mysql_query($query, $this->connection);
		if (!$result)
			throw new Core_QueryException('MySQL Query failed: '.mysql_error());
			
		return $result;
	}
	
	/**
	 * @return string the name of the database this object is connected to
	 */
	public function getDatabaseName() {
		return $this->connectionOptions['query'];
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
	
	/**
	 * Deletes ALL tables in the database (even those belonging to other projects)
	 */
	public function deleteTables() {
		$this->query('SET FOREIGN_KEY_CHECKS=0');
		$tables = $this->query('SHOW TABLES');
		while ($table = mysql_fetch_row($tables))
			foreach ($table as $tableName)
				$this->query(sprintf('DROP TABLE %s', $tableName));
		$this->query('SET FOREIGN_KEY_CHECKS=1');
	}
	
	/**
	 * Starts a transaction (set of atomar database operations)
	 */
	public function beginTransaction() {
		if (!$this->transaction) {
			$this->query('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
			$this->transaction = true;
		}
		$this->query('START TRANSACTION');
	}
	
	/**
	 * Commits all database operations started since the last beginTransaction()
	 */
	public function commit() {
		$this->transaction = false;
		return $this->query('COMMIT');
	}
	
	/**
	 * Drops all database operations started since the last beginTransaction()
	 */
	public function rollback() {
		return $this->query('ROLLBACK');
	}
}

// -----------------------------------------------------------------------------

/**
 * Thrown if a database operation went wrong
 */
class Core_QueryException extends Core_Exception {
	
}

?>