<?php

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
		$file = new IO_File(Core_MigrationsLoader::MIGRATION_LOG_FILE);
		$file->delete();
		
		// clear global cache
		$GLOBALS['cache']->clearAll();
		
		// clear session
		unset($_SESSION);
		
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