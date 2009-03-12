<?php

class GUI_Panel_Main extends GUI_Panel {
	private $module = null;
	private $pageTitle = '';
	
	public function __construct($name, Module $module) {
		parent::__construct($name);
		
		$this->module = $module;
		$this->setTemplate(dirname(__FILE__).'/main.tpl');
	}
	
	public function displayPage() {
		$this->module->contentPanel->display();
	}
	
	public function render() {
		require dirname(__FILE__).'/page.tpl';
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setPageTitle($pageTitle) {
		$this->pageTitle = $pageTitle;
	}
	
	public function getPageTitle() {
		return $this->pageTitle;
	}
}

?>