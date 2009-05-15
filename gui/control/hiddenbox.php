<?php

class GUI_Control_HiddenBox extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null) {
		parent::__construct($name, $defaultValue);
		
		$this->setTemplate(dirname(__FILE__).'/hiddenbox.tpl');
		$this->addClasses('core_gui_hiddenbox');
	}
}

?>