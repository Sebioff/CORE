<?php

/**
 * The application can run in two different environments:
 * - DEVELOPMENT: used for developing; advanced caching disabled, migrations executed
 *   whenever there are any new ones, HTMLTidy enabled, ...
 * - LIVE: the environment the application is actually used in
 */
class Environment {
	const DEVELOPMENT = 0;
	const LIVE = 1;
	
	/**
	 * @return int number representing the current environment (see constants)
	 */
	public static function getCurrentEnvironment() {
		if (defined('PROJECT_ENVIRONMENT'))
			return PROJECT_ENVIRONMENT;
			
		if (isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], array('::1', '127.0.0.1')))
			return self::DEVELOPMENT;
		else
			return self::LIVE;
	}
}

?>