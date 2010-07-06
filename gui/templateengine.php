<?php

class GUI_TemplateEngine {
	private $_params = array();
	private $objectContext = null;
	
	public function __get($value) {
		if (array_key_exists($value, $this->_params))
			return $this->_params[$value];
		else
			throw new Core_Exception('Template parameter value does not exist: '.$value);
	}
	
	public function __set($key, $value) {
		return $this->_params[$key] = $value;
	}
	
	public function __isset($value) {
		return isset($this->_params[$value]);
	}
	
	public function __unset($value) {
		unset($this->_params[$value]);
	}
	
	public function __call($name, $arguments) {
		if ($this->objectContext && method_exists($this->objectContext, $name))
			return call_user_method_array($name, $this->objectContext, $arguments);
		else
			throw new Core_Exception('Template method does not exist: '.$name);
	}
	
	public function render($template) {
		extract($this->_params);
		ob_start();
		require $template;
		return ob_get_clean();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @param $objectContext object the methods of this class get called if calling
	 * a method from within the template.
	 */
	public function setObjectContext($objectContext) {
		$this->objectContext = $objectContext;
	}
}
      

?>