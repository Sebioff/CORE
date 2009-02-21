<?php

class Router {
	private static $instance = null;
	/** contains static routes = routes to files/folders */
	private $staticRoutes = array();
	/** mapping of all top-level-routenames to their corresponding module objects **/
	private $moduleRoutes = array();
	/** routename of the currenctly active module */
	private $route = null;
	/** contains the information which route params are given for each module */
	private $params = array();
	/** the single sections of the current URI */
	private $requestParams = null;
	
	private function __construct() {
		// Singleton
		$this->addModuleRoute('core', new CoreRoutes_Core('coreroutes'));
	}
	
	/**
	 * generates an array for each module specified in the uri
	 * eg: /module/param1/param2
	 * => array('module'=>module, 'params'=>array(param1,param2));
	 * @return array
	 */
	private function generateParams() {
		$modules = 0;
		$params = array();
		foreach($this->requestParams as $param)
		{
			if(isset($this->moduleRoutes[$param])) {
				$modules++;
				$params[] = array('module' => $param, 'params' => array());
			}
			else {
				$params[$modules]['params'][] = $param;
			}
		}
		$this->params = $params;
	}
	
	public function init() {
		require_once '../config/routes.php';
		
		$languageScriptlet = Language_Scriptlet::get();
		
		$requestURI = explode('/', ltrim($_SERVER['REQUEST_URI'] , '/'));
		$this->requestParams = $requestURI;
		
		$firstParam = array_shift($requestURI);
		if($languageScriptlet->isLanguageIdentifier($firstParam)) {
			$this->route = array_shift($requestURI);
			$languageScriptlet->setCurrentLanguage($firstParam);
		}
		elseif(isset($this->moduleRoutes[$firstParam]) && !($this->moduleRoutes[$firstParam] instanceof CoreRoutes_Core))
			$languageScriptlet->switchToDefaultLanguage();
		else
			$this->route = $firstParam;
			
		$this->generateParams();
		
		if(!isset($this->moduleRoutes[$this->route]))
			throw new Core_Exception('Route to module does not exist: '.$this->route);
		
		$module = $this->getCurrentModule();
		$module->init();
		$module->display();
	}
	
	public function addModuleRoute($routeName, Module $module) {
		if(!in_array($routeName, $this->moduleRoutes))
			$this->setModuleRoute($routeName, $module);
		else
			throw new Core_Exception('A module route with this name has already been added: '.$routeName);
	}
	
	public function setModuleRoute($routeName, Module $module) {
		$this->moduleRoutes[$routeName] = $module;
	}
	
	/**
	 * @return the currently active module
	 */
	public function getCurrentModule() {
		return $this->moduleRoutes[$this->route];
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
	
	/**
	 * Transforms a path to a file/folder on the disk (but below project/CORE-root!)
	 * to a path that can be used in html (e.g. for images, inclusion of css/js files, ...)
	 */
	public function transformPathToHTMLPath($path) {
		return './../../'.IO_Utils::getRelativePath($path);
	}
	
	public function getParams() {
		return $this->params;
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}

?>