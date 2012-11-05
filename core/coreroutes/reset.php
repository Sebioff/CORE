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
 * Resets the project.
 */
class CoreRoutes_Reset extends Module {
	private static $callbacksOnAfterReset = array();
	
	public function init() {
		parent::init();
		
		// clear database
		DB_Connection::get()->deleteTables();
		
		// remove migrations log
		$file = new IO_File(PROJECT_PATH.Core_MigrationsLoader::MIGRATION_LOG_FILE);
		$file->delete();
		
		// clear global cache
		$GLOBALS['cache']->clearAll();
		
		// clear page cache
		IO_Utils::deleteFolder(Scriptlet::getPageCacheDirectory());
		
		// clear session
		unset($_SESSION);
		
		// rebuild database
		Core_MigrationsLoader::load();
		
		if (defined('CALLBACK_ONAFTERRESET'))
			self::addCallbackOnAfterReset(CALLBACK_ONAFTERRESET);
		foreach (self::$callbacksOnAfterReset as $callback)
			call_user_func($callback);
		
		Scriptlet::redirect(PROJECT_ROOTURI);
	}
	
	public static function addCallbackOnAfterReset($callback) {
		self::$callbacksOnAfterReset[] = $callback;
	}
}

?>