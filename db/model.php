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
 * Model part of the model-view-controler pattern.
 * You might need a model for certain tasks, e.g. keeping different GUI-parts on one
 * page up-to-date while the data changes. This class automatically provides models
 * for any record.
 */
class DB_Model extends DB_Record {
	private static $records = array();
	public $record = null;
	private $attribute = '';
	
	private function __construct(DB_Record $record) {
		$this->record = $record;
	}
	
	public function setAttribute($attribute) {
		$this->attribute = $attribute;
	}
	
	public function __set($property, $value) {
		$this->record->__set($property, $value);
	}
	
	public function __get($property) {
		return $this->record->__get($property);
	}
	
	public function __isset($property) {
		return $this->record->__isset($property);
	}
	
	public function __unset($property) {
		$this->record->__unset($property);
	}
	
	public function __call($name, $params) {
		return call_user_func_array(array($this->record, $name), $params);
	}
	
	public function __toString() {
		if ($this->attribute) {
			return (string)$this->record->{$this->attribute};
		}
		else {
			return '';
		}
	}
	
	/**
	 * Actually returns DB_Model, but DB_Model is just a facade for DB_Container
	 * @return DB_Record
	 */
	public static function getModelForRecord(DB_Record $record, $attribute = '') {
		$modelKey = $record->getContainer()->getTable().'_pk'.$record->getPK();
		if (!isset(self::$records[$modelKey])) {
			self::$records[$modelKey] = new self($record);
		}
		
		$model = clone self::$records[$modelKey];
		$model->setAttribute($attribute);
		return $model;
	}
}

?>