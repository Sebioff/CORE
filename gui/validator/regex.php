<?php

/**
 * Ensures the controls value matches a given regex.
 */
class GUI_Validator_Regex extends GUI_Validator {
	private $regex = '';
	private $errorMessage = '';
	
	public function __construct($regex, $errorMessage = '') {
		$this->regex = $regex;
		$this->errorMessage = $errorMessage;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return (preg_match($this->regex, $this->control->getValue()) !== false);
	}
	
	public function getError() {
		if ($this->errorMessage)
			return $this->errorMessage;
		else
			return 'Eingabe ist in falschem Format';
	}
}

?>