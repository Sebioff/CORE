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
 * Ensures the input consists only of digits.
 */
class GUI_Validator_Digits extends GUI_Validator {
	const INFINITY = PHP_INT_MAX;
	
	private $minValue = 0;
	private $maxValue = self::INFINITY;
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function __construct($minValue = 0, $maxValue = self::INFINITY) {
		$this->minValue = $minValue;
		$this->maxValue = $maxValue;
	}
	
	public function onSetControl() {
		if ($this->maxValue < self::INFINITY) {
			$maxLength = 1;
			// log10(x) for x <= 0 is undefined
			if ($this->maxValue > 0)
				$maxLength = floor(log10($this->maxValue)) + 1;
			$this->control->addValidator(new GUI_Validator_MaxLength($maxLength));
		}
	}
	
	public function isValid() {
		return ctype_digit((string)$this->control->getValue())
				&& $this->control->getValue() >= $this->minValue
				&& $this->control->getValue() <= $this->maxValue;
	}
	
	public function getError() {
		$errorMessage = 'Darf nur Ziffern beinhalten';
		
		if ($this->minValue > 0 || $this->maxValue < self::INFINITY) {
			$errorMessage .= ' und Zahl muss';
			if ($this->minValue > 0 && $this->maxValue < self::INFINITY)
		 		$errorMessage .= ' innerhalb von '.$this->minValue.' und '.$this->maxValue.' liegen';
		 	else if ($this->minValue > 0)
		 		$errorMessage .= ' mindestens '.$this->minValue.' sein';
		 	else if ($this->maxValue < self::INFINITY)
		 		$errorMessage .= ' maximal '.$this->maxValue.' sein';
		}
		
		return $errorMessage;
	}
	
	public function getJs() {
		if ($this->minValue > 0 || $this->maxValue < self::INFINITY) {
			if ($this->minValue > 0 && $this->maxValue < self::INFINITY)
				return array('range', '['.$this->minValue.', '.$this->maxValue.']');
		 	else if ($this->minValue > 0)
		 		return array('min', $this->minValue);
		 	else if ($this->maxValue < self::INFINITY)
		 		return array('max', $this->maxValue);
		}
		return array('digits', 'true');
	}
}

?>