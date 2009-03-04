<?php
class IO_Log {
	private static $instance = null;
	private $stamps;
	private function __construct() {
	}
	/**
	 * Returns singleton instance of this class
	 * @return reference IO_Log
	 */
	public static function get() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	/**
	 * Write a Line to Errorlog
	 * @param $msg Errormessage
	 * @param $line Optional errorline
	 * @return null
	 */
	public function error($msg, $line = -1) {
		$this->log($msg, $line, 'ERROR');
	}
	private function getDiff($pfad) {
		$cnt = count($this->stamps[$pfad]);
		return ($cnt > 1) ? 
			$this->stamps[$pfad][$cnt - 1]['time'] - $this->stamps[$pfad][$cnt - 2]['time'] :
			-1;
	}
	/**
	 * Write a Line to Informationlog
	 * @param $msg Informationmessage
	 * @param $line Optional Informationline
	 * @return null
	 */
	public function info($msg, $line = -1) {
		$this->log($msg, $line);
	}
	private function log($msg, $line = -1, $level = null) {
		if (!strlen($msg) > 0)
			throw new Core_Exception('First parameter is too short');
		$debug = debug_backtrace();
		$line = ($line > 0) ? $line : $debug[1]['line'];
		$pfad = str_replace(
			array('\\', $_SERVER['DOCUMENT_ROOT'].'/'), 
			array('/', ''), $debug[1]['file']
		);
		$printStr = date('H:i:s').' **'.(isset($level) ? $level : 'INFO')."**\t".$pfad.':'.$line."\t".$msg;
		$this->write2file($printStr, 'error');
	}
	/**
	 * Set stoppoints to benchmark the time between
	 * @param $print2file write the result into logfile (true) or print on display (false = default)
	 * @param $difference only recognize when difference between two stoppoints greater than this time in seconds 
	 * @return depends on options
	 */
	public function setMark($print2file = false, $difference = 0.0) {
		//visit http://bugs.php.net/bug.php?id=40782 for more information
		if (!is_bool($print2file))
			throw new Core_Exception('First parameter has to be bool');
		if (!is_float($difference) && !is_int($difference))
			throw new Core_Exception('Second parameter has to be float');
		$debug = debug_backtrace();
		$pfad = str_replace(
			array('\\', $_SERVER['DOCUMENT_ROOT'].'/'), 
			array('/', ''), $debug[0]['file']
		);
		$this->stamps[$pfad][] = array(	'line' => $debug[0]['line'],
										'time' => microtime(true));
		if (count($this->stamps[$pfad]) > 1) {
			$diff = $this->getDiff($pfad);
			if (($difference && $difference <= $diff) || (!$difference)) {
				$cnt = count($this->stamps[$pfad]);
				$retStr = '['.$pfad.'] Line '.$this->stamps[$pfad][$cnt - 2]['line'].' to '.$this->stamps[$pfad][$cnt - 1]['line'].': '.number_format(abs($diff), 6).' sec'; 
				if (!$print2file) {
					echo 'Differenz: '.$retStr.'<br />';
					return $retStr;
				} else {
					$printStr = date('H:i:s').' '.$retStr;
					$this->write2file($printStr, 'benchmark');
				}
			}
		}
	}	
	/**
	 * Write a Line to Warninglog
	 * @param $msg Warningmessage
	 * @param $line Optional Warningline
	 * @return null
	 */
	public function warning($msg, $line = -1) {
		$this->log($msg, $line, 'WARNING');
	}
	private function write2file($msg, $fileNamePrefix) {
		$logFileName = PROJECT_PATH.'/config/log/'.$fileNamePrefix.'_'.date('Y-m-d').'.log';
		if (!file_put_contents($logFileName, $msg.NEW_LINE, FILE_APPEND | LOCK_EX)) {
			throw new Core_Exception('File \''.$logFileName.'\' could not be written!');
		}
	}
}
?>