<?php

/**
 * Base class for submittable controls
 */
abstract class GUI_Control_Submittable extends GUI_Control {
	// GETTERS / SETTERS -------------------------------------------------------
	public function setConfirmationMessage($message) {
		$this->setAttribute('onClick', sprintf('return(confirm(\'%s\'))', $message));
	}
}

?>