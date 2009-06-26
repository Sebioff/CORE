<?php

class GUI_Panel_Number extends GUI_Panel_Text {
	protected $prefix = '';
	protected $suffix = '';
	// GETTERS / SETTERS -------------------------------------------------------
	public function getText() {
		return $this->prefix.number_format($this->text, is_float($this->text) ? 2 : 0, ',', '.').$this->suffix;
	}
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function setSuffix($suffix) {
		$this->suffix = $suffix;
	}
}

?>