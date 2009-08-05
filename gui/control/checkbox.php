<?php

class GUI_Control_CheckBox extends GUI_Control_Selectable {
	private $group = '';
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $value = '', $checked = false, $title = '') {
		parent::__construct($name, $value, $checked, $title);
		
		$this->setTemplate(dirname(__FILE__).'/checkbox.tpl');
		$this->addClasses('core_gui_checkbox');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getGroup() {
		if ($this->group)
			return $this->group.'[]';
		else
			return $this->getID();
	}
	
	public function setGroup($group) {
		if (!empty($_POST)) {
			if (isset($_POST[$group]) && array_search($this->getValue(), $_POST[$group]) !== false) {
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