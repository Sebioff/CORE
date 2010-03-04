<?php

class GUI_Panel_List extends GUI_Panel {
	private $items = array();
	
	public function __construct($name, $title = '') {
		parent::__construct($name, $title);
		
		$this->setTemplate(dirname(__FILE__).'/list.tpl');
	}
	
	public function addItem($item) {
		$this->items[] = $item;
		//just du call the init-methods and so on...
		if ($item instanceof GUI_Panel)
			$this->addPanel($item);
	}
	
	public function getItems() {
		return $this->items;
	}
}
?>