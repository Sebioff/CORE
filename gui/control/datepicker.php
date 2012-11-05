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

class GUI_Control_DatePicker extends GUI_Control {
	// CONSTRUCTORS ------------------------------------------------------------
	public function __construct($name, $defaultDateTime = 0, $title = '') {
		parent::__construct($name, $defaultDateTime, $title);
		
		$this->setTemplate(dirname(__FILE__).'/datepicker.tpl');
		$this->addClasses('core_gui_datepicker');
	}
	
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function afterInit() {
		parent::afterInit();
		
		$this->getModule()->addJsRouteReference('core_js', 'jquery/jquery-ui.js');
		$this->getModule()->addCssRouteReference('core_js', 'jquery/css/smoothness/jquery-ui.css');
		$this->addJS(
			sprintf('
				$(function() {
					$("#%s").datepicker({dateFormat: "dd.mm.yy", firstDay: 1});
				});
			', $this->getID())
		);
	}
	
	public function getValue() {
		$strValue = parent::getValue();
		if ($strValue) {
			$parts = explode('.', $strValue);
			return mktime(0, 0, 0, $parts[1], $parts[0], $parts[2]);
		}
		else
			return $strValue;
	}
}

?>