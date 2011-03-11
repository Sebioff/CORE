<?php

/**
 * @author out-of-order
 */
class IO_Log {
	const LOGFILE_PREFIX = 'log';
	const LOGFILE_EXTENSION = 'log';
	const BENCHFILE_PREFIX = 'bench';
	
	private static $instance = null;
	
	private $stamps = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	private function __construct() {
		// Singleton
	}
	
	/**
	 * Returns singleton instance of this class
	 * @return IO_Log
	 */
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * Write a line to Errorlog
	 * @param $msg Errormessage
	 * @param $line Optional errorline
	 */
	public function error($msg, $line = -1) {
		$this->log($msg, 'ERROR', $line);
	}
	
	/**
	 * Write a line to Informationlog
	 * @param $msg Informationmessage
	 * @param $line Optional Informationline
	 */
	public function info($msg, $line = -1) {
		$this->log($msg, 'INFO', $line);
	}
	
	/**
	 * Write a Line to Warninglog
	 * @param $msg Warningmessage
	 * @param $line Optional Warningline
	 */
	public function warning($msg, $line = -1) {
		$this->log($msg, 'WARNING', $line);
	}
	
	/**
	 * Set stoppoints to benchmark the time between
	 * @param $printToFile write the result into logfile (true) or print on display (false = default)
	 * @param $lowerTimeLimit only recognize when difference between two stoppoints greater than this time in seconds
	 * @param $notice Optional Notice
	 * @return depends on options
	 */
	public function setMark($printToFile = false, $lowerTimeLimit = 0.0, $notice = '') {
		if (Environment::getCurrentEnvironment() != Environment::DEVELOPMENT && (!defined('CORE_ENABLE_LOGGING') || CORE_ENABLE_LOGGING == false))
			return;
		
		$debug = debug_backtrace();
		$pfad = str_replace(
			array('\\', $_SERVER['DOCUMENT_ROOT'].'/'),
			array('/', ''), $debug[0]['file']
		);
		$this->stamps[$pfad][] = array( 'line' => $debug[0]['line'],
										'time' => microtime(true));
		if (count($this->stamps[$pfad]) > 1) {
			$diff = $this->getDiff($pfad);
			if ($lowerTimeLimit <= $diff) {
				$cnt = count($this->stamps[$pfad]);
				$retStr = '['.$pfad.'] Line '.$this->stamps[$pfad][$cnt - 2]['line'].' to '.$this->stamps[$pfad][$cnt - 1]['line'].': '.number_format(abs($diff), 6).' sec';
				if (isset($notice))
					$retStr .= ' '.$notice;
				if ($printToFile) {
					$this->writeToFile(date('H:i:s').' '.$retStr, self::BENCHFILE_PREFIX);
				} else {
					return $retStr;
				}
			}
		}
	}
	
	private function log($msg, $level = 'INFO', $line = -1) {
		$debug = debug_backtrace();
		foreach ($debug as $step) {
			if (in_array($step['function'], array('info', 'error', 'warning')))
				break;
		}
		$line = ($line > 0) ? $line : $step['line'];
		$pfad = str_replace(
			array('\\', $_SERVER['DOCUMENT_ROOT'].'/'),
			array('/', ''), $step['file']
		);
		$printStr = date('H:i:s').' **'.$level."**\t".$pfad.':'.$line."\t".$msg;
		$this->writeToFile($printStr);
	}
	
	private function getDiff($pfad) {
		$cnt = count($this->stamps[$pfad]);
		return ($cnt > 1) ?
			$this->stamps[$pfad][$cnt - 1]['time'] - $this->stamps[$pfad][$cnt - 2]['time'] :
			-1;
	}
	
	private function writeToFile($msg, $fileNamePrefix = self::LOGFILE_PREFIX) {
		$logFileName = self::getLogfilePath().'/'.$fileNamePrefix.'_'.date('Y-m-d').'.'.self::LOGFILE_EXTENSION;
		$file = new IO_File($logFileName);
		if (!$file->exists()) {
			$file->create();
			chmod($logFileName, 666);
		}
		$file->open(IO_File::WRITE_APPEND);
		$file->write($msg.System::getNewLine());
	}
	
	public static function getLogfilePath() {
		return PROJECT_PATH.'/config/log';
	}
}

?>