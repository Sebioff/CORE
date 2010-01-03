<?php

/**
 * Base class for submittable controls
 */
abstract class GUI_Control_Submittable extends GUI_Control {
	/**
	 * NOTE: this method is only reliable AFTER init (-> after all IDs have been
	 * generated) and may return wrong values before.
	 * @see gui/GUI_Panel#hasBeenSubmitted()
	 * @return true if this button has been used, false otherwhise
	 */
	public function hasBeenSubmitted() {
		return isset($_POST[$this->getID()]);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setConfirmationMessage($message) {
		$this->setAttribute('onClick', sprintf('return(confirm(\'%s\'))', $message));
	}
}

?>