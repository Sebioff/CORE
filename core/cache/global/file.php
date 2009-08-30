<?php

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
			self::setStore(PROJECT_PATH.'/config/tmp/');
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