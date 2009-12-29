<?php

/**
 * Called whenever a PHP error occurs
 */
class Core_ErrorHandler {
	public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
		$recoverableErrors = array(E_RECOVERABLE_ERROR, E_WARNING, E_USER_WARNING, E_NOTICE, E_USER_NOTICE);
		
		$errorType = '';
		switch ($errno) {
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$errorType = 'Error';
			break;
			case E_WARNING:
			case E_USER_WARNING:
				$errorType = 'Warning';
			break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$errorType = 'Notice';
			break;
		}
		
		/*
		 * if the error isn't that bad, we transform it into an exception to make
		 * it catchable
		 */
		if (in_array($errno, $recoverableErrors)) {
			// quietly consume recoverable errors of 3rdparty-scripts
			if (strpos($errfile, DS.'3rdparty'.DS) !== false)
				return true;
			else
				throw new Core_Exception_PHPError($errorType.': '.$errstr.' in '.$errfile.'('.$errline.') thrown');
		}
		/*
		 * otherwise there's nothing we can do so we call our error page handling
		 */
		else {
			$message = $errstr.' in:<br>'.$errfile.'('.$errline.')';
			$backtrace = debug_backtrace();
			unset($backtrace[0]);
			Core_BacktracePrinter::handle($backtrace, $message, $errorType);
		}
	}
	
	/**
	 * Executed on shutdown - thus it can also check for fatal errors etc. for which
	 * PHP doesn't execute the error handler.
	 */
	public static function onShutdown() {
	    if ($error = error_get_last()){
	    	switch($error['type']){
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					$message = $error['message'].' in:<br>'.$error['file'].'('.$error['line'].')';
					Core_BacktracePrinter::handle(array(), $message, 'Fatal error');
				break;
			}
		}
	}
}

?>