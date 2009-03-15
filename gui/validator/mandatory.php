<?php

class GUI_Validator_Mandatory extends GUI_Validator {
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return ($this->control->getValue() != null);
	}
	
	public function getError() {
		return 'Darf nicht leer sein';
	}
}

?>