<?php
class IO_Log {
	private $stamps;
	public function __construct() {
		date_default_timezone_set('Europe/Berlin');
	}
	private function getDiff($pfad) {
		$cnt = count($this->stamps[$pfad]);
		return ($cnt > 1) ? 
			$this->stamps[$pfad][$cnt - 1]['time'] - $this->stamps[$pfad][$cnt - 2]['time'] :
			-1;
	}
	/**
	 * Set stoppoints to benchmark the time between two stoppoints
	 * @param $print2file write the result into logfile (true) or print on display (false = default)
	 * @param $difference only recognize when difference between two stoppoints greater than this time in seconds 
	 * @return depends on options
	 */
	public function setMark($print2file = false, Float $difference = null) {
		//visit http://bugs.php.net/bug.php?id=40782 for more information
		if (!is_bool($print2file))
			throw new Core_Exception("First parameter has to be bool");
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
					echo 'Differenz: '.$retStr."<br />";
					return $retStr;
				} else {
					$printStr = date('H:i:s').' '.$retStr."\r\n";
					$logFileName = PROJECT_PATH.'/config/log/benchmark_'.date('Y-m-d').'.log';
					if (!file_put_contents($logFileName, $printStr, FILE_APPEND | LOCK_EX)) {
						throw new Core_Exception('File \''.$logFileName.'\' could not be written!');
					} else {
						return true;
					}
				}
			}
		}
	}
}
?>