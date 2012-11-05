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
 * Ensures the controls value matches a given regex.
 */
class GUI_Validator_Regex extends GUI_Validator {
	private $regex = '';
	private $errorMessage = '';
	
	public function __construct($regex, $errorMessage = '') {
		$this->regex = $regex;
		$this->errorMessage = $errorMessage;
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		return (preg_match($this->regex, $this->control->getValue()) !== false);
	}
	
	public function getError() {
		if ($this->errorMessage)
			return $this->errorMessage;
		else
			return 'Eingabe ist in falschem Format';
	}
}

?>