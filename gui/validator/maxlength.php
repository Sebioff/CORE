<?php

/**
 * Ensures the controls value isn't longer than a given length
 */
class GUI_Validator_MaxLength extends GUI_Validator {
	private $maxLength = 0;
	
	public function __construct($maxLength) {
		$this->maxLength = $maxLength;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function onSetControl() {
		if ($this->control instanceof GUI_Control_TextBox
			|| $this->control instanceof GUI_Control_PasswordBox
			|| $this->control instanceof GUI_Control_DigitBox
		)
			$this->control->setAttribute('maxlength', $this->maxLength);
	}
	
	public function isValid() {
		return (Text::length($this->control->getValue()) <= $this->maxLength);
	}
	
	public function getError() {
		return 'Darf nicht länger als '.$this->maxLength.' Zeichen sein (momentan: '.Text::length($this->control->getValue()).')';
	}
	
	public function getJs() {
		return array('maxlength', $this->maxLength);
	}
}

?>