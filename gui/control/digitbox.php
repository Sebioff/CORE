<?php

/**
 * Accepts only digits.
 * NOTE: It's called a _DIGIT_ box since something like -42 is not allowed.
 */
class GUI_Control_DigitBox extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = 0, $title = '', $minValue = 0, $maxValue = GUI_Validator_Digits::INFINITY) {
		parent::__construct($name, $defaultValue, $title);

		$this->setTemplate(dirname(__FILE__).'/textbox.tpl');
		$this->addValidator(new GUI_Validator_Digits($minValue, $maxValue));
		$this->addClasses('core_gui_digitbox');
	}
	
	public function getValue() {
		return (int)parent::getValue();
	}
}

?>