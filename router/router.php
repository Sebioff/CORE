<?php
class Router {
	private static $instance = null;
	private $staticRoutes = array();
	private $moduleRoutes = array();
	private $route = null;
	private $params = array();
	private $requestUri = null;
	
	private function __construct() {
		// Singleton
		$this->addModuleRoute('core', new Core_Routes('coreroutes'));
	}
	
	private function generateParams() {
		foreach($this->requestUri as $URI)
		{
			if((isset($this->moduleRoutes[$URI])&&$this->route!=$URI))
				$this->params[]['module']=$URI;
			else
				$this->params[]['param']=$URI;
		}
	}
	
	public function init() {
		require_once '../config/routes.php';
		
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$i=0;
		foreach($requestURI as $URI) {
			if(!mb_strlen($URI))
				unset($requestURI[$i]);
			else if(!$this->route) {
				$this->route=$URI;
				unset($requestURI[$i]);
			}
			$i++;
		}
		
		$this->requestUri=$requestURI;
		$this->generateParams();
		
		if (!isset($this->moduleRoutes[$this->route]))
			throw new Core_Exception('Route to module does not exist: '.$this->route);
		
		$module = $this->moduleRoutes[$this->route];
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
	
	public function getParams() {
		return $this->params;
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}
?>