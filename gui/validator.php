<?php

/**
 * Can be attached to a panel in order to validate it.
 * This way you can easily plug-in validators for common tasks like required
 * fields or fields that contain only digits.
 */
abstract class GUI_Validator {
	protected $control = null;
	
	/**
	 * Implement this for server-side validaton.
	 * @return boolean true if validation succeeds, false otherwise.
	 */
	public abstract function isValid();
	
	/**
	 * @return string the error message attached to this validators control if
	 * validation fails.
	 */
	public abstract function getError();
	
	/**
	 * @return array parameters for the jQuery Validation plugin
	 */
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