<?php

class GUI_Validator_Mail extends GUI_Validator {
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $this->control->getValue());
	}
	
	public function getError() {
		return 'Keine gültige EMail-Adresse';
	}
	
	public function getJs() {
		return array('email', 'true');
	}
}

?>