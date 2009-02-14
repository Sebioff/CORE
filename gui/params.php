<?php

class GUI_Params {
	private $params = array();
	
	public function __get($value) {
		if(array_key_exists($value, $this->params))
			return $this->params[$value];
		else
			throw new CORE_Exception('Param value does not exist: '.$value);
	}
	
	public function __set($key, $value) {
		return $this->params[$key] = $value;
	}
}

?>