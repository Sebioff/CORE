<?php

/**
 * Provides information about the server system
 */
abstract class System {
	const OS_WINDOWS = 0;
	const OS_LINUX = 1;
	
	/**
	 * @return int constant identifying the server operating system, see
	 * System::OS_*
	 */
	public static function getOS() {
		return (strpos(PHP_OS, 'Win') === 0) ? self::OS_WINDOWS : self::OS_LINUX;
	}
	
	/**
	 * @return string character sequence representing a new line, depending on the
	 * servers operating system
	 */
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