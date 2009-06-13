<?php

class GUI_Control_RadioButtonList extends GUI_Control {
	private $items = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $title = '') {
		parent::__construct($name, null, $title);
		
		$this->setTemplate(dirname(__FILE__).'/radiobuttonlist.tpl');
		$this->addClasses('core_gui_radiobuttonlist');
	}
	
	public function addItem($title, $value = '', $checked = false) {
		$name = 'item'.count($this->items);
		$this->addItemRadiobutton(new GUI_Control_RadioButton($name, $value, $checked, $title));
	}
	
	public function addItemRadiobutton(GUI_Control_RadioButton $radio) {
		$radio->setGroup($this->getID());
		$this->addPanel($radio);
		$this->items[] = $radio;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getItems() {
		return $this->items;
	}
}

?>