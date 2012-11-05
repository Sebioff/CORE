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
 * Defines basic functions every cache needs to have.
 */
interface Cache {
	/**
	 * Updates the given key with the given value or inserts it into the cache
	 * if it doesn't exist yet.
	 * Default ttl: 1 week
	 */
	public function set($key, $value, $ttl = 604800);
	
	/**
	 * Returns the value associated with the given key.
	 */
	public function get($key);
	
	/**
	 * Removes the value belonging to the given key from the cache.
	 */
	public function clear($key);
	
	/**
	 * Clears the whole cache.
	 */
	public function clearAll();
}

?>