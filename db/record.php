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
		if ($value === 'NULL')
			$value = null;
		// we don't want to allow having other objects than records set
		// (records get transformed automatically, normal objects don't always)
		if (is_object($value) && !($value instanceof DB_Record))
			$value = $value->__toString();
		$this->properties[$property] = $value;
	}
	
	public function __get($property) {
		if (isset($this->properties[$property])) {
			// handle foreign keys
			if ($this->hasForeignKey($property) && $this->properties[$property] != null && !is_object($this->properties[$property])) {
				$databaseSchema = $this->container->getDatabaseSchema();
				$reference = $databaseSchema['constraints'][$property];

				$container = new DB_Container($reference['referencedTable']);
				$this->properties[$property] = $container->{'selectBy'.Text::underscoreToCamelCase($reference['referencedColumn'], true).'First'}($this->properties[$property]);
			}			
			return $this->properties[$property];
		}
		else
			return null;
	}
	
	/**
	 * Saves the record in the container it was created from.
	 */
	public function save() {
		$this->getContainer()->save($this);
	}
	
	public function __toString() {
		return (string)$this->getPK();
	}
	
	/**
	 * @return an associative array containing all set properties
	 */
	public function getAllProperties() {
		return $this->properties;
	}
	
	private function hasForeignKey($property) {
		if (!$this->container)
			return null;
		$databaseSchema = $this->container->getDatabaseSchema();

		if (isset($databaseSchema['constraints'][$property]) && $databaseSchema['constraints'][$property]['type'] == 'foreignKey')
			return true;
		else
			return false;
	}
}

?>