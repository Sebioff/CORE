<?php

class GUI_Panel_Label extends GUI_Panel {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, GUI_Control $control, $caption = '') {
		if(!$caption)
			$caption = $control->getTitle();
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/label.tpl');
		$this->addClasses('core_gui_label');
		
		$this->params->control = $control;
	}
}

?>