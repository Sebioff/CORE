<?php

class GUI_Validator_MaxLength extends GUI_Validator {
	private $maxLength = 0;
	
	public function __construct($maxLength) {
		$this->maxLength = $maxLength;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function onSetControl() {
		$this->control->setAttribute('maxlength', $this->maxLength);
	}
	
	public function isValid() {
		return (Text::length($this->control->getValue()) <= $this->maxLength);
	}
	
	public function getError() {
		return 'Darf nicht länger als '.$this->maxLength.' Zeichen sein';
	}
}

?>