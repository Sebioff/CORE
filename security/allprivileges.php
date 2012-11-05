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
 * Security context with all privileges.
 */
class Security_AllPrivileges extends Security {
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public static function get($class = '', $name = '') { // TODO remove $class with php 5.3
		return parent::get(__CLASS__, $name);
	}
	
	public function hasPrivilege(DB_Record $user = null, $privilegeIdentifier) {
		return true;
	}
	
	/*
	 * TODO this is not neccessarily the intended behaviour!
	 * Needs rethinking.
	 */
	public function isInGroup(DB_Record $user, $groupIdentifier) {
		return true;
	}
	
	/**
	 * @return String prefix to be used for needed database tables
	 */
	protected function getTablePrefix() {
		return '';
	}
}

?>