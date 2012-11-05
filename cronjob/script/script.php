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
 * Base class for cronjob scripts, extend this to implement your script
 */
abstract class Cronjob_Script extends Scriptlet implements Scriptlet_Privileged {
	private $identifier = '';
	private $executionInterval = 0;
	
	/**
	 * @param $identifier string a unique identifier for this script
	 * @param $executionInterval int the interval in seconds in which this script
	 * should be executed
	 * NOTE: Execution is dependend on how often your Cronjob_Manager is executed
	 */
	public function __construct($identifier, $executionInterval = 60) {
		parent::__construct($identifier);
		$this->identifier = $identifier;
		$this->executionInterval = $executionInterval;
	}
	
	public final function init() {
		// FIXME App/Router currently don't respect Scriptlet_Privileged
		if (!$this->checkPrivileges())
			exit;
		
		$this->triggerExecution();
	}
	
	public final function triggerExecution() {
		$cronjobRecord = $this->getRecord();
		$cronjobRecord->lastExecution = time();
		try {
			$executionStart = microtime(true);
			$this->execute();
			$cronjobRecord->lastExecutionDuration = microtime(true) - $executionStart;
			$cronjobRecord->lastExecutionSuccessful = true;
		}
		catch (Core_Exception $ce) {
			$cronjobRecord->lastExecutionSuccessful = false;
			$this->getParent()->triggerOnScriptException($ce, $this);
		}
		
		$this->getParent()->getCronjobContainer()->save($cronjobRecord);
	}
	
	/**
	 * Implement what the script should do here
	 */
	public abstract function execute();
	
	/**
	 * @param $lastExecutionTime the last time at which this script has been
	 * executed (unix timestamp)
	 * @return boolean true if this script needs to be executed, false otherwise
	 */
	public function requiresExecution($lastExecutionTime) {
		return (time() - $lastExecutionTime >= $this->executionInterval);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getIdentifier() {
		return $this->identifier;
	}
	
	public function checkPrivileges() {
		return $this->getParent()->checkPrivileges();
	}
	
	/**
	 * Returns the DB_Record that is used to store information about this cronjob
	 * script.
	 * @return DB_Record
	 */
	public function getRecord() {
		$record = $this->getParent()->getCronjobContainer()->selectByPK($this->getIdentifier());
		if (!$record) {
			$record = new DB_Record();
			$record->identifier = $this->getIdentifier();
		}
		
		return $record;
	}
}

?>