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
 * Ensures the controls value isn't longer than a given length
 */
class GUI_Validator_MaxLength extends GUI_Validator {
	private $maxLength = 0;
	
	public function __construct($maxLength) {
		$this->maxLength = $maxLength;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function onSetControl() {
		if ($this->control instanceof GUI_Control_TextBox
			|| $this->control instanceof GUI_Control_PasswordBox
			|| $this->control instanceof GUI_Control_DigitBox
		)
			$this->control->setAttribute('maxlength', $this->maxLength);
	}
	
	public function isValid() {
		return (Text::length($this->control->getValue()) <= $this->maxLength);
	}
	
	public function getError() {
		return 'Darf nicht länger als '.$this->maxLength.' Zeichen sein (momentan: '.Text::length($this->control->getValue()).')';
	}
	
	public function getJs() {
		return array('maxlength', $this->maxLength);
	}
}

?>