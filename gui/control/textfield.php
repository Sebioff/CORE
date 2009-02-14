<?php

class GUI_Control_Textfield extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name) {
		parent::__construct($name);
		$this->setTemplate(dirname(__FILE__).'/textfield.tpl');
	}
}

?>