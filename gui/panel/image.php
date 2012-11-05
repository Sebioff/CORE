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

class GUI_Panel_Image extends GUI_Panel {
	private $url = '';
	private $description = '';
	
	public function __construct($name, $url, $description = '', $title = '') {
		parent::__construct($name, $title);
		
		if ($title && !$description)
			$description = $title;
		$this->setDescription($description);
		$this->setURL($url);
		$this->setTemplate(dirname(__FILE__).'/image.tpl');
	}

	// GETTERS / SETTERS -------------------------------------------------------
	public function getURL() {
		return $this->url;
	}
	
	public function setURL($url) {
		$this->url = $url;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
}

?>