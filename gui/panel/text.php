<?php

class GUI_Panel_Text extends GUI_Panel {
	private $text = '';
	
	public function __construct($name, $text, $title = '') {
		parent::__construct($name, $title);
		
		$this->text = $text;
		$this->setTemplate(dirname(__FILE__).'/text.tpl');
		$this->addClasses('core_gui_text');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getText() {
		return $this->text;
	}
}

?>