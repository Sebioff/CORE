<?php

class GUI_Panel_List extends GUI_Panel {
	private $items = array();
	
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/list.tpl');
	}
	
	public function addItem($item) {
		$this->items[] = $item;
	}
	
	public function getItems() {
		return $this->items;
	}
}
?>