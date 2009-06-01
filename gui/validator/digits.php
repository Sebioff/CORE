<?php

/**
 * Ensures the input consists only of digits.
 */
class GUI_Validator_Digits extends GUI_Validator {
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return ctype_digit((string)$this->control->getValue());
	}
	
	public function getError() {
		return 'Darf nur Ziffern beinhalten';
	}
	
	public function getJs() {
		return array('digits', 'true');
	}
}

?>