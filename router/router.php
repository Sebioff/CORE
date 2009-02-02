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
		$module = $this->moduleRoutes[$requestURI[1]];
		if ($module) {
			$module->init();
			$module->display();
		}
	}
	
	public function addModuleRoute($routeName, Module $module) {
		if(!in_array($routeName, $this->moduleRoutes)) {
			$this->moduleRoutes[$routeName]=$module;
		}
		else
			throw new Core_Exception('A module route with this name has already been added: '.$routeName);
	}
	
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