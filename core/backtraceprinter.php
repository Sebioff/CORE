<?php

class Core_BacktracePrinter {
	/**
	 * Clears the output buffer and displays an error page.
	 * Per default the error page contains the error message and a backtrace.
	 * On the Live Environment, a callback defined by the constant ERROR_CALLBACK
	 * is called (if defined).
	 * @param $backtrace the backtrace to display
	 * @param $customMessage
	 * @param $errorType a custom error type, useful to categorize errors.
	 */
	public static function handle(array $backtrace, $customMessage = '', $errorType = '') {
		if (ob_get_level() > 0)
			ob_end_clean();
		
		foreach ($backtrace as &$backtraceItem) {
			if (isset($backtraceItem['args'])) {
				foreach ($backtraceItem['args'] as &$argument) {
					if (is_object($argument))
						$argument = get_class($argument);
					elseif ($argument === null)
						$argument = 'null';
					else
						$argument = '\''.$argument.'\'';
				}
			}
		}
		
		if (Environment::getCurrentEnvironment() == Environment::LIVE && defined('CALLBACK_ERROR'))
			call_user_func(CALLBACK_ERROR, $backtrace, $customMessage, $errorType);
		else
			self::printBacktrace($backtrace, $customMessage, $errorType);
		exit;
	}
	
	/**
	 * Does the actual printing
	 */
	public static function printBacktrace(array $backtrace, $customMessage = '', $errorType = '') {
		switch (Router::get()->getRequestMode()) {
			case Router::REQUESTMODE_AJAX:
				header("HTTP/1.0 500 Internal Server Error");
			case Router::REQUESTMODE_CLI:
				echo self::backtraceToString($backtrace, $customMessage, $errorType);
			break;
			default:
				require_once('backtraceprinter.tpl');
			break;
		}
	}
	
	private static function backtraceToString(array $backtrace, $customMessage = '', $errorType = '') {
		$nr = 0;
		$traceCount = count($backtrace);
		$customMessage = str_replace('<br>', "\n", $customMessage);
		$string = $errorType.'! '.$customMessage."\n\n";
		foreach ($backtrace as $backtraceMessage) {
			$string .= '#'.($traceCount-$nr)."\t";
			$string .= (isset($backtraceMessage['class'])?$backtraceMessage['class'].$backtraceMessage['type'].$backtraceMessage['function']:$backtraceMessage['function']).'('.(isset($backtraceMessage['args']) ? implode(', ', $backtraceMessage['args']) : '').')';
			if (isset($backtraceMessage['file']))
				$string .= ' in '.$backtraceMessage['file'].'('.$backtraceMessage['line'].')';
			$string .= "\n";
			$nr++;
		}
		
		return $string;
	}
}

?>