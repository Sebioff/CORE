<?php

class GUI_Control_RadioButton extends GUI_Control_Selectable {
	private $group = '';
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $value = '', $checked = false, $title = '') {
		parent::__construct($name, $value, $checked, $title);

		$this->setTemplate(dirname(__FILE__).'/radiobutton.tpl');
		$this->addClasses('core_gui_radiobutton');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getGroup() {
		if ($this->group)
			return $this->group;
		else
			return $this->getID();
	}
	
	public function setGroup($group) {
		if (!empty($_POST)) {
			if (isset($_POST[$group]) && $this->getValue() == $_POST[$group]) {
				$this->checkedValue = true;
			}
		}
		else {
			$this->checkedValue = $this->checkedDefaultValue;
		}
		
		$this->group = $group;
	}
}

?>