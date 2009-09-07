<?php

class GUI_Panel_Number extends GUI_Panel_Text {
	const DECIMALS = 2;
	const DECIMALS_SEPARATOR = ',';
	const THOUSANDS_SEPARATOR = '.';
	
	protected $prefix = '';
	protected $suffix = '';
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getText() {
		return $this->prefix.number_format($this->text, is_float($this->text) ? self::DECIMALS : 0, self::DECIMALS_SEPARATOR, self::THOUSANDS_SEPARATOR).$this->suffix;
	}
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function setSuffix($suffix) {
		$this->suffix = $suffix;
	}
}

?>