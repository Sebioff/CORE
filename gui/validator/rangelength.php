<?php

class GUI_Validator_RangeLength extends GUI_Validator {
	private $minLength = 0;
	private $maxLength = 0;
	
	public function __construct($minLength, $maxLength) {
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function onSetControl() {
		$this->control->setAttribute('maxlength', $this->maxLength);
	}
	
	public function isValid() {
		$length = Text::length($this->control->getValue());
		return ($length >= $this->minLength && $length <= $this->maxLength);
	}
	
	public function getError() {
		return 'Muss zwischen '.$this->minLength.' und '.$this->maxLength.' Zeichen lang sein';
	}
	
	public function getJs() {
		return array('rangelength', '['.$this->minLength.', '.$this->maxLength.']');
	}
}

?>