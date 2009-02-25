<?php
/***
 * Created on 25.02.2009
 **/

class GUI_Control_TextArea extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null, $title = '') {
		parent::__construct($name, $defaultValue, $title);

		$this->setTemplate(dirname(__FILE__).'/textarea.tpl');
		$this->addClasses('core_gui_textarea');
	}
}

?>
