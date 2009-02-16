<?php
class Router {
	private static $instance = null;
	private $staticRoutes = array();
	private $moduleRoutes = array();
	private $route = null;
	private $params = array();
	private $requestParams = null;
	
	private function __construct() {
		// Singleton
		$this->addModuleRoute('core', new Core_Routes('coreroutes'));
	}
	
	/**
	 * generates an array for each module specified in the uri
	 * eg: /module/param1/param2
	 * => array('module'=>module, 'params'=>array(param1,param2));
	 * @return array
	 */
	private function generateParams() {
		$i=-1;
		$params=array();
		foreach($this->requestParams as $param)
		{
			if(in_array($param, $this->moduleRoutes)) {
				$i++;
				$params[$i]=array('module'=>$param, 'params'=>array());
			}
			else {
				if(!isset($params[$i]['module'])) {
					$i++;
				}
				$params[$i]['params'][]=$param;
			}
		}
		$this->params=$params;
	}
	
	public function init() {
		require_once '../config/routes.php';
		
		$languageScriptlet=Language_Scriptlet::get();
		
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		
		array_shift($requestURI);
		$firstParam=array_shift($requestURI);
		if($languageScriptlet->isLanguageParam($firstParam)) {
			$this->route=array_shift($requestURI);
			$languageScriptlet->setLanguage($firstParam);
		}
		elseif(isset($this->moduleRoutes[$firstParam]) && !$this->moduleRoutes[$firstParam] instanceof Core_Routes)
			$languageScriptlet->switchToDefaultLanguage();
		else
			$this->route=$firstParam;
			
		$this->requestParams=$requestURI;
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