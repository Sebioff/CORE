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