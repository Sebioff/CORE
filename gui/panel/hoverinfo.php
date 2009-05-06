<?php

class GUI_Panel_HoverInfo extends GUI_Panel {
	private $text = '';
	private $hoverText = '';
	
	public function __construct($name, $text, $hoverText) {
		parent::__construct($name);
		
		$this->text = $text;
		$this->hoverText = $hoverText;
		
		$this->setTemplate(dirname(__FILE__).'/hoverinfo.tpl');
		$this->addClasses('core_gui_hoverinfo_panel');
	}
	
	public function beforeDisplay() {
		parent::beforeDisplay();
		
		$module = Router::get()->getCurrentModule();
		$module->addJsRouteReference('core_js', 'panel/hoverinfo.js');
		$module->addJsAfterContent(sprintf('new GUI_Panel_HoverInfo("%s", "%s");', $this->getID(), $this->getHoverText()));
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
}

?>