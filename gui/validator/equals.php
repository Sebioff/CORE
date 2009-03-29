<?php

class GUI_Validator_Equals extends GUI_Validator {
	private $equalsControl = null;
	
	public function __construct(GUI_Control $control) {
		$this->equalsControl = $control;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return ($this->equalsControl->getValue() == $this->control->getValue());
	}
	
	public function getError() {
		return 'Muss mit '.$this->equalsControl->getTitle().' übereinstimmen';
	}
	
	public function getJs() {
		return array('equalTo', '"#'.$this->equalsControl->getID().'"');
	}
}

?>