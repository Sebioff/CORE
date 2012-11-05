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
 * Ensures the controls value is in a given range of length.
 */
class GUI_Validator_RangeLength extends GUI_Validator {
	private $minLength = 0;
	private $maxLength = 0;
	
	public function __construct($minLength, $maxLength) {
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function onSetControl() {
		$this->control->setAttribute('maxlength', $this->maxLength);
	}
	
	public function isValid() {
		$length = Text::length($this->control->getValue());
		return ($length >= $this->minLength && $length <= $this->maxLength);
	}
	
	public function getError() {
		return 'Muss zwischen '.$this->minLength.' und '.$this->maxLength.' Zeichen lang sein';
	}
	
	public function getJs() {
		return array('rangelength', '['.$this->minLength.', '.$this->maxLength.']');
	}
}

?>