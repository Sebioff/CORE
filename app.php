<?php

require_once 'core/memorycache.php'; // can't be autoloaded since the autoloader uses this class

class App {
	public static $projectName = null;
	public static $instance = null;

	private $modules = array();

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

		if (self::$projectName != null)
			throw new Core_Exception('Double boot');
			
		$backtrace = debug_backtrace();
		$projectPath = explode('/', str_replace('\\', '/', dirname($backtrace[0]['file'])));
		self::$projectName = min($projectPath);
		
		// first boot
		if (!$GLOBALS['memcache']->get('CORE_booted')) {
			self::systemCheck();
		}
		
		// TODO: migrations should only in development environment be loaded every
		// time. On live: probably by calling some route
		Core_MigrationsLoader::load();
		
		// get project modules
		require_once '../config/modules.php';	
		
		// initialize router
		Router::get()->init();
		
		$GLOBALS['memcache']->set('CORE_booted', true);
	}

	public static function getPathFromUnderscore($filename_) {
		return implode('/', explode('_', $filename_));
	}

	/**
	 * Registers a module.
	 * @param $name_ the name of the new module
	 * @param $module_ the module
	 */
	public function addModule(Module $module) {
		if(!in_array($module->getName(), $this->modules)) {
			$this->modules[$module->getName()]=$module;
			Router::get()->addModuleRoute($module->getRouteName(), $module);
		}
		else
			throw new Core_Exception('A module with this name has already been added: '.$name);
	}

	/**
	 * Returns a registered module
	 * @param $name_ the name of the module
	 * @return the module or null if it doesn't exist
	 */
	public function getModule($name) {
		if(!isset($this->modules[$name]))
			return null;
		else
			return $this->modules[$name];
	}
	
	/**
	 * Magic function such as get{name of module}Module
	 * @return depends on type of magic method; in this case: the module
	 */
	public function __call($name, $params) {
		if(preg_match('/^get(.*)Module$/', $name, $matches)) {
			$module=$this->getModule(ucfirst($matches[1]));
			if(!$module)
				$module=$this->getModule(strtolower($matches[1]));
			return $module;
		}
		else
			throw new Core_Exception('Call to a non existent function or magic method: '.$name);
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
	
	/**
	 * Checks that basic server configurations are set as needed
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

// -----------------------------------------------------------------------------

/**
 * Responsible for loading classes.
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
	private static function getClassPath($className) {
		$parts = explode('_', $className);
		$isProjectClass = ($parts[0] == App::$projectName);
		
		if ($isProjectClass)
			$basePath = '../..';
		else
			$basePath = '../../CORE';
			
		$parts = array_map('strtolower', $parts);

		// "normal" classes
		for($i = count($parts)-1; $i >= 0; $i--) {
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
		
		// framework classes
		if(!$isProjectClass) {
			$path = $basePath.'/'.$className.'/'.$className.'.php';
			if (self::correctClassPath($className, $path))
				return $path;
			$path = $basePath.'/'.$className.'.php';
			if (self::correctClassPath($className, $path))
				return $path;
		}

		if(!$GLOBALS['memcache']->get('CORE_booted'))
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
		if(file_exists($path)) {
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
	Core_Dump::dump(func_get_args());
}
function dump_flat() {
	Core_Dump::dump_flat(func_get_args());
}

?>