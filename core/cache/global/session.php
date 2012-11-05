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
 * For caching values in the session.
 * Thus it technically isn't a global cache, but use it as if it were.
 */
class Cache_Global_Session implements Cache {
	const SESSION_IDENTIFIER = 'CORE_cache_global_session';
	
	public function set($key, $value, $ttl = 604800) {
		$_SESSION[self::SESSION_IDENTIFIER][$key]['value'] = $value;
		$_SESSION[self::SESSION_IDENTIFIER][$key]['endtime'] = time() + $ttl;
	}

	public function get($key) {
		if (isset($_SESSION[self::SESSION_IDENTIFIER][$key])) {
			if ($_SESSION[self::SESSION_IDENTIFIER][$key]['endtime'] > time())
				return $_SESSION[self::SESSION_IDENTIFIER][$key]['value'];
			else
				$this->clear($key);
		}
		
		return null;
	}

	public function clear($key) {
		unset($_SESSION[self::SESSION_IDENTIFIER][$key]);
	}

	public function clearAll() {
		session_unset();
	}
}

?>