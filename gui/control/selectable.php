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
 * Base class for selectable items like checkboxes or radiobuttons
 */
abstract class GUI_Control_Selectable extends GUI_Control {
	protected $checkedDefaultValue = false;
	protected $checkedValue = false;
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $value = '', $checked = false, $title = '') {
		if ($value === '')
			$value = $name;
		parent::__construct($name, $value, $title);
		
		$this->checkedDefaultValue = $checked;
	}
	
	protected function generateID() {
		parent::generateID();
		
		if (!empty($_POST)) {
			if (isset($_POST[$this->getID()])) {
				$this->checkedValue = true;
			}
		}
		else {
			$this->checkedValue = $this->checkedDefaultValue;
		}
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public abstract function getGroup();
	
	public abstract function setGroup($group);
	
	public function getSelected() {
		return $this->checkedValue;
	}
	
	public function setSelected($checked) {
		$this->checkedDefaultValue = $checked;
	}
}

?>