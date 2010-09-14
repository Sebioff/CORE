<?php

class CMS_Navigation_Node {
	private $title = '';
	private $cssClasses = array();
	private $module = null;
	private $nodes = array();
	private $level = 1;
	
	public function __construct(Module $module, $title, $cssClasses = array()) {
		$this->title = $title;
		$this->module = $module;
		$this->cssClasses = $cssClasses;
	}
	
	public function render() {
		$result = $this->getLink()->render();
		if ($this->nodes) {
			$result .= '<ul class="core_navigation_level'.$this->getLevel().'">';
			$i = 0;
			$nodeCount = count($this->nodes);
			foreach ($this->nodes as $node) {
				$classes = $node->getCssClasses();
				$classes[] = 'core_navigation_node';
				if ($nodeCount > 1 && $i == 0)
					$classes[] = 'core_navigation_node_first';
				if ($nodeCount > 1 && $i == $nodeCount - 1)
					$classes[] = 'core_navigation_node_last';
				if ($nodeCount == 1)
					$classes[] = 'core_navigation_node_single';
				if ($node->isActive($node->getModule())) {
					$classes[] = 'core_navigation_node_active';
					$classes[] = 'core_navigation_node_inpath';
				}
				elseif ($node->isInPath($node->getModule())) {
					$classes[] = 'core_navigation_node_inpath';
				}
				$result .= '<li class="'.implode(' ', $classes).'">';
				$result .= $node->render();
				$result .= '</li>';
				$i++;
			}
			$result .= '</ul>';
		}
		return $result;
	}
	
	public function getLink() {
		return new GUI_Control_Link('core_navigation_node_link', $this->getTitle(), $this->module->getUrl());
	}
	
	public function isInPath(Module $module) {
		if ($this->isActive($module))
			return true;
		
		// is a module of any subnode of this node active?
		foreach ($this->nodes as $node) {
			if ($node->isInPath($node->getModule()))
				return true;
		}
		
		// is any submodule of this node active?
		foreach ($module->getAllSubmodules() as $subModule) {
			if ($this->isInPath($subModule))
				return true;
		}
		
		return false;
	}
	
	public function isActive(Module $module) {
		return (Router::get()->getCurrentModule() == $module);
	}
	
	/**
	 * Adds a new navigation node for a given module.
	 * @param $nodeTitle text to display for the module
	 * @param $module
	 * @return CMS_Navigation_Node the newly added navigation node
	 */
	public function addModuleNode(Module $module, $nodeTitle, $cssClasses = array()) {
		$node = new CMS_Navigation_Node($module, $nodeTitle, $cssClasses);
		$node->setLevel($this->getLevel() + 1);
		$this->nodes[] = $node;
		return $node;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getCssClasses() {
		return $this->cssClasses;
	}
	
	/**
	 * @return Module
	 */
	public function getModule() {
		return $this->module;
	}
	
	public function setModule(Module $module) {
		$this->module = $module;
	}
	
	public function getLevel() {
		return $this->level;
	}
	
	public function setLevel($level) {
		$this->level = $level;
	}
}

?>