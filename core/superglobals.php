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
 * Restricts access to superglobals
 */
final class Core_SuperGlobals extends ArrayObject {
	const SUPERGLOBAL_GET = '_GET';
	const SUPERGLOBAL_POST = '_POST';
	const SUPERGLOBAL_COOKIE = '_COOKIE';
	const SUPERGLOBAL_REQUEST = '_GET';
	const SUPERGLOBAL_SERVER = '_SERVER';
	const SUPERGLOBAL_SESSION = '_SESSION';
	const SUPERGLOBAL_ENV = '_ENV';

	private $name = '';

	public function __construct($name) {
		$this->name = $name;
	}

	// CUSTOM METHODS ----------------------------------------------------------
	public static function load($name/*, $name2, ... */) {
		$validNames = array(
			self::SUPERGLOBAL_COOKIE,
			self::SUPERGLOBAL_GET,
			self::SUPERGLOBAL_POST,
			self::SUPERGLOBAL_REQUEST,
			self::SUPERGLOBAL_SERVER,
			self::SUPERGLOBAL_SESSION,
			self::SUPERGLOBAL_ENV,
		);
		
		$names = func_get_args();
		foreach ($names as $name) {
			if (in_array($name, $validNames)) {
				$GLOBALS[$name] = new self($name);
			}
		}
	}
	
	private function accessDenied() {
		throw new Core_Exception('Access denied to superglobal $'.$this->name);
	}

	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function offsetGet($key) {
		$this->accessDenied();
	}
	
	public function offsetSet($key, $value) {
		$this->accessDenied();
	}
	
	public function offsetUnset($key) {
		$this->accessDenied();
	}
	
	public function offsetExists($key) {
		$this->accessDenied();
	}
	
	public function getIterator() {
		$this->accessDenied();
	}
	
	public function count() {
		$this->accessDenied();
	}
	
	public function append($value) {
		$this->accessDenied();
	}
}

?>