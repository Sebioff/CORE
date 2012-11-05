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

class GUI_Panel_Date extends GUI_Panel {
	const FORMAT_DATETIME = 'd.m.Y, H:i:s';
	const FORMAT_DATE = 'd.m.Y';
	const FORMAT_TIME = 'H:i:s';
	
	private $time = 0;
	private $format;
	
	public function __construct($name, $time = 0, $title = '', $format = self::FORMAT_DATETIME) {
		parent::__construct($name, $title);
		
		if ($time == 0)
			$time = time();
		$this->time = $time;
		$this->format = $format;
		$this->setTemplate(dirname(__FILE__).'/date.tpl');
		$this->addClasses('core_gui_date');
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function getValue() {
		return date($this->format, $this->getTime()).(in_array($this->format, array(self::FORMAT_TIME, self::FORMAT_DATETIME)) ? ' Uhr' : '');
	}
	
	public function getTime() {
		return $this->time;
	}
	
	public function setFormat($format) {
		$this->format = $format;
	}
}

?>