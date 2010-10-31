<?php

class Object {
	private $data = array();
	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public function __get($name) {
		return array_key_exists($name, $this->data) ? $this->data[$name] : null;
	}
	
	public function __isset($name) {
		return isset($this->data[$name]);
	}
	
	public function __unset($name) {
		unset($this->data[$name]);
	}
	
	public function getAsArray() {
		return $this->data;
	}
	
	public function merge(Object $obj, $overwrite = true) {
		foreach ($obj->getAsArray() as $key => $val)
			if (!isset($this->$key) || $overwrite)
				$this->$key = $val;
	}
}
?>