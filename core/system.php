<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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
		return (stripos(PHP_OS, 'Win') === 0) ? self::OS_WINDOWS : self::OS_LINUX;
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
		}
	}
	
	/**
	 * @return string path to a directory that can be used to store temporary files.
	 */
	public static function getTemporaryDirectory() {
		if (defined('CORE_TEMPORARY_DIRECTORY'))
			return CORE_TEMPORARY_DIRECTORY;
		else
			return sys_get_temp_dir();
	}
}

?>