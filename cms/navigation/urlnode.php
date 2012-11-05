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

class CMS_Navigation_UrlNode extends CMS_Navigation_Node {
	private $url = null;
	
	public function __construct($url, $title, $cssClasses = array()) {
		parent::__construct($title, $cssClasses);
		$this->url = $url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 * @return GUI_Control_Link
	 */
	public function getLink() {
		return new GUI_Control_Link('core_navigation_node_link', $this->getTitle(), $this->url);
	}
	
	public function isActive() {
		// TODO this isn't exactly correct, e.g. think of url params
		return (Router::get()->getCurrentModule()->getUrl() == $this->url);
	}
}

?>