<?php

class GUI_Validator_MinLength extends GUI_Validator {
	private $minLength = 0;
	
	public function __construct($minLength) {
		$this->minLength = $minLength;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function onSetControl() {
		$this->control->setAttribute('minlength', $this->minLength);
	}
	
	public function isValid() {
		return (Text::length($this->control->getValue()) >= $this->minLength);
	}
	
	public function getError() {
		return 'Muss mindestens '.$this->minLength.' Zeichen lang sein';
	}
}

?>