<?php

class GUI_Control_Link extends GUI_Control {
	private $url = '';
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption, $url) {
		parent::__construct($name, null, $caption);
		$this->url = $url;
		
		$this->setTemplate(dirname(__FILE__).'/link.tpl');
		$this->addClasses('core_gui_link');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
}

?>