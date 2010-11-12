<?php

/**
 * Provides a navigation for modules.
 * The navigation node hierarchy needs to be created from top to bottom or the behaviour
 * might be undefined.
 */
class CMS_Navigation {
	private $headNode = null;
	
	public function __construct() {
		$this->headNode = new CMS_Navigation_HeadNode();
	}
	
	/**
	 * Adds a new navigation node.
	 * @param $node CMS_Navigation_Node
	 */
	public function addNode(CMS_Navigation_Node $node) {
		$this->headNode->addNode($node);
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
	
	public function display() {
		echo $this->headNode->render();
	}
}

class CMS_Navigation_HeadNode extends CMS_Navigation_Node {
	public function __construct() {
		parent::__construct('');
	}
	
	public function renderTitle() {
		return '';
	}
	
	public function getLink() {
		return null;
	}
	
	public function isActive() {
		return false;
	}
}

?>