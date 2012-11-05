<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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
	private $transactionLevel = 0;
	
	/**
	 * NOTE: you should only create new objects of this class if you really need
	 * a fresh connection to the database (e.g. if you want to connect to another
	 * database than the default one). Use DB_Connection::get() in every other case.
	 * @see DB_Connection::get()
	 * @param $connectionOptions string of the format: mysql://username[:password]@server[:port]?database
	 */
	public function __construct($connectionOptions = DB_CONNECTION) {
		$this->connectionOptions = parse_url($connectionOptions);
		if (!isset($this->connectionOptions['pass']))
			$this->connectionOptions['pass'] = '';
	}
	
	/**
	 * Closes the database connection. Usually not really neccessary since the
	 * connection would be closed when the script terminates anyway; only interesting
	 * if there are multiple connections.
	 */
	public function __destruct() {
		if ($this->connection)
			mysql_close($this->connection);
	}

	/**
	 * For low-level-queries.
	 * NOTE: you should use this function as rare as possible (depending on how
	 * extensive our database abstraction/ORM will be even NEVER).
	 * Every database query has to use this function.
	 * @return the queries result
	 * @throws Core_Exception if connecting to the database isn't possible
	 * @throws Core_QueryException if the query fails
	 */
	public function query($query) {
		if (!$this->connection) {
			// connect to database server
			$server = isset($this->connectionOptions['port']) ? $this->connectionOptions['host'].':'.$this->connectionOptions['port'] : $this->connectionOptions['host'];
			$this->connection = mysql_connect($server, $this->connectionOptions['user'], $this->connectionOptions['pass'], true);
				
			if (!$this->connection)
				throw new Core_Exception('Can\'t connect to database server: '.mysql_error());
			
			// set active database
			if (!mysql_select_db($this->getDatabaseName(), $this->connection))
				throw new Core_Exception('Can\'t connect to database: '.mysql_error($this->connection));
				
			mysql_query('SET NAMES \'utf8\' COLLATE \'utf8_general_ci\'', $this->connection);
		}
		
		if (defined('CORE_LOG_SLOW_QUERIES'))
			$queryStartTime = microtime(true);
			
		if (defined('CORE_DEBUG_SHOW_QUERIES') && CORE_DEBUG_SHOW_QUERIES)
			dump($query);
			
		$result = mysql_query($query, $this->connection);
		
		if (defined('CORE_LOG_SLOW_QUERIES') && (microtime(true) - $queryStartTime) * 1000 > CORE_LOG_SLOW_QUERIES)
			IO_Log::get()->warning('SLOW QUERY ['.round((microtime(true) - $queryStartTime) * 1000).'ms] '.$query);
		
		if (!$result)
			throw new Core_QueryException('MySQL Query failed: '.mysql_error($this->connection));
			
		return $result;
	}
	
	/**
	 * @return string the name of the database this object is connected to
	 */
	public function getDatabaseName() {
		return $this->connectionOptions['query'];
	}
	
	/**
	 * @return DB_Connection
	 */
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
	
	/**
	 * @return int the ID of the last inserted record
	 */
	public function getLastInsertID() {
		return mysql_insert_id($this->connection);
	}
	
	/**
	 * @return int the number of rows affected by the last query
	 */
	public function getNumberOfAffectedRows() {
		return mysql_affected_rows($this->connection);
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
		if ($this->transactionLevel == 0) {
			$this->query('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
			$this->query('START TRANSACTION');
		}
		else {
			$this->query('SAVEPOINT CORE'.$this->transactionLevel);
		}
		$this->transactionLevel++;
	}
	
	/**
	 * Commits all database operations started since the last beginTransaction()
	 */
	public function commit() {
		if ($this->transactionLevel < 1)
			throw new Core_Exception('There is no open transaction that could be committed.');
		
		$result = false;
		$this->transactionLevel--;
		if ($this->transactionLevel == 0) {
			$result = $this->query('COMMIT');
		}
		else {
			$result = $this->query('RELEASE SAVEPOINT CORE'.$this->transactionLevel);
		}
		return $result;
	}
	
	/**
	 * Commits all open transactions
	 */
	public function commitAll() {
		while ($this->transactionLevel)
			$this->commit();
	}
	
	/**
	 * Drops all database operations started since the last beginTransaction()
	 */
	public function rollback() {
		if ($this->transactionLevel < 1)
			throw new Core_Exception('There is no open transaction that could be rolled back.');
		
		$result = false;
		$this->transactionLevel--;
		if ($this->transactionLevel == 0) {
			$result = $this->query('ROLLBACK');
		}
		else {
			$result = $this->query('ROLLBACK TO SAVEPOINT CORE'.$this->transactionLevel);
		}
		
		// clear caches
		DB_Container::clearAllQueryCaches();
		
		return $result;
	}
	
	/**
	 * Rollbacks all open transactions
	 */
	public function rollbackAll() {
		while ($this->transactionLevel)
			$this->rollback();
	}
}

// -----------------------------------------------------------------------------

/**
 * Thrown if a database operation went wrong
 */
class Core_QueryException extends Core_Exception {
	
}

?>