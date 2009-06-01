<?php

/**
 * Called whenever an exception occurs
 */
class Core_ExceptionHandler {
	public static function handleException(Exception $exception) {
		$message=$exception->getMessage().' in:<br>'.$exception->getFile().'('.$exception->getLine().')';
		$backtrace = $exception->getTrace();
		if (isset($backtrace[0]['class']) && $backtrace[0]['class'] == 'Core_ErrorHandler')
			unset($backtrace[0]);
		Core_BacktracePrinter::printBacktrace($backtrace, $message, 'Uncaught '.get_class($exception));
	}
}

?>