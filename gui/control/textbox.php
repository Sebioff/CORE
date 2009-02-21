<?php

class GUI_Control_TextBox extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null, $title = '') {
		parent::__construct($name, $defaultValue, $title);
		
		$this->setTemplate(dirname(__FILE__).'/textbox.tpl');
		$this->addClasses('core_gui_textfield');
	}
}

?>