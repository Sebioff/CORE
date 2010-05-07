<?php

class GUI_Control_SubmitButton extends GUI_Control_Submittable {
	private $callbacks = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '') {
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/submitbutton.tpl');
		$this->addClasses('core_gui_submitbutton');
		
		$callbackName = 'on'.Text::underscoreToCamelCase($this->getName(), true);
		// first, check if parent has submit handler
		if (method_exists($this->getParent(), $callbackName)) {
			$this->addCallback(array($this->getParent(), $callbackName));
		}
		// if parent hasn't got submit handler, search in call history for submit handler
		// (debug_backtrace() is a bit more expensive and usually checking the parent should be enough)
		else {
			$trace = debug_backtrace();
			$i = 1;
			while (($callingObject = $trace[$i]['object']) == $this)
				$i++;
			if (method_exists($callingObject, $callbackName)) {
				$this->addCallback(array($callingObject, $callbackName));
			}
		}
	}
	
	public function addCallback($callback, array $arguments = array()) {
		$this->callbacks[] = array($callback, $arguments);
	}
	
	protected function executeCallbacks() {
		if (!$this->hasBeenSubmitted())
			return;
		
		foreach ($this->callbacks as $callback) {
			call_user_func_array($callback[0], $callback[1]);
		}
	}
}

?>