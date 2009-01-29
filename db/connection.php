<?php

/**
 * Encapsulates a connection to the database.
 */
class COREDBConnection {
	private $connection = null;

	public function query($query) {
		if (!$this->connection) {
			$this->connection = mysql_connect(/* TODO find some way to get the connection data from config.php */);
		}

		mysql_query($query, $this->connection);
	}
}

?>