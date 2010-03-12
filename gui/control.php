<?php

/**
 * Base class for all controls (elements that interact with the user)
 */
abstract class GUI_Control extends GUI_Panel {
	protected $value;
	protected $defaultValue;
	private $focused = false;
	private $preserveValue = false;
	private $valueHasBeenSet = false;
	
	/** contains all validators of this control */
	private $validators = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultValue = null, $title = '') {
		parent::__construct($name, $title);
		$this->value = $defaultValue;
		$this->defaultValue = $defaultValue;
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function hasValidator($validatorClassName) {
		foreach ($this->validators as $validator)
			if ($validator instanceof $validatorClassName)
				return true;
		return false;
	}
	
	// OVERRIDES ---------------------------------------------------------------
	public function display() {
		if ($this->focused)
			$this->addJS(sprintf('$("#%s").focus();', $this->getID()));

		parent::display();
	}
	
	public function __toString() {
		return (string)$this->getValue();
	}
	
	/**
	 * Executes all validators belonging to this control.
	 * Overwrite this method if you want to check for custom errors.
	 * Must return $this->errors.
	 * @see gui/GUI_Panel#validate()
	 */
	protected function validate() {
		foreach ($this->validators as $validator) {
			if (!$validator->isValid()) {
				$this->errors[] = $validator->getError();
				break;
			}
		}
		
		parent::validate();
		
		return $this->errors;
	}
	
	protected function getJsValidators() {
		$validators = array();
		$messages = array();
		foreach ($this->validators as $validator) {
			if ($jsCode = $validator->getJs()) {
				$validators[] = $jsCode[0].': '.$jsCode[1];
				$messages[] = $jsCode[0].': "'.$validator->getError().'"';
			}
		}
		
		$validatorsString = '';
		if (!empty($validators)) {
			$validators[] = sprintf('messages: {%s}', implode(', ', $messages));
			$validatorsString = sprintf('$("#%s").rules("add", {%s});', $this->getID(), implode(', ', $validators));
		}
		
		$validatorsString .= parent::getJsValidators();
		
		return $validatorsString;
	}
	
	protected function generateID() {
		parent::generateID();
		
		if (isset($_POST[$this->getID()]))
			$this->setValue($_POST[$this->getID()]);
	}
	
	/**
	 * Resets this control and all child controls to their default values
	 */
	public function resetValue() {
		// TODO use lambda-function with PHP 5.3
		$this->walkRecursive(array('GUI_Control', 'resetValueFunction'));
	}
	
	public static function resetValueFunction(GUI_Panel $panel) {
		if ($panel instanceof GUI_Control)
			$panel->setValue($panel->getDefaultValue());
	}
	
	/**
	 * Sets the focus on this control.
	 */
	public function setFocus($focused = true) {
		$this->focused = $focused;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getValue() {
		return $this->value;
	}
	
	public function setValue($value) {
		$this->value = $value;
		$this->valueHasBeenSet = true;
		if ($this->preserveValue)
			$_SESSION['preservedValues'][$this->getID()] = $value;
	}
	
	public function getDefaultValue() {
		return $this->defaultValue;
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
	}
	
	public function addValidator(GUI_Validator $validator) {
		$validator->setControl($this);
		$this->validators[] = $validator;
	}
	
	public function setPreserveValue($preserveValue = true) {
		$this->preserveValue = $preserveValue;
		if ($preserveValue == true) {
			if (!isset($_SESSION['preservedValues'][$this->getID()]) || $this->valueHasBeenSet)
				$_SESSION['preservedValues'][$this->getID()] = $this->getValue();
			elseif (!$this->valueHasBeenSet)
				$this->setValue($_SESSION['preservedValues'][$this->getID()]);
		}
	}
	
	public function getPreserveValue() {
		return $this->preserveValue;
	}
}

?>