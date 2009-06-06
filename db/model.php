<?php

/**
 * Model part of the model-view-controler pattern.
 * You might need a model for certain tasks, e.g. keeping different GUI-parts on one
 * page up-to-date while the data changes. This class automatically provides models
 * for any record.
 */
class DB_Model {
	private static $models = array();
	private $record = null;
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
		return $this->record->_isset($property);
	}
	
	public function __unset($property) {
		$this->record->__unset($property);
	}
	
	public function __call($name, $params) {
		return call_user_func_array(array($this->record, $name), $params);
	}
	
	public function __toString() {
		if ($this->attribute)
			return (string)$this->record->{$this->attribute};
		else
			return '';
	}
	
	/**
	 * Actually returns DB_Model, but DB_Model is just a facade for DB_Container
	 * @return DB_Record
	 */
	public static function getModelForRecord(DB_Record $record, $attribute = '') {
		$modelKey = $record->getContainer()->getTable().'_pk'.$record->getPK();
		if (!isset($models[$modelKey]))
			$models[$modelKey] = new self($record);
		
		$models[$modelKey]->setAttribute($attribute);
		return $models[$modelKey];
	}
}

?>