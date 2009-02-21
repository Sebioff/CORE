<?php

abstract class GUI_Control extends GUI_Panel {
	protected $value;
	
	/** contains all validators of this control */
	private $validators = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null, $title = '') {
		parent::__construct($name, $title);
		$this->value = $defaultValue;
	}
	
	// OVERRIDES ---------------------------------------------------------------
	public function display() {
		require $this->template;
	}
	
	protected function validate() {
		foreach($this->validators as $validator) {
			if(!$validator->isValid()) {
				$this->errors[] = $validator->getError();
			}
		}
		
		parent::validate();
		
		return $this->errors;
	}
	
	protected function generateID() {
		parent::generateID();
		
		if(isset($_POST[$this->getID()]))
			$this->value = $_POST[$this->getID()];
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getValue() {
		return $this->value;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function addValidator(GUI_Validator $validator) {
		$validator->setControl($this);
		$this->validators[] = $validator;
	}
}

?>