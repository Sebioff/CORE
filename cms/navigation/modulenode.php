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