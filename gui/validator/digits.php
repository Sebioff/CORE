<?php

/**
 * Ensures the input consists only of digits.
 */
class GUI_Validator_Digits extends GUI_Validator {
	const INFINITY = PHP_INT_MAX;
	
	private $minValue = 0;
	private $maxValue = self::INFINITY;
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function __construct($minValue = 0, $maxValue = self::INFINITY) {
		$this->minValue = $minValue;
		$this->maxValue = $maxValue;
	}
	
	public function onSetControl() {
		if ($this->maxValue < self::INFINITY)
			$this->control->addValidator(new GUI_Validator_MaxLength(floor(log10($this->maxValue)) + 1));
	}
	
	public function isValid() {
		return ctype_digit((string)$this->control->getValue())
				&& $this->control->getValue() >= $this->minValue
				&& $this->control->getValue() <= $this->maxValue;
	}
	
	public function getError() {
		$errorMessage = 'Darf nur Ziffern beinhalten';
		
		if ($this->minValue > 0 || $this->maxValue < self::INFINITY) {
			$errorMessage .= ' und Zahl muss';
			if ($this->minValue > 0 && $this->maxValue < self::INFINITY)
		 		$errorMessage .= ' zwischen '.$this->minValue.' und '.$this->maxValue.' liegen';
		 	else if ($this->minValue > 0)
		 		$errorMessage .= ' größer als '.$this->minValue.' sein';
		 	else if ($this->maxValue < self::INFINITY)
		 		$errorMessage .= ' kleiner als '.$this->maxValue.' sein';
		}
		
		return $errorMessage;
	}
	
	public function getJs() {
		return array('digits', 'true');
	}
}

?>