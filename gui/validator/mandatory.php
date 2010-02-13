<?php

/**
 * Ensures the controls value isn't empty
 */
class GUI_Validator_Mandatory extends GUI_Validator {
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return (Text::length(trim($this->control->getValue())) > 0);
	}
	
	public function getError() {
		return 'Darf nicht leer sein';
	}
	
	public function getJs() {
		return array('required', 'true');
	}
}

?>