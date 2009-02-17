<?php

class Router {
	// TODO PWO: codingstyle! a=b -> a = b
	private static $instance=null;
	/** contains static routes = routes to files/folders */
	private $staticRoutes=array();
	/**
	 * moduleRoutes = array of linkling string to modules
	 * route = current route (mostyl first param)
	 * params = array of params (following after route param in URL)
	 * requestParams = params in for of an URI
	 * mainPanel = tha panel to be displayed everytime
	 */
	private $moduleRoutes=array();
	private $route=null;
	private $params=array();
	private $requestParams=null;
	private $mainPanel=null;
	
	private function __construct($mainPanel=null) {
		// Singleton
		$this->mainPanel=$mainPanel;
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
		// TODO PWO if !object instanceof class -> !(object instanceof class) or it won't work ;P
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
	
	public function addModuleRoute($routeName, Module $module, $override=false) {
		if(!in_array($routeName, $this->moduleRoutes)&&!$override) {
			$this->moduleRoutes[$routeName]=$module;
		}
		else
			throw new Core_Exception('A module route with this name has already been added: '.$routeName);
	}
	
	public function setMainPanel($panelName) {
		$this->mainPanel=$panelName;
		$this->addModuleRoute('', new $this->mainPanel('main'));
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
	
	public static function get($mainPanel=null) {
		return (self::$instance) ? self::$instance : self::$instance = new self($mainPanel);
	}
}

?>