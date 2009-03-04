<?php

require_once 'core/memorycache.php'; // can't be autoloaded since the autoloader uses this class

class App {
	private static $instance = null;
	private $modules = array();

	// CONSTRUCTION ------------------------------------------------------------
	private function __construct() {
		// Singleton
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
		date_default_timezone_set('Europe/Berlin');
		$GLOBALS['memcache'] = new Core_MemoryCache();
		spl_autoload_register(array('App_Autoloader', 'autoload'));
		set_error_handler(array('Core_ErrorHandler', 'handleError'));
		set_exception_handler(array('Core_ExceptionHandler', 'handleException'));

		$backtrace = debug_backtrace();
		define('PROJECT_PATH', realpath(dirname($backtrace[0]['file']).'/..'));

		// first boot
		if (!$GLOBALS['memcache']->get('CORE_booted')) {
			self::systemCheck();
		}
		
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
			require_once PROJECT_PATH.'/config/environments/config.development.php';
			Core_MigrationsLoader::load();
		}
		else
			require_once PROJECT_PATH.'/config/environments/config.live.php';
		
		// get project modules
		require_once PROJECT_PATH.'/config/modules.php';	

		// TODO: make use of language scriptlet configurable in project
		// initialize language scriptlet
		Language_Scriptlet::get()->init();
		// initialize router
		Router::get()->init();
		
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT)
			HTMLTidy::tidy();
		
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
		if (!in_array($module->getName(), $this->modules)) {
			$this->modules[$module->getName()] = $module;
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
		if (!isset($this->modules[$name]))
			return null;
		else
			return $this->modules[$name];
	}
	
	/**
	 * Magic function such as get{name of module}Module
	 * @return depends on type of magic method; in this case: the module
	 */
	public function __call($name, $params) {
		if (preg_match('/^get(.*)Module$/', $name, $matches)) {
			$module = $this->getModule(ucfirst($matches[1]));
			if (!$module)
				$module = $this->getModule(strtolower($matches[1]));
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
		$extensions = array('mbstring', 'gd', 'mysql');
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT)
			$extensions[] = 'tidy';
		foreach( $extensions as $extension)
			if (!extension_loaded($extension))
				throw new Core_Exception('Please verify your PHP configuration: extension "'.$extension.'" should be loaded.');
		
		// TODO: handle magic_quotes in db_connection so it can be turned on and off
		foreach (array('register_globals'=>0, 'magic_quotes_runtime'=>0, 'short_open_tag'=>1) as $option=>$value)
			if ($value != ini_get($option))
				throw new Core_Exception('Please verify your PHP configuration: '.$option.' should be "'.$value.'", but is "'.ini_get($option).'".');
	}
}

// -----------------------------------------------------------------------------

/**
 * Responsible for loading classes.
 */
class App_Autoloader {
	// CUSTOM METHODS ----------------------------------------------------------
	public static function autoload($className) {
		$path = null;
		
		// is path cached?
		if (($path = $GLOBALS['memcache']->get($className)))
			if (file_exists($path))
				require_once $path;
				
		// path not cached or wrong cached, search for class
		if (!$path || !class_exists($className, false)) {
			$parts = explode('_', $className);
			$isProjectClass = ($parts[0] == PROJECT_NAME);
			
			if ($isProjectClass) {
				array_shift($parts);
				$basePath = PROJECT_PATH.'/source';
			}
			else
				$basePath = CORE_PATH;
				
			$parts = array_map('strtolower', $parts);
			
			$path = self::loadClass($className, $parts, $basePath);
			// TODO some paths are searched twice, try for example Security_User_CoreAdmin
			if (!$isProjectClass) {
				if (!$path)
					$path = self::loadClass($className, $parts, $basePath.'/'.$parts[0]);
				if (!$path)
					$path = self::loadClass($className, $parts, $basePath.'/core');
				if (!$path)
					$path = self::loadClass($className, $parts, $basePath.'/core/'.$parts[0]);
			}
		}
		
		if (!class_exists($className, false))
			trigger_error(sprintf('Class \'%s\' not found', $className), E_USER_ERROR);
	}

	/**
	 * Gets the path of the file a given class is located in
	 * @param $className the searched class
	 * @param $parts the splitted parts the classname is constructed of
	 * @param $basePath a base path relative to which the search is made
	 * @return String the path of the file the searched class is in
	 */
	private static function loadClass($className, Array $parts, $basePath) {
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

			if ($i != count($parts)-1) {
				$path = $basePath;
				for ($j = 0; $j < $i; $j++)
					$path .= '/'.$parts[$j];
				$path .= '/'.$parts[count($parts)-1].'.php';
				if (self::correctClassPath($className, $path))
					return $path;
			}
		}
		return null;
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
			require_once $path;
			if (class_exists($className, false)) {
				$GLOBALS['memcache']->set($className, $path);
				return true;
			}
		}
		return false;
	}
}

// -----------------------------------------------------------------------------

/* GLOBALLY AVAILABLE FUNCTIONS */

/**
 * Dumps values in readable format
 */
function dump() {
	Core_Dump::dump(func_get_args());
}
/**
 * For dumping objects in readable format
 */
function dump_flat() {
	Core_Dump::dump_flat(func_get_args());
}

?>