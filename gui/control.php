<?php

abstract class GUI_Control extends GUI_Panel {
	private $value;
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
}

?>