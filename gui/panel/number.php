<?php

class GUI_Panel_Number extends GUI_Panel_Text {
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getText() {
		return number_format($this->text, is_float($this->text) ? 2 : 0, ',', '.');
	}
}

?>