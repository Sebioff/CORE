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
			
		// TODO remove with PHP 5.3 if SERVER_ADDR contains correct ip adress in cli mode (see App::boot())
		if (PHP_SAPI == 'cli')
			return self::LIVE;
			
		if (in_array($_SERVER['SERVER_ADDR'], array('::1', '127.0.0.1')))
			return self::DEVELOPMENT;
		else
			return self::LIVE;
	}
}

?>