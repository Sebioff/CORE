<?php

// TODO replace all dirname(__FILE__) with __DIR__ with PHP 5.3

require_once 'core/cache/cache.php'; // can't be autoloaded since the cache (see below) uses this class
require_once 'core/cache/global/session.php'; // can't be autoloaded since the autoloader uses this class

/**
 * This class represents the single point of entrance for the web application.
 * It is mainly responsible for setting up basic settings and managing the main
 * modules.
 *
 * Magic methods:
 * @method Module getMODULENAMEModule()
 *
 * Required defines:
 * CORE_PATH				- path on the server to CORE's main folder (defined in constants.php)
 * PROJECT_NAME				- unique name of the project (defined in constants.php)
 * PROJECT_VERSION			- current project version, should be increased with each build (defined in config/constants.php)
 * DB_CONNECTION			- defines the main db connection, see class DB_Connection (defined in config/environments/config.*.php)
 *
 * Available defines:
 * PROJECT_PATH				- path to the projects main folder
 * PROJECT_ROOTURI			- root uri of the project (might be wrong in CLI mode, can be overwritten in config)
 * DS						- shortcut for DIRECTORY_SEPARATOR
 *
 * Optional defines:
 * CORE_MAILSENDER			- standard sender for CORE's mail functions if no sender is explicitly given
 * CORE_ENABLE_LOGGING		- can be set to "false" to disable logging
 * CORE_ENABLE_URLREWRITE	- can be set to "false" to disable rewriting urls even if mod_rewrite is available
 * CORE_TEMPORARY_DIRECTORY - can be used to define a different temporary folder than the one of the OS, see System::getTemporaryDirectory()
 * CORE_LOG_SLOW_QUERIES	- can be set to an amount of milliseconds to log all queries that take more time than this
 * PROJECT_ENVIRONMENT		- overrides the environment that is automatically detected by Environment::getCurrentEnvironment()
 *
 * Callback defines:
 * CALLBACK_ERROR			- executed as soon as an error occurs. If not defined the error message and a backtrace will be printed
 * CALLBACK_ONAFTERRESET	- executed after the project has been reset
 * CALLBACK_MAINTENANCE		- if the application is in maintenance mode this callback will be executed if defined
 */
class App {
	private static $instance = null;
	
	private $modules = array();
	private $maintenanceModeLockfilePath = '';

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
		ob_start();
		if (PHP_SAPI == 'cli') {
			$_SERVER['REQUEST_URI'] = 'http://localhost/'.$_SERVER['argv'][1];
			$_SERVER['SERVER_NAME'] = '';
			// TODO fill in the actual ip address with PHP 5.3 (-> gethostbyname(gethostname()))
			$_SERVER['SERVER_ADDR'] = '127.0.0.1';
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
			$_SERVER['HTTP_ACCEPT_ENCODING'] = '';
			$_SERVER['HTTP_USER_AGENT'] = 'CLI';
		}
		error_reporting(E_ALL|E_STRICT);
		ini_set('default_charset', 'utf-8');
		header('Content-type: text/html; charset=utf-8');
		date_default_timezone_set('Europe/Berlin');
		define('DS', DIRECTORY_SEPARATOR);
		$backtrace = debug_backtrace();
		define('PROJECT_PATH', realpath(dirname($backtrace[0]['file']).'/..'));
		// setup autoloading (before session_start() or deserialization won't work)
		spl_autoload_register(array('App_Autoloader', 'autoload'));
		// overwrite $_SESSION
		session_start();
		if (!isset($_SESSION[PROJECT_PATH]))
			$_SESSION[PROJECT_PATH] = array();
		$GLOBALS['_SESSION'] = &$_SESSION[PROJECT_PATH];
		$GLOBALS['cache'] = new Cache_Global_Session();
		App_Autoloader::loadClassesByURL();
		// register error handlers
		set_error_handler(array('Core_ErrorHandler', 'handleError'));
		set_exception_handler(array('Core_ExceptionHandler', 'handleException'));
		register_shutdown_function(array('Core_ErrorHandler', 'onShutdown'));
		$app = self::get();
		$app->maintenanceModeLockfilePath = PROJECT_PATH.'/config/maintenance.lock';
		
