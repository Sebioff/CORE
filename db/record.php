<?php

class DB_Record
{
	private $properties = array();
	
	// SETTERS / GETTERS -------------------------------------------------------
	public function __set($property, $value) {
		$this->properties[$property] = $value;
	}
	
	public function __get($property)
	{
		if(isset($this->properties[$property]))
			return $this->properties[$property];
		else
			return null;
	}

	/**
	 * @return an associative array containing all set properties
	 */
	public function getAllProperties()
	{
		return $this->properties;
	}
}

?>