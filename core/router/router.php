<?php

class Router {
	private static $instance = null;
	/** contains static routes = routes to files/folders */
	private $staticRoutes = array();
	/** mapping of all top-level-routenames to their corresponding module objects **/
	private $moduleRoutes = array();
	/** routename of the topmost module */
	private $route = null;
	/** contains the information which route params are given for each module */
	private $params = array();
	/** the single sections of the current URI */
	private $requestParams = null;
	
	private function __construct() {
		// Singleton
		$this->addModuleRoute('core', new CoreRoutes_Core('coreroutes'));
		$this->addStaticRoute('core_css', dirname(__FILE__).'/../../www/css');
		$this->addStaticRoute('core_js', dirname(__FILE__).'/../../www/js');
	}
	
	/**
	 * generates an array for each module specified in the uri
	 * eg: /module/param1/param2
	 * => array('module'=>module, 'params'=>array(param1,param2));
	 * @return array
	 */
	private function generateParams() {
		$modules = -1;
		$params = array();
		$lastModule = null;
		$currentModule = null;
		foreach ($this->requestParams as $param) {
			if (isset($this->moduleRoutes[$param])) {
				$modules++;
				$lastModule = array('module' => $param, 'params' => array(), 'submodule' => array());
				$currentModule = $this->moduleRoutes[$param];
				$params[] = &$lastModule;
			}
			elseif (isset($currentModule) && $module = $currentModule->getSubmodule($param)) {
				$currentModule = $module;
				$lastModule['submodule'][] = array('module' => $param, 'params' => array(), 'submodule' => array());
				$lastModule = &$lastModule['submodule'][count($lastModule['submodule']) - 1];
			}
			elseif (isset($params[$modules])) {
				$paramArray = explode('_', $param, 2);
				$lastModule['params'][$paramArray[0]] = isset($paramArray[1]) ? $paramArray[1] : null;
			}
		}
		$this->params = $params;
	}
	
	public function init() {
		require_once PROJECT_PATH.'/config/routes.php';
		
		$languageScriptlet = Language_Scriptlet::get();
		
		$requestURI = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
		$this->requestParams = $requestURI;
		
		$languageIdentifierSet = false;
		while (!$this->route && $requestURI) {
			$firstParam = array_shift($requestURI);
			if ($languageScriptlet->isLanguageIdentifier($firstParam)) {
				$this->requestParams = $requestURI;
				$languageScriptlet->setCurrentLanguage($firstParam);
				$languageIdentifierSet = true;
			}
			elseif (isset($this->moduleRoutes[$firstParam])) {
				$this->route = $firstParam;
			}
			else {
				$this->requestParams = $requestURI;
			}
		}
		
		$this->generateParams();
		
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])?'https':'http';
		$rootURI = $protocol.'://'.$_SERVER['SERVER_NAME'];
		$route = implode('/', $this->getRequestParams());
		if ($languageIdentifierSet)
			$route = $languageScriptlet->getCurrentLanguage().'/'.$route;
		$rootURI .= str_replace($route, '', $_SERVER['REQUEST_URI']);
		define('PROJECT_ROOTURI', rtrim($rootURI, '/'));
		
		if (!$languageIdentifierSet && count($languageScriptlet->getAvailableLanguages()) > 1 && !($this->moduleRoutes[$this->route] instanceof CoreRoutes_Core))
			$languageScriptlet->switchToDefaultLanguage();
			
		if (!isset($this->moduleRoutes[$this->route]))
			throw new Core_Exception('Route to module does not exist: '.$this->route);
	}
	
	public function runCurrentModule() {
		$module = $this->getCurrentModule();
		$module->beforeInit();
		$module->init();
		$module->afterInit();
		$module->display();
	}
	
	public function addModuleRoute($routeName, Module $module) {
		if (!in_array($routeName, $this->moduleRoutes))
			$this->setModuleRoute($routeName, $module);
		else
			throw new Core_Exception('A module route with this name has already been added: '.$routeName);
	}
	
	public function setModuleRoute($routeName, Module $module) {
		$this->moduleRoutes[$routeName] = $module;
	}
	
	/**
	 * @return Module the currently active module
	 */
	public function getCurrentModule() {
		$currentModule = $this->moduleRoutes[$this->route];
		
		$module = isset($this->params[0])?$this->params[0]:null;
		while (isset($module['submodule'][0]['module'])) {
			$currentModule = $currentModule->getSubmodule($module['submodule'][0]['module']);
			$module = $module['submodule'][0];
		}
		
		if ($currentModule)
			return $currentModule;
		else
			throw new Core_Exception('Module doesn\'t exist.');
	}
	
	public function getParamsForModule(Module $searchedModule) {
		$module = $searchedModule;
		$path = array($module->getRouteName());
		while ($module = $module->getParent()) {
			$path[] = $module->getRouteName();
		}
		
		$pathItems = count($path) - 1;
		$module = isset($this->params[0])?$this->params[0]:null;
		while(isset($module['module'])) {
			if ($module['module'] == $searchedModule->getRouteName()) {
				break;
			}
			if (!isset($module['submodule'][0]))
				break;
			$module = $module['submodule'][0];
			$pathItems--;
		}
		
		return $module['params'];
	}
	
	/**
	 * Adds a route to a static file, e.g. stylesheets, JavaScript-files...
	 * @param $routeName
	 * @param $path the path to where this route links to
	 */
	public function addStaticRoute($routeName, $path) {
		$this->staticRoutes[$routeName] = $path;
	}
	
	public function getStaticRoute($routeName, $path) {
		if (isset($this->staticRoutes[$routeName]))
			return $this->transformPathToHTMLPath($this->staticRoutes[$routeName].'/'.$path);
	}
	
	/**
	 * Transforms a path to a file/folder on the disk to a path relative to document
	 * root that can be used in html (e.g. for images, inclusion of css/js files, ...)
	 */
	public function transformPathToHTMLPath($path) {
		return '/'.IO_Utils::getRelativePath($path, $_SERVER['DOCUMENT_ROOT']);
	}
	
	public function getParams() {
		return $this->params;
	}
	
	public function getRequestParams() {
		return $this->requestParams;
	}
	
	public static function get() {
		return (self::$instance) ? self::$instance : self::$instance = new self();
	}
}

?>