<?php

/**
 * Provides a navigation for modules.
 * TODO needs refactoring; addModuleNode and display have duplicate code in CMS_Navigation_Node
 */
class CMS_Navigation {
	private $nodes = array();
	
	/**
	 * Adds a new navigation node for a given module.
	 * @param $nodeTitle text to display for the module
	 * @param $module
	 * @return CMS_Navigation_Node the newly added navigation node
	 */
	public function addModuleNode(Module $module, $nodeTitle, $cssClasses = array()) {
		$node = new CMS_Navigation_Node($module, $nodeTitle, $cssClasses);
		$this->nodes[] = $node;
		return $node;
	}
	
	public function display() {
		$result = '<ul class="core_navigation">';
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
		echo $result;
	}
}

?>