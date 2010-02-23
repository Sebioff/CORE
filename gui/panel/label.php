<?php

class GUI_Panel_Label extends GUI_Panel {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, GUI_Panel $panel, $caption = '') {
		if (!$caption)
			$caption = $panel->getTitle();
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/label.tpl');
		$this->addClasses('core_gui_label');
		
		$this->params->panel = $panel;
	}
}

?>