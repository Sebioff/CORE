<?php

abstract class GUI_Validator {
	protected $control = null;
	
	public abstract function isValid();
	public abstract function getError();
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setControl(GUI_Control $control) {
		$this->control = $control;
	}
}

?>