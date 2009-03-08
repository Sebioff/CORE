<?php

class Environment {
	const DEVELOPMENT = 0;
	const LIVE = 1;
	
	public static function getCurrentEnvironment() {
		if(defined('ENVIRONMENT'))
			return ENVIRONMENT;
		
		if($_SERVER['SERVER_ADDR'] == '127.0.0.1')
			return self::DEVELOPMENT;
		else
			return self::LIVE;
	}
}

?>