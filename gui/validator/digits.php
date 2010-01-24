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
		if ($this->maxValue < self::INFINITY) {
			$maxLength = 1;
			// log10(x) for x <= 0 is undefined
			if ($this->maxValue > 0)
				$maxLength = floor(log10($this->maxValue)) + 1;
			$this->control->addValidator(new GUI_Validator_MaxLength($maxLength));
		}
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
		 		$errorMessage .= ' innerhalb von '.$this->minValue.' und '.$this->maxValue.' liegen';
		 	else if ($this->minValue > 0)
		 		$errorMessage .= ' mindestens '.$this->minValue.' sein';
		 	else if ($this->maxValue < self::INFINITY)
		 		$errorMessage .= ' maximal '.$this->maxValue.' sein';
		}
		
		return $errorMessage;
	}
	
	public function getJs() {
		return array('digits', 'true');
	}
}

?>