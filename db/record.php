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
 * DB_Record is an abstraction for a row in the database
 */
class DB_Record {
	private $properties = array();
	private $resolvedProperties = array();
	private $virtualProperties = array();
	private $modifiedProperties = array();
	private $container = null;
	
	/**
	 * @return int the primary key of this record
	 */
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
		if (is_object($value)) {
			if ($value instanceof DB_Record) {
				$this->resolvedProperties[$property] = $value;
				$value = $value->getPK();
			}
			else {
				$value = $value->__toString();
			}
		}
		
		// mark property as modified
		if (array_key_exists($property, $this->properties) && !array_key_exists($property, $this->modifiedProperties) && $value != $this->properties[$property]) {
			$this->modifiedProperties[$property] = $this->properties[$property];
		}
		
		// update property value
		$this->properties[$property] = $value;
	}
	
	public function __get($property) {
		if (array_key_exists($property, $this->properties)) {
			// resolved property?
			if (array_key_exists($property, $this->resolvedProperties)) {
				return $this->resolvedProperties[$property];
			}
			// unresolved property that needs to be resolved?
			elseif ($this->hasForeignKey($property)) {
				// resolve property
				$databaseSchema = $this->container->getDatabaseSchema();
				$reference = $databaseSchema['constraints'][$property];

				if (isset($reference['referencedContainer'])) {
					$container = $reference['referencedContainer'];
				}
				else {
					$container = new DB_Container($reference['referencedTable']);
					$container->setConnection($this->container->getConnection());
				}
				$this->resolvedProperties[$property] = $container->{'selectBy'.Text::underscoreToCamelCase($reference['referencedColumn'], true).'First'}($this->properties[$property]);
				
				return $this->resolvedProperties[$property];
			}
			// no foreign keys, just a plain old normal property
			else {
				return $this->properties[$property];
			}
		}
		// property not defined - let's check if it's a virtual property...
		elseif ($this->hasVirtualProperty($property))
			return $this->getVirtualProperty($property);
		else
			return null;
	}
	
	/**
	 * @param $property string the name of the property to get (in camel case notation)
	 * @return the plain value of the given property, without resolving any references
	 * or null if the property doesn't exist
	 */
	public function get($property) {
		if (isset($this->properties[$property]))
			return $this->properties[$property];
		else
			return null;
	}
	
	public function __isset($property) {
		if (isset($this->properties[$property]))
			return true;
			
		if ($this->hasVirtualProperty($property) && $this->getVirtualProperty($property) !== null)
			return true;
		
		return false;
	}
	
	public function __unset($property) {
		unset($this->properties[$property]);
	}
	
	/**
	 * Saves the record in the container it was created from.
	 * Shortcut for Container::save($record)
	 */
	public function save() {
		$this->getContainer()->save($this);
	}
	
	/**
	 * Removes the record from its container.
	 * Shortcut for Container::delete($record)
	 */
	public function delete() {
		$this->getContainer()->delete($this);
	}
	
	public final function __toString() {
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
	
	public function hasVirtualProperty($property) {
		return isset($this->virtualProperties[$property]);
	}
	
	private function getVirtualProperty($property) {
		return call_user_func($this->virtualProperties[$property], $this);
	}
	
	/**
	 * A virtual property is just like a normal property, except that it doesn't
	 * actually exist in the database.
	 * @param $property the name of the property you want to define
	 * @param $callback a callback that returns the value of your property. The
	 * callback receives the record as first parameter.
	 */
	public function setVirtualProperty($property, $callback) {
		$this->virtualProperties[Text::underscoreToCamelCase($property)] = $callback;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return DB_Container
	 */
	public function getContainer() {
		return $this->container;
	}
	
	public function setContainer(DB_Container $container) {
		$this->container = $container;
	}
	
	/**
	 * @return array containing all properties that have been modified since the
	 * record has been read from the database ($popertyName => $originalValue)
	 */
	public function &getModifiedProperties() {
		return $this->modifiedProperties;
	}
}

?>