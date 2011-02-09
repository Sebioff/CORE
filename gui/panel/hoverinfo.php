<?php

class GUI_Panel_HoverInfo extends GUI_Panel {
	private $text = '';
	private $hoverText = '';
	private $enableLocking = false;
	private $enableAjax = false;
	private $ajaxCallback = array();
	
	public function __construct($name, $text, $hoverText) {
		parent::__construct($name);
		
		$this->text = $text;
		$this->hoverText = $hoverText;
		
		$this->setTemplate(dirname(__FILE__).'/hoverinfo.tpl');
		$this->addClasses('core_gui_hoverinfo_panel');
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$module = Router::get()->getCurrentModule();
		$module->addJsRouteReference('core_js', 'panel/hoverinfo.js');
		$this->addJS(sprintf('new GUI_Panel_HoverInfo("%s", "%s", "%s", "%s");', $this->getID(), $this->getHoverText(), $this->enableLocking, $this->enableAjax ? $this->getAjaxID() : ''));
	}
	
	public function __toString() {
		$this->beforeDisplay();
		return $this->render();
	}
	
	public function ajaxOnHover() {
		return call_user_func($this->ajaxCallback);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setText($text) {
		$this->text = $text;
	}
	
	public function getText() {
		return $this->text;
	}
	
	public function setHoverText($hoverText) {
		$this->hoverText = $hoverText;
	}
	
	public function getHoverText() {
		return $this->hoverText;
	}
	
	public function enableAjax($enableAjax, array $ajaxCallback = array()) {
		if ($enableAjax && !$ajaxCallback)
			throw new Core_Exception('A callback is needed for enabling ajax');
		$this->enableAjax = $enableAjax;
		$this->ajaxCallback = $ajaxCallback;
	}
	
	public function enableLocking($enableLocking = true) {
		$this->enableLocking = $enableLocking;
	}
}

?>