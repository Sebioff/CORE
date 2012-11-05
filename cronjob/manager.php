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
 * Manager class for all cronjob scripts. Knows all scripts and triggers their
 * execution if neccessary.
 */
class Cronjob_Manager extends Scriptlet implements Scriptlet_Privileged {
	private $scripts = array();
	private $cronjobContainer = null;
	private $databaseTableName;
	
	public function __construct($moduleName, $databaseTableName) {
		parent::__construct($moduleName);

		$this->databaseTableName = $databaseTableName;
		Core_MigrationsLoader::addMigrationFolder(dirname(__FILE__).'/migrations', array('databaseTableName' => $databaseTableName));
	}
	
	public function afterInit() {
		parent::afterInit();
		
		// FIXME App/Router currently don't respect Scriptlet_Privileged
		if (!$this->checkPrivileges())
			exit;
		
		$this->triggerExecution();
	}
	
	public function triggerExecution() {
		foreach ($this->scripts as $script) {
			if ($script->requiresExecution($script->getRecord()->lastExecution)) {
				$script->triggerExecution();
			}
		}
	}
	
	public final function triggerOnScriptException(Core_Exception $ce, Cronjob_Script $script) {
		DB_Connection::get()->rollbackAll();
		$this->onScriptException($ce, $script);
	}
	
	/**
	 * Executed if a cronjob script fails
	 * @param $ce Core_Exception
	 * @param $script Cronjob_Script the script that failed
	 */
	protected function onScriptException(Core_Exception $ce, Cronjob_Script $script) {
		
	}
	
	protected function addScript(Cronjob_Script $script) {
		$this->scripts[] = $script;
		$this->addSubmodule($script);
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function checkPrivileges() {
		return ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']);
	}
	
	/**
	 * @return DB_Container
	 */
	public function getCronjobContainer() {
		if ($this->cronjobContainer)
			return $this->cronjobContainer;
			
		$this->cronjobContainer = new DB_Container($this->databaseTableName);
		return $this->cronjobContainer;
	}
}

?>