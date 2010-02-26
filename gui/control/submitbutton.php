<?php

class GUI_Control_SubmitButton extends GUI_Control_Submittable {
	private $callbacks = array();
	private $clone = false;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption = '', $clone = false) {
		parent::__construct($name, $caption);
		
		$this->setTemplate(dirname(__FILE__).'/submitbutton.tpl');
		$this->addClasses('core_gui_submitbutton');
		
		$callbackName = 'on'.Text::underscoreToCamelCase($this->getName(), true);
		// first, check if parent has submit handler
		if (method_exists($this->getParent(), $callbackName)) {
			$this->addCallback($this->getParent(), $callbackName);
		}
		// if parent hasn't got submit handler, search in call history for submit handler
		// (debug_backtrace() is a bit more expensive and usually checking the parent should be enough)
		else {
			$trace = debug_backtrace();
			$i = 1;
			while (($callingObject = $trace[$i]['object']) == $this)
				$i++;
			if (method_exists($callingObject, $callbackName)) {
				$this->addCallback($callingObject, $callbackName);
			}
		}
		// should this button become cloned to beginning of the form?
		$this->clone = $clone;
	}
	
	public function addCallback($object, $methodName) {
		$this->callbacks[] = array($object, $methodName);
	}
	
	protected function executeCallbacks() {
		if (!$this->hasBeenSubmitted())
			return;
		
		foreach ($this->callbacks as $callback) {
			$callback[0]->$callback[1]();
		}
	}
	
	public function getClone() {
		return (bool)$this->clone;
	}
	
	public function setClone($clone) {
		$this->clone = $clone;
	}
}

?>