<?php

class Module {
	protected $contentPanel = 'GUI_Panel';
	protected $mainPanel = 'GUI_Panel';
	
	private $name = null;
	private $routeName = null;
	private $jsRouteReferences = array();
	private $submodules = array();
	
	public function __construct($name) {
		$this->name = $name;
		$this->mainPanel = new $this->mainPanel('main');
		$this->mainPanel->addClasses($this->name.'_main');
	}
	
	public function init() {
		$this->contentPanel = new $this->contentPanel($this->name.'_content');
	}
	
	public function setRouteName($routeName) {
		$this->routeName = $routeName;
	}
	
	public function getRouteName() {
		return ($this->routeName) ? $this->routeName : $this->name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function addSubmodule(Module $submodule) {
		$this->submodules[$submodule->getRouteName()] = $submodule;
	}
	
	public function getSubmodule($moduleRouteName) {
		if(isset($this->submodules[$moduleRouteName]))
			return $this->submodules[$moduleRouteName];
		else
			return null;
	}
	
	public function display() {
		$this->mainPanel->params->contentPanel = $this->contentPanel;
		$this->mainPanel->display();
	}
	
	public function addJsRouteReference($routeName, $path) {
		$this->jsRouteReferences[] = array('routeName' => $routeName, 'path' => $path);
		dump($this->jsRouteReferences);
	}
	
	public function displayJsIncludes() {
		foreach($this->jsRouteReferences as $jsRouteReference) {
			dump(Router::get()->getStaticRoute($jsRouteReference['routeName']));
		}
	}
}

?>