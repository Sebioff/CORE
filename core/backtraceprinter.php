<?php

class Core_BacktracePrinter {
	/**
	 * Clears the output buffer and displays an error page with backtrace.
	 * @param $backtrace the backtrace to display
	 * @param $customMessage
	 * @param $errorType a custom error type, useful to categorize errors.
	 */
	public static function printBacktrace(Array $backtrace, $customMessage = '', $errorType = '') {
		ob_end_clean();
		require_once('templates/backtraceprinter.tpl');
		exit;
	}
}

?>