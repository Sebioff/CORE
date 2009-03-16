<?php

class GUI_Control_SubmitButton extends GUI_Control {
	private $callbacks = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '', $title = '') {
		parent::__construct($name, $caption, $title);
		
		$this->setTemplate(dirname(__FILE__).'/submitbutton.tpl');
		$this->addClasses('core_gui_submitbutton');
		
		$trace = debug_backtrace();
		$callingObject = $trace[1]['object'];
		$callbackName = 'on'.ucfirst(Text::underscoreToCamelCase($this->getName()));
		if (method_exists($callingObject, $callbackName))
			$this->addCallback($callingObject, $callbackName);
	}
	
	public function addCallback($object, $methodName) {
		$this->callbacks[] = array($object, $methodName);
	}
	
	protected function executeCallbacks() {
		foreach($this->callbacks as $callback) {
			$callback[0]->$callback[1]();
		}
	}
}

?>