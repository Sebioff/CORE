<?php

class Module {
	protected $contentPanel = 'GUI_Panel';
	
	private $name = null;
	private $routeName = null;
	private $jsRouteReferences = array();
	
	public function __construct($name) {
		$this->name = $name;
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
	
	public function display() {
		$this->contentPanel->display();
		$this->displayJsIncludes();
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