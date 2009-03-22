<?php

/**
 * Resets the project.
 */
class CoreRoutes_Reset extends Module {
	public function init() {
		parent::init();
		
		DB_Connection::get()->deleteTables();
		$file=new IO_File(Core_MigrationsLoader::MIGRATION_LOG_FILE);
		$file->delete();
		
		$GLOBALS['memcache']->clear();
		
		unset($_SESSION);
		
		Scriptlet::redirect(PROJECT_ROOTURI);
	}
}

?>