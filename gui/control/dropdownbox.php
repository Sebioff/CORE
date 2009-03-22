<?php

class GUI_Control_DropDownBox extends GUI_Control {
	private $values = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $values, $defaultValue = null, $title = '') {
		parent::__construct($name, $defaultValue, $title);

		$this->values = $values;
		
		$this->setTemplate(dirname(__FILE__).'/dropdownbox.tpl');
		$this->addClasses('core_gui_dropdownbox');
	}
	
	public function getValues() {
		return $this->values;
	}
	
	public function getValue() {
		return $this->values[$this->getKey()];
	}
	
	public function getKey() {
		return $this->value;
	}
}

?>