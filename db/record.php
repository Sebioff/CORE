<?php

class DB_Record {
	private $properties = array();
	private $container = null;
	
	// SETTERS / GETTERS -------------------------------------------------------
	public function setContainer(DB_Container $container) {
		$this->container = $container;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainer() {
		return $this->container;
	}
	
	public function getPK() {
		if (!$this->container)
			return null;
		$databaseSchema = $this->container->getDatabaseSchema();
		return $this->$databaseSchema['primaryKey'];
	}
	
	public function __set($property, $value) {
		$this->properties[$property] = $value;
	}
	
	public function __get($property) {
		if (isset($this->properties[$property]))
			return $this->properties[$property];
		else
			return null;
	}
	
	public function __toString() {
		return $this->id;
	}
	
	/**
	 * @return an associative array containing all set properties
	 */
	public function getAllProperties() {
		return $this->properties;
	}
}

?>