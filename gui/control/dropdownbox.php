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

class GUI_Control_DropDownBox extends GUI_Control {
	private $values = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, array $values, $defaultValue = null, $title = '') {
		parent::__construct($name, $defaultValue, $title);

		$this->values = $values;
		
		$this->setTemplate(dirname(__FILE__).'/dropdownbox.tpl');
		$this->addClasses('core_gui_dropdownbox');
	}
	
	public function getValues() {
		return $this->values;
	}
	
	public function getValue() {
		return $this->values[$this->getKey()];
	}
	
	public function getKey() {
		return $this->value;
	}
}

?>