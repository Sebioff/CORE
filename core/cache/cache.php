<?php

/**
 * Defines basic functions every cache needs to have.
 */
interface Cache {
	/**
	 * Updates the given key with the given value or inserts it into the cache
	 * if it doesn't exist yet.
	 */
	public function set($key, $value);
	
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