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

require_once dirname(__FILE__).'/3rdparty/Cache.php';
require_once dirname(__FILE__).'/../../System.php';

/**
 * For caching data in files. Note that the default caching location on unix systems
 * is /dev/shm (= shared memory), so actually there aren't any files created there.
 * Facade for Phpgurus Cache.
 */
class Cache_Global_File extends Phpguru_DataCache implements Cache {
	const FILE_IDENTIFIER = 'CORE_cache_global_file';
	
	public function __construct() {
		self::setPrefix('core_cache_');
		if (System::getOS() != System::OS_LINUX)
			self::setStore(System::getTemporaryDirectory());
		if (!file_exists(self::$store))
			trigger_error('Cache directory doesn\'t exist: '.self::$store, E_USER_ERROR);
	}
	
	public function set($key, $value, $ttl = 604800) {
		self::PutData(self::FILE_IDENTIFIER, $key, $ttl, $value);
	}

	public function get($key) {
		return self::GetData(self::FILE_IDENTIFIER, $key);
	}

	public function clear($key) {
		unlink(self::getFilename(self::FILE_IDENTIFIER, $key));
	}

	public function clearAll() {
		foreach (IO_Utils::getFilesFromFolder(self::$store) as $file) {
			if (strpos($file, self::$prefix) === 0)
				unlink(self::$store.$file);
		}
	}
}

?>