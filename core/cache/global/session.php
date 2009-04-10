<?php

/**
 * For caching values in the session.
 * Thus it technically isn't a global cache, but use it as if it were.
 */
class Cache_Global_Session implements Cache {
	const SESSION_IDENTIFIER = 'CORE_cache_global_session';
	
	public function set($key, $value) {
		$_SESSION[self::SESSION_IDENTIFIER][$key] = $value;
	}

	public function get($key) {
		return isset($_SESSION[self::SESSION_IDENTIFIER][$key]) ? $_SESSION[self::SESSION_IDENTIFIER][$key] : false;
	}

	public function clear($key) {
		unset($_SESSION[self::SESSION_IDENTIFIER][$key]);
	}

	public function clearAll() {
		session_unset();
	}
}

?>