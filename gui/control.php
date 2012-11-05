<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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
	/**
	 * @return boolean true if this control has a validator instance of the given
	 * class name, false otherwise.
	 */
	public function hasValidator($validatorClassName) {
		foreach ($this->validators as $validator)
			if ($validator instanceof $validatorClassName)
				return true;
		return false;
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
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
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
			/*
			 * We can't apply rules to non-existing DOM elements without getting
			 * an error as of jQuery 1.4.3, jQuery Validate 1.7, so we have to check
			 * if the element actually exists (on the client side, no satisfactory
			 * way to do so on the server side atm).
			 */
			$validatorsString = sprintf('$("#%s").each(function(){$(this).rules("add", {%s})});', $this->getID(), implode(', ', $validators));
		}
		
		$validatorsString .= parent::getJsValidators();
		
		return $validatorsString;
	}
	
	protected function generateID() {
		parent::generateID();
		
		if (isset($_POST[$this->getID()]))
			$this->setValue($_POST[$this->getID()]);
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
	
	/**
	 * If preserving values is enabled, the value of this control is stored in
	 * session and will be restored on new page loads.
	 * @param $preserveValue boolean true if values should be preserved
	 */
	public function setPreserveValue($preserveValue = true) {
		$this->preserveValue = $preserveValue;
		if ($preserveValue == true) {
			if (!isset($_SESSION['preservedValues'][$this->getID()]) || $this->valueHasBeenSet)
				$_SESSION['preservedValues'][$this->getID()] = $this->getValue();
			elseif (!$this->valueHasBeenSet)
				$this->setValue($_SESSION['preservedValues'][$this->getID()]);
		}
	}
	
	/**
	 * @return boolean true if preserving value is enabled, false otherwise
	 */
	public function getPreserveValue() {
		return $this->preserveValue;
	}
}

?>