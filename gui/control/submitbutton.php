<?php

class GUI_Control_SubmitButton extends GUI_Control {
	private $callbacks = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '') {
		parent::__construct($name, $caption, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/submitbutton.tpl');
		$this->addClasses('core_gui_submitbutton');
		
		// TODO is using debug_backtrace overkill? probably.
		// check how expensive that function is.
		// alternative solution for backtrace: check if the parent panel has
		// an onXYZSubmit-method (should be enough for most use-cases)
		$trace = debug_backtrace();
		$i = 1;
		while (($callingObject = $trace[$i]['object']) == $this)
			$i++;
		$callbackName = 'on'.Text::underscoreToCamelCase($this->getName(), true);
		if (method_exists($callingObject, $callbackName)) {
			$this->addCallback($callingObject, $callbackName);
		}
	}
	
	public function addCallback($object, $methodName) {
		$this->callbacks[] = array($object, $methodName);
	}
	
	protected function executeCallbacks() {
		if (!isset($_POST[$this->getID()]))
			return;
		
		foreach ($this->callbacks as $callback) {
			$callback[0]->$callback[1]();
		}
	}
}

?>