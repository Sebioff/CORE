<?php

/**
 * Provides a navigation for modules.
 */
class CMS_Navigation {
	private $nodes = array();
	
	public function addModuleNode($nodeTitle, Module $module) {
		$node = new CMS_Navigation_Node($nodeTitle, $module);
		$this->nodes[] = $node;
	}
	
	public function display() {
		$result = '<ul class="core_navigation">';
		$i = 0;
		$nodeCount = count($this->nodes);
		foreach ($this->nodes as $node) {
			$classes = array('core_navigation_node');
			if ($i == 0)
				$classes[] = 'core_navigation_node_first';
			if ($i == $nodeCount - 1)
				$classes[] = 'core_navigation_node_last';
			if ($node->isActive()) {
				$classes[] = 'core_navigation_node_active';
			}
			$result .= '<li class="'.implode(' ', $classes).'">';
			$result .= $node->getLink()->render();
			$result .= '</li>';
			$i++;
		}
		$result .= '</ul>';
		echo $result;
	}
}

?>