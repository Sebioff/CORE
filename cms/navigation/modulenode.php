<?php

class CMS_Navigation_ModuleNode extends CMS_Navigation_UrlNode {
	private $module = null;
	
	public function __construct(Module $module, $title, $cssClasses = array()) {
		parent::__construct($module->getUrl(), $title, $cssClasses);
		$this->module = $module;
	}
	
	private function isModuleInPath(Module $module) {
		if (Router::get()->getCurrentModule() == $module)
			return true;
		
		foreach ($module->getAllSubmodules() as $subModule) {
			if ($this->isModuleInPath($subModule))
				return true;
		}
		
		return false;
	}
	
	public function isInPath() {
		if (parent::isInPath())
			return true;
			
		if ($this->isModuleInPath($this->getModule()))
			return true;
		else
			return false;
	}
	
	public function isActive() {
		return (Router::get()->getCurrentModule() == $this->getModule());
	}
	
	/**
	 * Adds a new navigation node for a given module.
	 * @deprecated TODO remove as soon as it isn't used in Rakuun source anymore, use addNode() instead
	 * @param $nodeTitle text to display for the module
	 * @param $module
	 * @return CMS_Navigation_ModuleNode the newly added navigation node
	 */
	public function addModuleNode(Module $module, $nodeTitle, $cssClasses = array()) {
		$node = new CMS_Navigation_ModuleNode($module, $nodeTitle, $cssClasses);
		$this->addNode($node);
		return $node;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return Module
	 */
	public function getModule() {
		return $this->module;
	}
	
	public function setModule(Module $module) {
		$this->module = $module;
	}
}

?>