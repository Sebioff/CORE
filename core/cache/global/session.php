<?php

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