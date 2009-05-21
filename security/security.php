<?php

// TODO use late static binding for singleton instance in PHP 5.3
abstract class Security {
	private $privileges = array();
	private $containerUsers = null;
	private $containerUserGroups = null;
	private $containerUserGroupsUsersAssoc = null;
	private $containerPrivileges = null;
	
	public function __construct() {
		$this->containerUserGroups = $this->defineContainerUserGroups();
		$this->containerUserGroupsUsersAssoc = $this->defineContainerUserGroupsUsersAssoc();
		$this->containerPrivileges = $this->defineContainerPrivileges();
		$this->containerUsers = $this->defineContainerUsers();
	}
	
	public function setPrivilege($privilegeIdentifier, DB_Record $userGroup, $value = true) {
		$options = array();
		$options['conditions'][] = array('user_group = ?', $userGroup);
		$options['conditions'][] = array('privilege = ?', $privilegeIdentifier);
		if ($privilege = $this->getContainerPrivileges()->selectFirst($options)) {
			if ($privilege->value != $value) {
				$privilege->value = $value;
				$privilege->save();
			}
		}
		else {
			$privilege = new DB_Record();
			$privilege->privilege = $privilegeIdentifier;
			$privilege->userGroup = $userGroup;
			$privilege->value = $value;
			$this->getContainerPrivileges()->save($privilege);
		}
	}
	
	public function addToGroup(DB_Record $user, DB_Record $userGroup) {
		$userGroup = new DB_Record();
		$userGroup->user = $user;
		$userGroup->userGroup = $userGroup;
		$this->getContainerGroupsUsersAssoc()->save($userGroup);
	}
	
	public function getGroup($groupIdentifier) {
		return $this->getContainerUserGroups()->selectByGroupIdentifierFirst($groupIdentifier);
	}
	
	public function hasPrivilege(DB_Record $user, $privilegeIdentifier) {
		$privilegeDefined = false;
		
		foreach ($this->getContainerUserGroupsUsersAssoc()->selectByUser($user->getPK()) as $userGroupAssoc) {
			foreach ($this->getContainerPrivileges()->selectByUserGroup($userGroupAssoc->userGroup) as $privilege) {
				if ($privilege->privilege == $privilegeIdentifier) {
					if ($privilege->value == false)
						return false;
					$privilegeDefined = true;
				}
			}
		}
		
		if ($privilegeDefined)
			return true;
		else
			return $this->getDefaultValue($privilegeIdentifier);
	}
	
	/**
	 * Override this method if you want to have custom default values for your
	 * privileges.
	 */
	protected function getDefaultValue($privilegeIdentifier) {
		return false;
	}
	
	/**
	 * Defines a name (and optinally a description) for a privilege.
	 * It's not neccessary to define a privilege here in order to use it; it
	 * can just be handy for handling privileges.
	 */
	public function definePrivilege($privilegeIdentifier, $privilegeName, $privilegeDescription = '') {
		$this->privileges[$privilegeIdentifier]['name'] = $privilegeName;
		$this->privileges[$privilegeIdentifier]['description'] = $privilegeDescription;
	}
	
	public function getPrivilegeName($privilegeIdentifier) {
		if (isset($this->privileges[$privilegeIdentifier]))
			return $this->privileges[$privilegeIdentifier]['name'];
		else
			return '';
	}
	
	public function getPrivilegeDescription($privilegeIdentifier) {
		if (isset($this->privileges[$privilegeIdentifier]['description']))
			return $this->privileges[$privilegeIdentifier]['description'];
		else
			return '';
	}
	
	public function getDefinedPrivileges() {
		return array_keys($this->privileges);
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainerUsers() {
		return $this->containerUsers;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainerUserGroups() {
		return $this->containerUserGroups;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainerUserGroupsUsersAssoc() {
		return $this->containerUserGroupsUsersAssoc;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainerPrivileges() {
		return $this->containerPrivileges;
	}
	
	public function setContainerUsers($containerUsers) {
		$this->containerUsers = $containerUsers;
	}
	
	public function setContainerUserGroups($containerUserGroups) {
		$this->containerUserGroups = $containerUserGroups;
	}
	
	public function setContainerUserGroupsUsersAssoc($containerUserGroupsUsersAssoc) {
		$this->containerUserGroupsUsersAssoc = $containerUserGroupsUsersAssoc; 
	}
	
	public function setContainerPrivileges($setContainerPrivileges) {
		$this->containerPrivileges = $setContainerPrivileges;
	}
	
	/** @return DB_Container */
	protected abstract function defineContainerUsers();
	/** @return DB_Container */
	protected abstract function defineContainerUserGroups();
	/** @return DB_Container */
	protected abstract function defineContainerUserGroupsUsersAssoc();
	/** @return DB_Container */
	protected abstract function defineContainerPrivileges();
}

?>