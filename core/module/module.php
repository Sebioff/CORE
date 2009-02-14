<?php

class Module {
	protected $contentPanel = 'GUI_Panel';
	
	private $name = null;
	private $routeName = null;
	
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
	}
}

?>