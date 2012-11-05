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

class GUI_TemplateEngine {
	private $_params = array();
	private $objectContext = null;
	
	public function __get($value) {
		if (array_key_exists($value, $this->_params))
			return $this->_params[$value];
		else
			throw new Core_Exception('Template parameter value does not exist: '.$value);
	}
	
	public function __set($key, $value) {
		return $this->_params[$key] = $value;
	}
	
	public function __isset($value) {
		return isset($this->_params[$value]);
	}
	
	public function __unset($value) {
		unset($this->_params[$value]);
	}
	
	public function __call($name, $arguments) {
		if ($this->objectContext && method_exists($this->objectContext, $name))
			return call_user_method_array($name, $this->objectContext, $arguments);
		else
			throw new Core_Exception('Template method does not exist: '.$name);
	}
	
	public function render($template) {
		extract($this->_params);
		ob_start();
		require $template;
		return ob_get_clean();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @param $objectContext object the methods of this class get called if calling
	 * a method from within the template.
	 */
	public function setObjectContext($objectContext) {
		$this->objectContext = $objectContext;
	}
}
      

?>