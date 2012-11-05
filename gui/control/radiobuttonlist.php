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

class GUI_Control_RadioButtonList extends GUI_Control {
	private $items = array();
	
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $title = '') {
		parent::__construct($name, null, $title);
		
		$this->setTemplate(dirname(__FILE__).'/radiobuttonlist.tpl');
		$this->addClasses('core_gui_radiobuttonlist');
	}
	
	public function addItem($title, $value = '', $checked = false) {
		$name = 'item'.count($this->items);
		$this->addItemRadiobutton(new GUI_Control_RadioButton($name, $value, $checked, $title));
	}
	
	public function addItemRadiobutton(GUI_Control_RadioButton $radio) {
		$radio->setGroup($this->getID());
		$this->addPanel($radio);
		$this->items[] = $radio;
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getItems() {
		return $this->items;
	}
	
	public function getValue() {
		foreach ($this->getItems() as $item) {
			if ($item->getSelected())
				return $item->getValue();
		}
		return null;
	}
}

?>