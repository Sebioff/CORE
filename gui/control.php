<?php

abstract class GUI_Control extends GUI_Panel {
	protected $name;
	protected $value;
	
	private $template;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name) {
		$this->name = $name;
	}
	
	// CUSTOM METHODS ----------------------------------------------------------	
	public function display() {
		require $this->template;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
}

?>