<?php

class GUI_Control_DatePicker extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultDateTime = 0, $title = '') {
		parent::__construct($name, $defaultDateTime, $title);
		
		$this->setTemplate(dirname(__FILE__).'/datepicker.tpl');
		$this->addClasses('core_gui_datepicker');
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function afterInit() {
		parent::afterInit();
		
		$this->getModule()->addJsRouteReference('core_js', 'jquery/jquery-ui.js');
		$this->getModule()->addCssRouteReference('core_js', 'jquery/css/smoothness/jquery-ui.css');
		$this->addJS(
			sprintf('
				$(function() {
					$("#%s").datepicker({dateFormat: "dd.mm.yy", firstDay: 1});
				});
			', $this->getID())
		);
	}
	
	public function getValue() {
		$strValue = parent::getValue();
		if ($strValue) {
			$parts = explode('.', $strValue);
			return mktime(0, 0, 0, $parts[1], $parts[0], $parts[2]);
		}
		else
			return $strValue;
	}
}

?>