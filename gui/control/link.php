<?php

class GUI_Control_Link extends GUI_Control_Submittable {
	private $url = '#';
	private $caption = '';
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption, $url, $title = '') {
		parent::__construct($name, $caption, $title);
		
		$this->url = $url;
		$this->caption = $caption;
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
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
	}
}

?>