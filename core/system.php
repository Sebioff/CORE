<?php

/**
 * Makes information about the server system available
 */
abstract class System {
	const OS_WINDOWS = 0;
	const OS_LINUX = 1;
	
	public static function getOS() {
		return (strpos($_ENV['OS'], 'Win') === 0) ? self::OS_WINDOWS : self::OS_LINUX;
	}
	
	public static function getNewLine() {
		switch (self::getOS()) {
			case self::OS_WINDOWS:
				return "\r\n";
			default:
				return "\n";
				break;
		}
	}
}

?>