<?php

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