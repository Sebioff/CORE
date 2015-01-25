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

	// path of reset callback logfile, relative to PROJECT_PATH
	const RESET_CALLBACK_LOG_FILE = '/config/log/reset_callback.log.xml';

	private static $callbacksOnAfterReset = array();
	
	public function init() {
		parent::init();
		
		// clear database
		DB_Connection::get()->deleteTables();
		
		// remove migrations log
		$migrationFile = new IO_File(PROJECT_PATH.Core_MigrationsLoader::MIGRATION_LOG_FILE);
		$migrationFile->delete();

		// remove reset callbacks log
		$resetCallbacksFile = new IO_File(PROJECT_PATH.self::RESET_CALLBACK_LOG_FILE);
		$resetCallbacksFile->delete();
		
		// clear global cache
		$GLOBALS['cache']->clearAll();
		
		// clear page cache
		IO_Utils::deleteFolder(Scriptlet::getPageCacheDirectory());
		
		// clear session
		unset($_SESSION);
		
		// rebuild database
		Core_MigrationsLoader::load();
		
                // execute registered callbacks
                self::executeCallbacksOnAfterReset();
		
		Scriptlet::redirect(PROJECT_ROOTURI);
	}
	
	public static function addCallbackOnAfterReset($callback) {
		self::$callbacksOnAfterReset[] = $callback;
	}

	/**
	 * Executes the callbacks registered by addCallbackOnAfterReset
	 * or defined by the constant CALLBACK_ONAFTERRESET.
	 * Execution is only performed if the callbacks haven't been called
	 * since the last reset.
	 * This method is intended to be used when there's a reason to execute
	 * these callbacks once, even when there's no complete reset being performed.
	 * One such reason is the initial setup and running newly added callbacks
	 * in development mode.
	 * Callbacks that differ only in the instance of a class and not in the method called
	 * will be treated as if they were the same. So if more than one instance of a class
	 * is used for a callback, each instance needs its unique member function to be executed.
	 */
	public static function executeCallbacksOnAfterReset() {

		if (defined('CALLBACK_ONAFTERRESET'))
			self::addCallbackOnAfterReset(CALLBACK_ONAFTERRESET);

		if (file_exists(PROJECT_PATH.self::RESET_CALLBACK_LOG_FILE))
			$xml = simplexml_load_file(PROJECT_PATH.self::RESET_CALLBACK_LOG_FILE);
		else {
			$baseResetCallbackLog = '<?xml version=\'1.0\'?><content></content>';
			$xml = new SimpleXMLElement($baseResetCallbackLog);
		}

		foreach (self::$callbacksOnAfterReset as $callback) {
			if (is_array($callback) && isset($callback[0]) && isset($callback[1])) {
				// different objects of the same class will be treated as if they were the same
				$callbackIdentifier = get_class($callback[0]).'->'.$callback[1];
			} else $callbackIdentifier = $callback;

			// execute reset callbacks if they haven't been before
			$result = $xml->xpath(sprintf('/content/callback[@identifier=\'%s\']', $callbackIdentifier));
			if (empty($result)) {
				$result = $xml->xpath('/content');
				$child = $result[0]->addChild('callback');
				$child->addAttribute('identifier', $callbackIdentifier);
				call_user_func($callback);
                        }
                }
		// save reset callback logfile
		$doc = new DOMDocument('1.0');
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($xml->asXML());
		$doc->formatOutput = true;
		$doc->save(PROJECT_PATH.self::RESET_CALLBACK_LOG_FILE);
        }

}

?>
