<?php

/**
 * Dumps information about whatever input it gets
 */
class Core_Dump {
	/**
	 * Dumps detailed information about its input
	 */
	public static function dump() {
		foreach (func_get_args() as $arg) {
			echo '<div class="ob_dump" style="display:block; position:relative;z-index:1000;"><table style="background-color:green;border:1px solid black;margin-top:5px;"><tr><td style="color:white;"><pre>';
			var_dump($arg);
			echo '</pre></td></tr></table></div>';
		}

		ob_flush();
		ob_start();
	}
	
	/**
	 * Doesn't dive into objects -> prevents problems caused by recursion
	 */
	public static function dump_flat() {
		foreach (func_get_args() as $arg) {
			ob_flush();
			if (empty($GLOBALS['ob_flushed']))
				$GLOBALS['ob_flushed']=true;
			echo '<div style="position:relative;z-index:1000;"><table style="background-color:gray;border:1px solid black;margin-top:5px;"><tr><td style="color:white;padding:5px;"><pre>';
			if ($arg===true)
				echo 'true (bool)';
			elseif ($arg===false)
				echo 'false (bool)';
			elseif ($arg===null)
				echo 'NULL';
			elseif (is_scalar($arg))
				printf('%s (%s)', $arg, gettype($arg));
			elseif (is_object($arg)) {
				printf("Object(%s)\n{\n", get_class($arg));
				echo " ::: PROPERTIES :::\n";

				foreach ((array)$arg as $k=>$v) {
					if (preg_match(sprintf('#^%1$s.+%1$s(.+?)$#', preg_quote(chr(0))), $k, $match))
					$k=$match[1];
					if (is_object($v)) {
						if (in_array('__toString', get_class_methods($v)))
							printf("  { %s: '%s' { %s } }\n", $k, (string)$v, get_class($v));
						else
							printf("  { %s: { %s } }\n", $k, get_class($v));
					}
					elseif (is_array($v))
						printf("  { %s: [ Array (%d) ] }\n", $k, count($v));
					elseif (is_string($v))
						printf("  { %s: '%s' (string) }\n", $k, $v);
					elseif (is_null($v))
						printf("  { %s: NULL }\n", $k);
					elseif ($v===true)
						printf("  { %s: true (bool) }\n", $k);
					elseif ($v===false)
						printf("  { %s: false (bool) }\n", $k);
					else
						printf("  { %s: %s (%s) }\n", $k, $v, gettype($v));
				}
				echo " ::: METHODS :::\n";
				foreach (get_class_methods($arg) as $method)
					echo '  '.$method."\n";
				echo '}';
			}
			elseif (is_array($arg)) {
				printf("Array(%s)\n[\n", count($arg));

				foreach ($arg as $k=>$v) {
					if (preg_match(sprintf('#^%1$s.+%1$s(.+?)$#', preg_quote(chr(0))), $k, $match))
					$k=$match[1];

					if (is_object($v)) {
						if (in_array('__toString', get_class_methods($v)))
							printf("  [ %s => '%s' { %s } ]\n", $k, (string)$v, get_class($v));
						else
							printf("  [ %s => { %s } ]\n", $k, get_class($v));
					}
					elseif (is_array($v))
						printf("  [ %s => [ Array (%d) ] ]\n", $k, count($v));
					elseif (is_null($v))
						printf("  [ %s => NULL ]\n", $k);
					elseif ($v===true)
						printf("  [ %s => true (bool) ]\n", $k);
					elseif ($v===false)
						printf("  [ %s => false (bool) ]\n", $k);
					else
						printf("  [ %s => %s (%s) ]\n", $k, $v, gettype($v));
				}
				echo ']';
			}

			$trace=debug_backtrace();
			$first=reset($trace);
			printf("\n\n<span style=\"font-size:10px;\">%s:%s</span>", $first['file'], $first['line']);
			echo '</pre></td></tr></table></div>';
		}

		ob_flush();
		ob_start();
	}
	
	public static function backtrace() {
		$e = new Exception();
	    $trace = '';
	    $traceArray = explode("\n", $e->getTraceAsString());
	    array_shift($traceArray);
	    array_shift($traceArray);
	    foreach ($traceArray as $line)
	      $trace .= substr($line, strpos($line, ' ') + 1)."\n";
	      ob_flush();
	      echo '<div style="position:relative;z-index:1000;"><table style="background-color:brown;border:1px solid black;margin-top:5px;"><tr><td style="color:white;"><pre>';
	      echo $trace;
	      echo '</pre></td></tr></table></div>';
	}
}

?>