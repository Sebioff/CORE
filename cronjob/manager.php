<?php

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
		
		foreach ($this->scripts as $script) {
			$cronjobRecord = Rakuun_DB_Containers::getCronjobsContainer()->selectByPK($script->getIdentifier());
			if (!$cronjobRecord) {
				$cronjobRecord = new DB_Record();
				$cronjobRecord->identifier = $script->getIdentifier();
			}
			
			if (!$script->requiresExecution($cronjobRecord->lastExecution))
				continue;
			
			$cronjobRecord->lastExecution = time();
			try {
				$executionStart = microtime(true);
				$script->execute();
				$cronjobRecord->lastExecutionDuration = microtime(true) - $executionStart;
				$cronjobRecord->lastExecutionSuccessful = true;
			}
			catch (Core_Exception $ce) {
				$cronjobRecord->lastExecutionSuccessful = false;
				$this->onScriptException($ce, $script);
			}
			
			Rakuun_DB_Containers::getCronjobsContainer()->save($cronjobRecord);
		}
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
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function checkPrivileges() {
		return ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']);
	}
	
	public function getCronjobContainer() {
		if ($this->cronjobContainer)
			return $this->cronjobContainer;
			
		$this->cronjobContainer = new DB_Container($this->databaseTableName);
	}
}

?>