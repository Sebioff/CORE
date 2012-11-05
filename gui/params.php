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
 * Class for easy handing over values to the template.
 */
class GUI_Params {
	private $params = array();
	
	public function __get($value) {
		if (array_key_exists($value, $this->params))
			return $this->params[$value];
		else
			throw new Core_Exception('Param value does not exist: '.$value);
	}
	
	public function __set($key, $value) {
		return $this->params[$key] = $value;
	}
	
	public function __isset($value) {
		return isset($this->params[$value]);
	}
	
	public function __unset($value) {
		unset($this->params[$value]);
	}
}
      
?>