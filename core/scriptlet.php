<?php

class Scriptlet {
	private $name = '';
	private $routeName = '';
	private $parent = null;
	
	public function __construct($name) {
		if ($name != Text::toLowerCase($name))
			throw new Core_Exception('Use lowercase scriptlet names.');
			
		$this->name = $name;
		$this->routeName = $name;
	}
	
	/**
	 * @return the url to this scriptlet.
	 */
	public function getUrl($params = array()) {
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
	
	public function getParam($name) {
		$params = Router::get()->getParamsForScriptlet($this);
		if (isset($params[$name]))
			return $params[$name];
		else
			return null;
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
	    header('Status: 301 Moved Permanently');
	    header('Location: '.$url);
	    exit;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return Module
	 */
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(Module $parentModule) {
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