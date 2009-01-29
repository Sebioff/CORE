<?php

class Core_BacktracePrinter {
	public static function printBacktrace(Array $backtrace, $customMessage = '', $errorType = '') {
		require_once('templates/backtraceprinter.tpl');
	}
}

?>