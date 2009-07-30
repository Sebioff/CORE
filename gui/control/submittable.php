<?php

/**
 * Base class for submittable controls
 */
abstract class GUI_Control_Submittable extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '', $title = '') {
		parent::__construct($name, $caption, $title);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setConfirmationMessage($message) {
		$this->setAttribute('onClick', sprintf('return(confirm(\'%s\'))', $message));
	}
}

?>