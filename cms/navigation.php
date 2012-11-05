<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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