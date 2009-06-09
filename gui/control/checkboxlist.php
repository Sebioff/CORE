<?php

class GUI_Control_CheckBoxList extends GUI_Control {
	private $items = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $title = '') {
		parent::__construct($name, null, $title);
		
		$this->setTemplate(dirname(__FILE__).'/checkboxlist.tpl');
		$this->addClasses('core_gui_checkboxlist');
	}
	
	public function addItem($title, $value = '', $checked = false) {
		$name = 'item'.count($this->items);
		$this->addItemCheckbox(new GUI_Control_CheckBox($name, $value, $checked, $title));
	}
	
	public function addItemCheckbox(GUI_Control_CheckBox $checkbox) {
		$checkbox->setGroup($this->getID());
		$this->addPanel($checkbox);
		$this->items[] = $checkbox;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getItems() {
		return $this->items;
	}
}

?>