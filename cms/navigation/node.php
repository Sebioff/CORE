<?php

abstract class CMS_Navigation_Node {
	protected $nodes = array();
	private $title = '';
	private $cssClasses = array();
	private $level = 0;
	
	public function __construct($title, $cssClasses = array()) {
		$this->title = $title;
		$this->cssClasses = $cssClasses;
	}
	
	public function render() {
		$result = $this->renderTitle();
		if ($this->nodes) {
			$result .= '<ul class="'.(($this->getLevel() == 0) ? 'core_navigation' : 'core_navigation_level'.$this->getLevel()).'">';
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
				if ($node->isActive()) {
					$classes[] = 'core_navigation_node_active';
					$classes[] = 'core_navigation_node_inpath';
				}
				elseif ($node->isInPath()) {
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
	
	public function renderTitle() {
		 return $this->getLink()->render();
	}
	
	/**
	 * @return GUI_Control_Link
	 */
	public abstract function getLink();
	
	/**
	 * @return boolean true if this node or any of its sub nodes is active, false
	 * otherwise
	 */
	public function isInPath() {
		if ($this->isActive())
			return true;
		
		// is a module of any subnode of this node active?
		foreach ($this->nodes as $node) {
			if ($node->isInPath())
				return true;
		}
		
		return false;
	}
	
	/**
	 * @return boolean true if this node represents the currently active page, false
	 * otherwise
	 */
	public abstract function isActive();
	
	/**
	 * Adds a new sub navigation node.
	 * @param $node CMS_Navigation_Node
	 */
	public function addNode(CMS_Navigation_Node $node) {
		$node->setLevel($this->getLevel() + 1);
		$this->nodes[] = $node;
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
	
	public function getLevel() {
		return $this->level;
	}
	
	public function setLevel($level) {
		$this->level = $level;
	}
}

?>