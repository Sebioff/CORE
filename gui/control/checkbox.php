<?php

class GUI_Control_CheckBox extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = false, $title = '') {
		parent::__construct($name, $defaultValue, $title);
		
		$this->setTemplate(dirname(__FILE__).'/checkbox.tpl');
		$this->addClasses('core_gui_checkbox');
	}
}

?>