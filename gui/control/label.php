<?php

class GUI_Control_Label extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, GUI_Control $control, $caption = '') {
		if(!$caption)
			$caption = $control->getTitle();
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/label.tpl');
		
		$this->params->control = $control;
	}
}

?>