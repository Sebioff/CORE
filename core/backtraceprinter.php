<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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
		header('HTTP/1.0 500 Internal Server Error');
		
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
			$string .= '#'.($traceCount - $nr).':'."\t";
			$string .= (isset($backtraceMessage['class']) ? $backtraceMessage['class'].$backtraceMessage['type'].$backtraceMessage['function'] : $backtraceMessage['function']).'('.(isset($backtraceMessage['args']) ? implode(', ', $backtraceMessage['args']) : '').')';
			if (isset($backtraceMessage['file']))
				$string .= ' in '.$backtraceMessage['file'].'('.$backtraceMessage['line'].')';
			$string .= "\n";
			$nr++;
		}
		
		return $string;
	}
}

?>