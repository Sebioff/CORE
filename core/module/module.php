<?php

class Module {
	public $contentPanel = 'GUI_Panel';
	
	protected $mainPanel = 'GUI_Panel_Main';
	
	private $name = '';
	private $routeName = '';
	private $jsRouteReferences = array();
	private $cssRouteReferences = array();
	private $submodules = array();
	private $parent = null;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	// CUSTOM METHODS ----------------------------------------------------------
	public function init() {
		$this->contentPanel = new $this->contentPanel($this->name.'_content');
		$this->mainPanel = new $this->mainPanel('main', $this);
		$this->mainPanel->addClasses($this->name.'_main');
	}
	
	public function addSubmodule(Module $submodule) {
		$this->submodules[$submodule->getRouteName()] = $submodule;
		$submodule->setParent($this);
	}
	
	public function getSubmodule($moduleRouteName) {
		if (isset($this->submodules[$moduleRouteName]))
			return $this->submodules[$moduleRouteName];
		else
			return null;
	}
	
	public function getSubmoduleByName($moduleName) {
		foreach ($this->submodules as $submodule)
			if ($submodule->getName() == $moduleName)
				return $submodule;
		return null;
	}
	
	public function display() {
		$this->mainPanel->render();
	}
	
	public function addJsRouteReference($routeName, $path) {
		$this->jsRouteReferences[] = Router::get()->getStaticRoute($routeName, $path);
	}
	
	public function getJsRouteReferences() {
		return $this->jsRouteReferences;
	}
	
	public function addCssRouteReference($routeName, $path) {
		$this->cssRouteReferences[] = Router::get()->getStaticRoute($routeName, $path);
	}
	
	public function getCssRouteReferences() {
		return $this->cssRouteReferences;
	}
	
	public function getRoute() {
		$route = $this->getRouteName();
		$module = $this;
		
		while ($module = $module->getParent()) {
			$route = $module->getRouteName().'/'.$route;
		}
		
		if (count(Language_Scriptlet::get()->getAvailableLanguages()) > 0)
			$route = Language_Scriptlet::get()->getCurrentLanguage().'/'.$route;
		
		return PROJECT_ROOTURI.'/'.$route;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getRouteName() {
		return ($this->routeName) ? $this->routeName : $this->name;
	}
	
	public function setRouteName($routeName) {
		$this->routeName = $routeName;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(Module $parentModule) {
		$this->parent = $parentModule;
	}
}

?>