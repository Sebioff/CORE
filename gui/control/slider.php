<?php

class GUI_Control_Slider extends GUI_Control {
	
	public function init() {
		parent::init();
		
		$this->setTemplate(dirname(__FILE__).'/slider.tpl');
		$this->getModule()->addJsRouteReference('core_js', '/jquery/jquery-ui.js');
		$this->getModule()->addCssRouteReference('jquery_css', '/smoothness/jquery-ui.css');
	}
	
	public function afterInit() {
		parent::afterInit();
		
		$this->addJS('$().ready(function(){$(\'#'.$this->getID().'\').slider();});');
	}
}
?>