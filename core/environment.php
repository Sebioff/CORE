<?php

class Environment {
	const AUTO_DETERMINE = 0;
	const DEVELOPMENT = 1;
	const LIVE = 2;
	
	private static $currentEnvironment = self::AUTO_DETERMINE;
	
	/**
	 * Allows overriding the environment, e.g. for testing purposes
	 * @param $environment see Environment-constants
	 */
	public static function setCurrentEnvironment($environment) {
		self::$currentEnvironment = $environment;
	}
	
	public static function getCurrentEnvironment() {
		if(self::$currentEnvironment != self::AUTO_DETERMINE)
			return self::$currentEnvironment;
		
		if($_SERVER['SERVER_ADDR'] == '127.0.0.1')
			return self::DEVELOPMENT;
		else
			return self::LIVE;
	}
}

?>