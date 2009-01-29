<?php

require_once'core/memorycache.php'; // can't be autoloaded since the autoloader uses this class

class App {
	public static $projectBasePath = null;
	public static $instance = null;

	private static $_modules=array();

	// CONSTRUCTION ------------------------------------------------------------
	private function __construct() {
		// singleton
	}

	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * Sets up everything neccessary, e.g. error/exception-handlers.
	 * Needs to be called before anything else can be done.
	 */
	public static function boot() {
		session_start();
		ob_start();
		error_reporting(E_ALL|E_STRICT);
		$GLOBALS['memcache'] = new Core_MemoryCache();
		spl_autoload_register(array('App_Autoloader', 'autoload'));
		set_error_handler(array('Core_ErrorHandler', 'handleError'));
		set_exception_handler(array('Core_ExceptionHandler', 'handleException'));

		if (!defined('CORE_PATH'))
			throw new Core_Exception('Path to framework (CORE_PATH) not set');

		if (self::$projectBasePath != null)
			throw new Core_Exception('Double boot');
			
		$backtrace=debug_backtrace();
		self::$projectBasePath = CORE_PATH.'/../'.dirname($backtrace[0]['file']);
			
		if (!$GLOBALS['memcache']->get('CORE_booted'))
			self::systemCheck();
			
		$GLOBALS['memcache']->set('CORE_booted', true);
	}

	public static function getPathFromUnderscore($filename_) {
		return implode('/', explode('_', $filename_));
	}

	/**
	 * Registers a module.
	 * @param $name_ the name of the new module
	 * @param $module_ the module
	 * @return unknown_type
	 */
	public function addModule($name_, $module_) {
		if(!in_array($name_, self::$_modules))
			self::$_modules[$name_]=$module_;
		else
			throw new Core_Exception('A module with this name has already been added: '.$name_);
	}

	/**
	 * Returns a registered module
	 * @param $name_ the name of the module
	 * @return the module
	 */
	public function getModule($name_) {
		if(!isset(self::$_modules[$name_]))
			throw new Core_Exception('Module doesn\'t exist: '.$name_);
		else
			return self::$_modules[$name_];
	}
	
	public static function get() {
    	return (self::$instance) ? self::$instance : self::$instance = new self();
  	}
	
  	/**
  	 * Checks that basic server configurations are set as needed
  	 * @return unknown_type
  	 */
	private static function systemCheck() {
		foreach(array('zip', 'xmlreader', 'mbstring', 'gd', 'mysql') as $extension)
			if(!extension_loaded($extension))
				throw new Core_Exception('Please verify your PHP configuration: extension "'.$extension.'" should be loaded.');
		
		// TODO can't be uncommented as long as its not clear which settings are needed / wanted
		//foreach(array('register_globals'=>0, 'magic_quotes_gpc'=>0, 'magic_quotes_runtime'=>0, 'short_open_tag'=>1, 'iconv.input_encoding'=>'UTF-8', 'iconv.internal_encoding'=>'UTF-8', 'iconv.output_encoding'=>'UTF-8', 'mbstring.encoding_translation'=>false, 'mbstring.internal_encoding'=>'UTF-8', 'mbstring.http_input'=>'auto', 'mbstring.http_output'=>'pass', 'bcmath.scale'=>10) as $option=>$value)
			//if($value!=ini_get($option))
				//throw new COREException('Please verify your PHP configuration: '.$option.' should be "'.$value.'".');
	}
}

/**
 * Responsible for loading classes.
 * @author Sebastian
 */
class App_Autoloader {
	// CUSTOM METHODS ----------------------------------------------------------
	public static function autoload($className) {
		$path = false;

		if (!($path = $GLOBALS['memcache']->get($className)))
			$path = self::getClassPath($className);

		if ($path)
			require_once $path;
		else
			trigger_error(sprintf('Class \'%s\' not found', $className), E_USER_ERROR);
	}

	/**
	 * Gets the path of the file a given class is located in
	 * @param $className the searched class
	 * @param $basePath a base path relative to which the search is made
	 * @return String the path of the file the searched class is in
	 */
	private static function getClassPath($className, $basePath = CORE_PATH) {
		$parts = explode('_', $className);
		$parts = array_map('strtolower', $parts);

		//"normal" classes
		for ($i = count($parts)-1; $i >= 0; $i--) {
			$path = $basePath;
			for ($j = 0; $j < $i; $j++)
				$path .= '/'.$parts[$j];
			$file = '';
			for ($j = $i; $j < count($parts); $j++)
				$file .= $parts[$j];
			$path .= '/'.$file.'.php';

			if (self::correctClassPath($className, $path))
				return $path;
		}
		
		//framework classes
		$path = $basePath.'/'.$className.'/'.$className.'.php';
		if (self::correctClassPath($className, $path))
			return $path;
		$path = $basePath.'/'.$className.'.php';
		if (self::correctClassPath($className, $path))
			return $path;

		if (!$GLOBALS['memcache']->get('CORE_booted'))
			throw new Core_Exception('Tried to load a class before engine finished booting.');

		return false;
	}
	
	/**
	 * Checks whether a given path really is the file in which the given class is
	 * located in. If the path is valid it is cached.
	 * @param $className the searched class
	 * @param $path the path the searched class might be in
	 * @return boolean true if the path is correct, false otherwhise
	 */
	private static function correctClassPath($className, $path) {
		if (file_exists($path)) {
			$GLOBALS['memcache']->set($className, $path);
			return true;
		}
		return false;
	}
}

/**
 * Dumps values in readable format
 */
function dump() {
	foreach (func_get_args() as $arg) {
		if ('cli'==PHP_SAPI)
		var_dump($arg);
		else {
			if (empty($GLOBALS['ob_flushed']))
				$GLOBALS['ob_flushed']=true;
			echo '<div class="ob_dump" style="display:inline-block; position:relative;z-index:1000;"><table style="background-color:green;border:1px solid black;margin-top:5px;"><tr><td style="color:white;"><pre>';
			var_dump($arg);
			echo '</pre></td></tr></table></div>';
		}
	}
}

?>