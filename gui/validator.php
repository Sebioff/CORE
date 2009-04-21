<?php

abstract class GUI_Validator {
	protected $control = null;
	
	public abstract function isValid();
	public abstract function getError();
	
	public function getJs() {
		// callback
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setControl(GUI_Control $control) {
		$this->control = $control;
		$this->onSetControl();
	}
	
	// CALLBACKS ---------------------------------------------------------------
	protected function onSetControl() {
		// callback
	}
}

?>