		// first boot
		if (!$GLOBALS['cache']->get('CORE_booted')) {
			self::systemCheck();
		}
		
		if ($app->isMaintenanceModeEnabled()) {
			if (defined('CALLBACK_MAINTENANCE'))
				call_user_func(CALLBACK_MAINTENANCE);
			else
				exit;
		}
		
		// load configuration files
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
			require_once PROJECT_PATH.'/config/environments/config.development.php';
		}
		else {
			ini_set('display_errors', 0);
			require_once PROJECT_PATH.'/config/environments/config.live.php';
		}
		
		// add project migration folder
		Core_MigrationsLoader::addMigrationFolder(PROJECT_PATH.'/migrations');
		
		// get project modules
		require_once PROJECT_PATH.'/config/modules.php';
		
		// initialize language scriptlet
		Language_Scriptlet::get()->init();
		// initialize I18N
		I18N::get();
		
		// initialize router
		Router::get()->init();
		
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
			// always check for changed migrations on development (except when resetting)
			if (!(Router::get()->getCurrentModule() instanceof CoreRoutes_Reset))
				Core_MigrationsLoader::load();
		}
		
		Router::get()->runCurrentModule();
		
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT) {
			if (Router::get()->getCurrentModule() instanceof Module) {
				HTMLTidy::tidy();
			}
		}
		else {
			/*
			 * TODO there is an issue with using dump()/dump_flat() when using
			 * gzip. ATM not so important since gzip is only used on LIVE (where
			 * dumps shouldn't be needed anyway)
			 */
			// output gzip'ed content
			if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && function_exists('gzencode')
				&& (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false
				|| strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false)
			) {
				header('Content-Encoding: gzip');
				header('Vary: Accept-Encoding');
				echo gzencode(ob_get_clean());
			}
		}
		
		if (!$GLOBALS['cache']->get('CORE_booted')) {
			$GLOBALS['cache']->set('CORE_booted', true);
		}
	}

	/**
	 * Registers a module.
	 * @param $module_ the module
	 * @throws Core_Exception if a module with the same name already exists
	 */
	public function addModule(Scriptlet $module) {
		if (!in_array($module->getName(), $this->modules)) {
			$this->modules[$module->getName()] = $module;
			Router::get()->addScriptletRoute($module->getRouteName(), $module);
		}
		else
			throw new Core_Exception('A module with this name has already been added: '.$name);
	}

	/**
	 * Returns a registered module
	 * @param $name_ the name of the module
	 * @return Module the module or null if it doesn't exist
	 */
	public function getModule($name) {
		if (!isset($this->modules[$name]))
			return null;
		else
			return $this->modules[$name];
	}
	
	/**
	 * Returns all available modules.
	 * @return array of Modules
	 */
	public function getModules() {
		return $this->modules;
	}
	
	/**
	 * Magic function such as get{name of module}Module
	 * @return depends on type of magic method; in this case: the module
	 */
	public function __call($name, $params) {
		if (preg_match('/^get(.*)Module$/', $name, $matches)) {
			$module = $this->getModule(Text::camelCaseToUnderscore($matches[1]));
			return $module;
		}
		else
			throw new Core_Exception('Call to a non existent function or magic method: '.$name);
	}
	
	/**
	 * @param $enableMaintenanceMode boolean true if maintenance mode should be
	 * enabled (default), false if maintenance mode should be disabled
	 */
	public function enableMaintenanceMode($enableMaintenanceMode = true) {
		$lockfile = new IO_File($this->maintenanceModeLockfilePath);
		if ($enableMaintenanceMode) {
			$lockfile->create();
		}
		else {
			$lockfile->delete();
		}
	}
	
	/**
	 * @return boolean true if maintenance mode is enabled, false otherwise
	 */
	public function isMaintenanceModeEnabled() {
		$lockfile = new IO_File($this->maintenanceModeLockfilePath);
		return $lockfile->exists();
	}
	
	/**
	 * @return App
	 */
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
	
	/**
	 * Checks that basic server configurations are set as needed
	 * @throws Core_Exception if something is wrong with the server configuration
	 */
	private static function systemCheck() {
		$extensions = array('mbstring', 'gd', 'mysql');
		if (Environment::getCurrentEnvironment() == Environment::DEVELOPMENT)
			$extensions[] = 'tidy';
		
		foreach ($extensions as $extension)
			if (!extension_loaded($extension))
				throw new Core_Exception('Please verify your PHP configuration: extension "'.$extension.'" should be loaded.');
		
		foreach (array(/*'register_globals' => 0, */'magic_quotes_runtime' => 0, 'magic_quotes_gpc' => 0, 'short_open_tag' => 1) as $option => $value)
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
	public static function loadClassesByURL() {
		if ($classPaths = $GLOBALS['cache']->get('classPaths'.$_SERVER['REQUEST_URI'])) {
			foreach ($classPaths as $classPath) {
				if (file_exists($classPath)) {
					require_once $classPath;
				}
			}
		}
	}
	
	public static function autoload($className) {
		$path = null;
		
		// is path cached?
		if (isset($GLOBALS['cache']) && $path = $GLOBALS['cache']->get($className)) {
			if (file_exists($path)) {
				// cache all class paths belonging to this url
				$classPaths = $GLOBALS['cache']->get('classPaths'.$_SERVER['REQUEST_URI']);
				if (!$classPaths || !in_array($path, $classPaths)) {
					$classPaths[] = $path;
					$GLOBALS['cache']->set('classPaths'.$_SERVER['REQUEST_URI'], $classPaths);
				}
				require $path;
			}
		}
		
		// path not cached or wrong cached, search for class
		if ((!class_exists($className, false) && !interface_exists($className, false)) || !$path) {
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
			if (!$isProjectClass) {
				if (!$path)
					$path = self::loadClass($className, $parts, $basePath.'/'.$parts[0]);
				if (!$path)
					$path = self::loadClass($className, $parts, $basePath.'/core');
				if (!$path)
					$path = self::loadClass($className, $parts, $basePath.'/core/'.$parts[0]);
			}
		}
		
		if (!class_exists($className, false) && !interface_exists($className, false))
			trigger_error(sprintf('Class \'%s\' not found', $className), E_USER_ERROR);
	}
	
	// TODO add public method to get a classes file location

	/**
	 * Gets the path of the file a given class is located in
	 * @param $className the searched class
	 * @param $parts the splitted parts the classname is constructed of
	 * @param $basePath a base path relative to which the search is made
	 * @return String the path of the file the searched class is in
	 */
	private static function loadClass($className, Array $parts, $basePath) {
		$partsCount = count($parts);
		for ($i = $partsCount - 1; $i >= 0; $i--) {
			// Just_Some_Class -> just/some/class.php, just/someclass.php, ...
			$path = $basePath;
			for ($j = 0; $j < $i; $j++)
				$path .= '/'.$parts[$j];
			$file = '';
			for ($j = $i; $j < $partsCount; $j++)
				$file .= $parts[$j];
			$path .= '/'.$file.'.php';
			if (self::correctClassPath($className, $path))
				return $path;

			if ($i != $partsCount - 1) {
				// Just_Some_Class -> just/class.php, some.php, ...
				$path = $basePath;
				for ($j = 0; $j < $i; $j++)
					$path .= '/'.$parts[$j];
				$path .= '/'.$parts[$partsCount - 1].'.php';
				if (self::correctClassPath($className, $path))
					return $path;
			}
		}
		
		if (!empty($parts)) {
			// Just_Some_Class -> just/some/class/class.php
			$path = $basePath.'/'.implode('/', $parts).'/'.$parts[$partsCount - 1].'.php';
			if (self::correctClassPath($className, $path))
					return $path;
			
			// Maybe it's defined in the file of the parent class?
			// Try searching Just_Some, Just, ... as well
			array_pop($parts);
			self::loadClass($className, $parts, $basePath);
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
			if (class_exists($className, false) || interface_exists($className, false)) {
				if (isset($GLOBALS['cache']))
					$GLOBALS['cache']->set($className, $path);
				return true;
			}
		}
		return false;
	}
}

// -----------------------------------------------------------------------------

/* GLOBALLY AVAILABLE FUNCTIONS */

/**
 * Dumps detailed information about its input
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

/**
 * Prints a backtrace
 */
function backtrace() {
	Core_Dump::backtrace();
}

?>