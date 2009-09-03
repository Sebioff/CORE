<?php

/**
 * The application can run in two different environtments:
 * - DEVELOPMENT: used for developing; advanced caching disabled, migrations executed
 *   whenever there are any new ones, HTMLTidy enabled, ...
 * - LIVE: the environment the application is actually used in
 */
class Environment {
	const DEVELOPMENT = 0;
	const LIVE = 1;
	
	public static function getCurrentEnvironment() {
		if (defined('PROJECT_ENVIRONMENT'))
			return PROJECT_ENVIRONMENT;
		
		if ($_SERVER['SERVER_ADDR'] == '127.0.0.1')
			return self::DEVELOPMENT;
		else
			return self::LIVE;
	}
}

?>