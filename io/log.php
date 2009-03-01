<?php
class IO_Log {
	private $stamps;
	private function getDiff($pfad) {
		$cnt = count($this->stamps[$pfad]);
		return ($cnt > 1) ? 
			$this->stamps[$pfad][$cnt - 1]['time'] - $this->stamps[$pfad][$cnt - 2]['time'] :
			-1;
	}
	public function setMark(Boolean $print2file = null, Float $difference = null) {
		$debug = debug_backtrace();
		$pfad = str_replace(
			array('\\', $_SERVER['DOCUMENT_ROOT'].'/'), 
			array('/', ''), $debug[0]['file']
		);
		$this->stamps[$pfad][] = array(	'line' => $debug[0]['line'],
										'time' => microtime(true));
//		echo 'setMark ('.count($this->stamps[$pfad]).') '.$pfad.':<br />';
//		echo 'basename: '.basename(PROJECT_PATH).' -- '.($debug[0]['file']).'<br />';
//		echo '<pre>'.print_r($debug[0], true).'</pre>';
		if (count($this->stamps[$pfad]) > 1) {
			$diff = $this->getDiff($pfad);
			$cnt = count($this->stamps[$pfad]);
			if (!$print2file) {
				echo 'Differenz ['.$pfad.':'.$this->stamps[$pfad][$cnt - 2]['line'].'] - ['.$pfad.':'.$this->stamps[$pfad][$cnt - 1]['line'].']: '.number_format($diff, 5).'<br />';
			} else {
				//Print to Logfile
			}
		}
	}
	public function __destruct() {
//		echo 'Destruct:<br />';
//		echo '<pre>'.print_r($this->stamps, true).'</pre>';
	}
}
?>