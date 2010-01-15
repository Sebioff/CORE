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
		if (Router::get()->getRequestMode() != Router::REQUESTMODE_AJAX) {
			require dirname(__FILE__).'/page.tpl';
		}
		else {
			require dirname(__FILE__).'/page_ajax.tpl';
		}
	}
	
	/**
	 * Displays the content of a page that is loaded via Ajax
	 * -> displays only specific panels
	 */
	public function displayContentAjax() {
		$panels = explode(',', $_POST['refreshPanels']);
		$panelsCount = count($panels);
		for ($i = 0; $i < $panelsCount; $i++) {
			$panelTree = explode('-', $panels[$i]);
			$currentPanel = $this;
			$panelTreeCount = count($panelTree);
			for ($j = 1; $j < $panelTreeCount; $j++) {
				if (!$currentPanel->hasPanel($panelTree[$j])) {
					break;
				}
				
				$currentPanel = $currentPanel->{$panelTree[$j]};
			}
			
			if ($currentPanel->getID() == $panels[$i]) {
				echo $currentPanel->display();
			}
		}
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