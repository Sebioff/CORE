<?php

/**
 * Ensures the input consists only of digits.
 */
class GUI_Validator_Digits extends GUI_Validator {
	const INFINITY = 999999999999999999;
	
	private $minValue = 0;
	private $maxValue = self::INFINITY;
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function __construct($minValue = 0, $maxValue = self::INFINITY) {
		$this->minValue = $minValue;
		$this->maxValue = $maxValue;
	}
	
	public function isValid() {
		return ctype_digit((string)$this->control->getValue()) 
				&& $this->minValue <= $this->control->getValue()
				&& $this->control->getValue() <= $this->maxValue;
	}
	
	public function getError() {
		return 'Darf nur Ziffern beinhalten und Zahl muss zwischen '.$this->minValue.' und '.$this->maxValue.' liegen';
	}
	
	public function getJs() {
		return array('digits', 'true');
	}
}

?>