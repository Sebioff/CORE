<?php

abstract class GUI_Control extends GUI_Panel {
	protected $value;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null, $title = '') {
		parent::__construct($name, $title);
		$this->value = $defaultValue;
		// TODO set value from request / session / defaultValue
	}
	
	// OVERRIDES ---------------------------------------------------------------
	public function display() {
		require $this->template;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getValue() {
		return $this->value;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
}

?>