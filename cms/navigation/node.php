<?php

class CMS_Navigation_Node {
	private $title = '';
	private $module = null;
	
	public function __construct($title, Module $module) {
		$this->title = $title;
		$this->module = $module;
	}
	
	public function getLink() {
		return new GUI_Control_Link('core_navigation_node_link', $this->getTitle(), $this->module->getRoute());
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getModule() {
		return $this->module;
	}
	
	public function setModule($module) {
		$this->module = $module;
	}
}

?>