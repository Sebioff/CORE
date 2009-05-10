<?php

/**
 * Delegates unknown methods to the currently active module.
 */
class GUI_Panel_Main extends GUI_Panel {
	private $module = null;
	private $pageTitle = '';
	
	public function __construct($name, Module $module) {
		parent::__construct($name);
		
		$this->module = $module;
		$this->setTemplate(dirname(__FILE__).'/main.tpl');
	}
	
	public function beforeDisplay() {
		$this->module->contentPanel->beforeDisplay();
	}	
	
	public function displayPage() {
		$this->module->contentPanel->display();
	}
	
	public function displayContent() {
		require dirname(__FILE__).'/page.tpl';
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setPageTitle($pageTitle) {
		$this->pageTitle = $pageTitle;
	}
	
	public function getPageTitle() {
		return $this->pageTitle;
	}
	
	/**
	 * Delegate unknown functions to the currently active module.
	 */
	public function __call($name, $params) {
		return call_user_func_array(array($this->module, $name), $params);
	}
}

?>