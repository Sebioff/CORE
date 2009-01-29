<?php

class Core_ExceptionHandler {
	public static function handleException(Exception $exception) {
		$message=$exception->getMessage().' in:<br>'.$exception->getFile().'('.$exception->getLine().')';
		Core_BacktracePrinter::printBacktrace($exception->getTrace(), $message, 'Uncaught '.get_class($exception));
	}
}

?>