<?php

/**
 * Base class for selectable items like checkboxes or radiobuttons
 */
abstract class GUI_Control_Selectable extends GUI_Control {
	protected $checkedDefaultValue = false;
	protected $checkedValue = false;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $value = '', $checked = false, $title = '') {
		if ($value === '')
			$value = $name;
		parent::__construct($name, $value, $title);
		
		$this->checkedDefaultValue = $checked;
	}
	
	protected function generateID() {
		parent::generateID();
		
		if (!empty($_POST)) {
			if (isset($_POST[$this->getID()])) {
				$this->checkedValue = true;
			}
		}
		else {
			$this->checkedValue = $this->checkedDefaultValue;
		}
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public abstract function getGroup();
	
	public abstract function setGroup($group);
	
	public function getSelected() {
		return $this->checkedValue;
	}
	
	public function setSelected($checked) {
		$this->checkedDefaultValue = $checked;
	}
}

?>