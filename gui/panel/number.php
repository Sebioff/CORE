<?php

class GUI_Panel_Number extends GUI_Panel_Text {
	protected $prefix = '';
	protected $suffix = '';
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getText() {
		return $this->prefix.Text::formatNumber($this->text).$this->suffix;
	}
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function setSuffix($suffix) {
		$this->suffix = $suffix;
	}
}

?>