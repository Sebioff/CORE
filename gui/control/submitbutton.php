<?php

class GUI_Control_SubmitButton extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '', $title = '') {
		parent::__construct($name, $caption, $title);
		
		$this->setTemplate(dirname(__FILE__).'/submitbutton.tpl');
		$this->addClasses('core_gui_submitbutton');
	}
}

?>