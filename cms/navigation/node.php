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