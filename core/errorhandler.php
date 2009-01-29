<?php

class Core_ErrorHandler {
	public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
		$errorType='';
		switch($errno) {
			case E_ERROR:
			case E_USER_ERROR:
				$errorType='Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$errorType='Warning';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$errorType='Notice';
				break;
		}
		$message=$errstr.' in:<br>'.$errfile.'('.$errline.')';
		$backtrace=debug_backtrace();
		unset($backtrace[0]);
		Core_BacktracePrinter::printBacktrace($backtrace, $message, $errorType);
		return true;
	}
}

?>