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
 * Ensures that the content of the control this validator is attached to equals
 * the content of the control given in the constructor.
 */
class GUI_Validator_Equals extends GUI_Validator {
	private $equalsControl = null;
	
	public function __construct(GUI_Control $control) {
		$this->equalsControl = $control;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return ($this->equalsControl->getValue() == $this->control->getValue());
	}
	
	public function getError() {
		return 'Muss mit '.$this->equalsControl->getTitle().' übereinstimmen';
	}
	
	public function getJs() {
		return array('equalTo', '"#'.$this->equalsControl->getID().'"');
	}
}

?>