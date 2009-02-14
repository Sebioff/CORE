<?php

abstract class GUI_Control extends GUI_Panel {
	protected $name;
	protected $value;
	
	private $template;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null) {
		$this->name = $name;
		// TODO set value from request / session / defaultValue
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function display() {
		require $this->template;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
}

?>