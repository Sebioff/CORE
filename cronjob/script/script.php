<?php

/**
 * Base class for cronjob scripts, extend this to implement your script
 */
abstract class Cronjob_Script {
	private $identifier = '';
	private $executionInterval = 0;
	
	/**
	 * @param $identifier string a unique identifier for this script
	 * @param $executionInterval int the interval in seconds in which this script
	 * should be executed
	 * NOTE: Execution is dependend on how often your Cronjob_Manager is executed
	 */
	public function __construct($identifier, $executionInterval = 60) {
		$this->identifier = $identifier;
		$this->executionInterval = $executionInterval;
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
}

?>