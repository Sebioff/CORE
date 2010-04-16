<?php

/**
 * Scriptlets are simple php scripts that are reachable under a specific route
 */
class Scriptlet {
	private $name = '';
	private $routeName = '';
	private $parent = null;
	private $submodules = array();
	
	public function __construct($name) {
		if ($name != Text::toLowerCase($name))
			throw new Core_Exception('Use lowercase scriptlet names.');
			
		$this->name = $name;
		$this->routeName = $name;
	}
	
	/**
	 * @return the url to this scriptlet.
	 */
	public function getUrl(array $params = array()) {
		$route = $this->getRouteName();
		$module = $this;
		
		while ($module = $module->getParent()) {
			$route = $module->getRouteName().'/'.$route;
		}
		
		if (count(Language_Scriptlet::get()->getAvailableLanguages()) > 1)
			$route = Language_Scriptlet::get()->getCurrentLanguage().'/'.$route;
		
		$completeRoute = PROJECT_ROOTURI.'/'.$route;
		
		foreach ($params as $param => $value) {
			$completeRoute .= '/'.$param.'_'.$value;
		}
			
		return $completeRoute;
	}
	
	/**
	 * @param $name
	 * @return string the value of the scriptlet parameter identified by $name
	 */
	public function getParam($name) {
		$params = Router::get()->getParamsForScriptlet($this);
		if (isset($params[$name]))
			return $params[$name];
		else
			return null;
	}
	
	/**
	 * @return array of params for this scriptlet
	 */
	public function getParams() {
		return Router::get()->getParamsForScriptlet($this);
	}
	
	public function display() {
		
	}
	
	public function init() {
		
	}
	
	public function beforeInit() {
		
	}
	
	public function afterInit() {
		
	}
	
	public static function redirect($url) {
	    header('Status: 302 Moved Temporarily');
	    header('Location: '.$url);
	    exit;
	}
	
	public function addSubmodule(Scriptlet $submodule) {
		$this->submodules[$submodule->getRouteName()] = $submodule;
		$submodule->setParent($this);
	}
	
	/**
	 * @return Module
	 */
	// TODO: use $moduleName here instead of routename
	// change getSubmoduleByName to getSubmoduleByRouteName
	public function getSubmodule($moduleRouteName) {
		if (isset($this->submodules[$moduleRouteName])) {
			if (!($this->submodules[$moduleRouteName] instanceof Scriptlet_Privileged) || $this->submodules[$moduleRouteName]->checkPrivileges())
				return $this->submodules[$moduleRouteName];
		}
		else {
			return null;
		}
	}
	
	/**
	 * @return Module
	 */
	public function getSubmoduleByName($moduleName) {
		foreach ($this->submodules as $submodule) {
			if ($submodule->getName() == $moduleName) {
				if (!($submodule instanceof Scriptlet_Privileged) || $submodule->checkPrivileges())
					return $submodule;
				else
					return null;
			}
		}
		return null;
	}
	
	// TODO privilege checks are ignored here
	public function getAllSubmodules() {
		return $this->submodules;
	}
	
	public function hasSubmodule($moduleRouteName) {
		return (isset($this->submodules[$moduleRouteName]) && (!($this->submodules[$moduleRouteName] instanceof Scriptlet_Privileged) || $this->submodules[$moduleRouteName]->checkPrivileges()));
	}

	public function hasSubmodules() {
		return !empty($this->submodules);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return Module
	 */
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(Scriptlet $parentModule) {
		$this->parent = $parentModule;
	}
	
	public function getRouteName() {
		return $this->routeName;
	}
	
	public function setRouteName($routeName) {
		$this->routeName = $routeName;
	}
	
	public function getName() {
		return $this->name;
	}
}

?>