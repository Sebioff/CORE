<?php

/**
 * Called whenever an exception hasn't been catched
 */
class Core_ExceptionHandler {
	public static function handleException(Exception $exception) {
		try {
			$message = $exception->getMessage().' in:<br>'.$exception->getFile().'('.$exception->getLine().')';
			$backtrace = $exception->getTrace();
			if (isset($backtrace[0]['class']) && $backtrace[0]['class'] == 'Core_ErrorHandler')
				unset($backtrace[0]);
			Core_BacktracePrinter::handle($backtrace, $message, 'Uncaught '.get_class($exception));
		}
		/*
		 * If there is an exception thrown within the exception handler, PHP gives
		 * a pretty useless error messsage - so we catch all exceptions that could
		 * happen here and output a better message.
		 */
		catch (Exception $exception) {
			// on DEVELOPMENT we can print a detailed error message...
			if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
				$message = $exception->getMessage().' in:<br>'.$exception->getFile().'('.$exception->getLine().'). No backtrace available.';
				/*
				 * NOTE: obviously we can't call Core_BacktracePrinter::handle() here
				 * since this is pretty sure the place where the exception was thrown.
				 * That's why we call printBacktrace() where nothing can go wrong.
				 */
				Core_BacktracePrinter::printBacktrace(array(), $message, 'Uncaught '.get_class($exception).' thrown within Core_ExceptionHandler');
			}
			// on LIVE there's nothing we can do anymore at this point
			else {
				echo 'An error occured in the method that should display error messages.';
			}
		}
	}
}

?>