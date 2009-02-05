<?php
class Router {
	private static $instance = null;
	private $staticRoutes = array();
	private $moduleRoutes = array();
	
	private function __construct() {
		// Singleton
	}
	
	public function init() {
		require_once '../config/routes.php';
		
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		if (!isset($this->moduleRoutes[$requestURI[1]]))
			throw new Core_Exception('Route to module does not exist: '.$requestURI[1]);
		
		$module = $this->moduleRoutes[$requestURI[1]];
		$module->init();
		$module->display();
	}
	
	public function addModuleRoute($routeName, Module $module) {
		if(!in_array($routeName, $this->moduleRoutes)) {
			$this->moduleRoutes[$routeName]=$module;
		}
		else
			throw new Core_Exception('A module route with this name has already been added: '.$routeName);
	}
	
	/**
	 * Adds a route to a static file, e.g. stylesheets, JavaScript-files...
	 * @param $routeName
	 * @param $path the path to where this route links to
	 */
	public function addStaticRoute($routeName, $path) {
		$this->staticRoutes[$routeName] = '/'.$path;
	}
	
	public function getStaticRoute($routeName) {
		return $this->staticRoutes[$routeName];
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}
?>