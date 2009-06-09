<?php

class GUI_Control_CheckBox extends GUI_Control {
	private $group = '';
	private $checkedDefaultValue = false;
	private $checkedValue = false;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $value = '', $checked = false, $title = '') {
		if ($value === '')
			$value = $name;
		parent::__construct($name, $value, $title);
		
		$this->checkedDefaultValue = $checked;
		$this->setTemplate(dirname(__FILE__).'/checkbox.tpl');
		$this->addClasses('core_gui_checkbox');
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
	
	public function getChecked() {
		return $this->checkedValue;
	}
	
	public function setChecked($checked) {
		$this->checkedDefaultValue = $checked;
	}
}

?>