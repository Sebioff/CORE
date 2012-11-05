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

class GUI_Control_Link extends GUI_Control_Submittable {
	private $url = '#';
	private $caption = '';
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $caption, $url, $title = '') {
		parent::__construct($name, $caption, $title);
		
		$this->url = $url;
		$this->caption = $caption;
		$this->setTemplate(dirname(__FILE__).'/link.tpl');
		$this->addClasses('core_gui_link');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
	}
}

?>