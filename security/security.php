<?php

// TODO use late static binding for singleton instance in PHP 5.3
/**
 * Provides functions for security protection.
 * Users are associated with groups.
 * Privileges can be assigned to groups.
 * In order to use automatic database table creation you need to register your
 * security object using Security::register() in your modules.php
 */
abstract class Security {
	private $privileges = array();
	private $containerUsers = null;
	private $containerGroups = null;
	private $containerGroupsUsersAssoc = null;
	private $containerPrivileges = null;
	
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
		$usersGroupAssoc = new DB_Record();
		$usersGroupAssoc->user = $user;
		$usersGroupAssoc->userGroup = $userGroup;
		$this->getContainerGroupsUsersAssoc()->save($usersGroupAssoc);
	}
	
	public function getGroup($groupIdentifier) {
		return $this->getContainerGroups()->selectByGroupIdentifierFirst($groupIdentifier);
	}
	
	/**
	 * @return array of users that belong to the group identified by groupIdentifier
	 */
	public function getGroupUsers($groupIdentifier) {
		// FIXME can be done in one query with joins
		$assocEntries = $this->getContainerGroupsUsersAssoc()->selectByUserGroup($this->getGroup($groupIdentifier));
		if (empty($assocEntries))
			return array();
		$groupUsers = array();
		foreach ($assocEntries as $assocEntry)
			$groupUsers[] = $assocEntry->user;
		$condition = 'id IN ('.implode(', ', $groupUsers).')';
		$options = array();
		$options['conditions'][] = array($condition);
		return $this->getContainerUsers()->select($options);
	}
	
	/**
	 * @return array of groups the given user belongs to
	 */
	public function getUserGroups(DB_Record $user) {
		$users = $this->getContainerUsersTableName();
		$groupsUsersAssoc = $this->getContainerGroupsUsersAssocTableName();
		$groups = $this->getContainerGroupsTableName();
		$options = array();
		$options['join'] = array($groupsUsersAssoc, $users);
		$options['conditions'][] = array($groups.'.id = '.$groupsUsersAssoc.'.user_group');
		$options['conditions'][] = array($groupsUsersAssoc.'.user = '.$users.'.id');
		return $this->getContainerGroups()->select($options);
	}
	
	/**
	 * @return boolean true if the given user belongs to the group identified by
	 * the given groupIdentifier
	 */
	public function isInGroup(DB_Record $user, $groupIdentifier) {
		if ($groupIdentifier instanceof DB_Record)
			$group = $groupIdentifier;
		else
			$group = $this->getGroup($groupIdentifier);
		$options = array();
		$options['conditions'][] = array('user = ?', $user);
		$options['conditions'][] = array('user_group = ?', $group);
		$assocs = $this->getContainerGroupsUsersAssoc()->selectFirst($options);
		return (!empty($assocs));
	}
	
	/**
	 * @return array containing all users with the specified privilege
	 */
	public function getPrivilegedUsers($privilegeIdentifier) {
		$users = $this->getContainerUsersTableName();
		$groupsUsersAssoc = $this->getContainerGroupsUsersAssocTableName();
		$groups = $this->getContainerGroupsTableName();
		$privileges = $this->getContainerPrivilegesTableName();
		// get all users with positively defined right
		$options = array();
		$options['join'] = array($groups, $groupsUsersAssoc, $privileges);
		$options['conditions'][] = array($privileges.'.value = ?', true);
		$options['conditions'][] = array($privileges.'.privilege = ?', $privilegeIdentifier);
		$options['conditions'][] = array($privileges.'.user_group = '.$groups.'.id');
		$options['conditions'][] = array($groupsUsersAssoc.'.user_group = '.$groups.'.id');
		$options['conditions'][] = array($groupsUsersAssoc.'.user = '.$users.'.id');
		$privilegedUsers = $this->getContainerUsers()->select($options);
		$privilegedUsersIDs = implode(', ', $privilegedUsers);
		// get all users with negatively defined right
		$options = array();
		$options['join'] = array($groups, $groupsUsersAssoc, $privileges);
		$options['conditions'][] = array($privileges.'.value = ?', false);
		$options['conditions'][] = array($privileges.'.privilege = ?', $privilegeIdentifier);
		$options['conditions'][] = array($privileges.'.user_group = '.$groups.'.id');
		$options['conditions'][] = array($groupsUsersAssoc.'.user_group = '.$groups.'.id');
		$options['conditions'][] = array($groupsUsersAssoc.'.user = '.$users.'.id');
		$unprivilegedUsers = $this->getContainerUsers()->select($options);
		$unprivilegedUsersIDs = implode(', ', $unprivilegedUsers);
		// check for undefined users
		$options = array();
		if (!empty($privilegedUsers))
			$options['conditions'][] = array('id NOT IN ('.$privilegedUsersIDs.')');
		if (!empty($unprivilegedUsers))
			$options['conditions'][] = array('id NOT IN ('.$unprivilegedUsersIDs.')');
		$undefinedUsers = $this->getContainerUsers()->select($options);
		foreach ($undefinedUsers as $undefinedUser) {
			if ($this->getDefaultValue($privilegeIdentifier, $undefinedUser))
				$privilegedUsers[] = $undefinedUser;
		}
		return $privilegedUsers;
	}
	
	public function hasPrivilege(DB_Record $user = null, $privilegeIdentifier) {
		if (!$user)
			return false;
			
		$privilegeDefined = false;

		// TODO due to the new joins-feature this should be doable in one query
		foreach ($this->getContainerGroupsUsersAssoc()->selectByUser($user->getPK()) as $userGroupAssoc) {
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
			return $this->getDefaultValue($privilegeIdentifier, $user);
	}
	
	public function groupHasPrivilege(DB_Record $group, $privilegeIdentifier) {
		$privilegeDefined = false;
		
		foreach ($this->getContainerPrivileges()->selectByUserGroup($group) as $privilege) {
			if ($privilege->privilege == $privilegeIdentifier) {
				if ($privilege->value == false)
					return false;
				$privilegeDefined = true;
			}
		}
		
		if ($privilegeDefined)
			return true;
		else
			return false;
	}
	
	/**
	 * Override this method if you want to have custom default values for your
	 * privileges.
	 */
	protected function getDefaultValue($privilegeIdentifier, DB_Record $user) {
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
	
	public function onSetup() {
		$queries = array();
		// TODO use migration-executing method as soon as available
		require dirname(__FILE__).'/migrations/001.setup.php';
		foreach ($queries as $query)
			DB_Connection::get()->query($query);
	}
	
	/**
	 * @param $tablePrefix to be used for database tables
	 * @param $securityImpl your implementation of Security
	 */
	public static function register(Security $securityImpl) {
		CoreRoutes_Reset::addCallbackOnAfterReset(array($securityImpl, 'onSetup'));
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
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
	
	public function setContainerUsers($containerUsers) {
		$this->containerUsers = $containerUsers;
	}
	
	public function setContainerGroups($containerGroups) {
		$this->containerGroups = $containerGroups;
	}
	
	public function setContainerGroupsUsersAssoc($containerGroupsUsersAssoc) {
		$this->containerGroupsUsersAssoc = $containerGroupsUsersAssoc; 
	}
	
	public function setContainerPrivileges($setContainerPrivileges) {
		$this->containerPrivileges = $setContainerPrivileges;
	}
	
	// ABSTRACT METHODS --------------------------------------------------------
	/** 
	 * @return DB_Container the container for users
	 */
	public function getContainerUsers() {
		if ($this->containerUsers)
			return $this->containerUsers;
			
		$this->containerUsers = new DB_Container($this->getContainerUsersTableName());
		
		return $this->containerUsers;
	}
	
	/** 
	 * @return DB_Container the container for groups
	 */
	public function getContainerGroups() {
		if ($this->containerGroups)
			return $this->containerGroups;
			
		$this->containerGroups = new DB_Container($this->getContainerGroupsTableName());
		
		return $this->containerGroups;
	}
	
	/** 
	 * @return DB_Container the container for the association between users and groups
	 */
	public function getContainerGroupsUsersAssoc() {
		if ($this->containerGroupsUsersAssoc)
			return $this->containerGroupsUsersAssoc;
			
		$this->containerGroupsUsersAssoc = new DB_Container($this->getContainerGroupsUsersAssocTableName());
		
		return $this->containerGroupsUsersAssoc;
	}
	
	/** 
	 * @return DB_Container the container for privileges
	 */
	public function getContainerPrivileges() {
		if ($this->containerPrivileges)
			return $this->containerPrivileges;
			
		$this->containerPrivileges = new DB_Container($this->getContainerPrivilegesTableName());
		
		return $this->containerPrivileges;
	}
	
	/** 
	 * @return String name of the user table
	 */
	protected function getContainerUsersTableName() {
		return $this->getTablePrefix().'_users';
	}
	
	/** 
	 * @return String name of the groups table
	 */
	protected function getContainerGroupsTableName() {
		return $this->getTablePrefix().'_groups';
	}
	
	/** 
	 * @return String name of the groups/users-assoc table
	 */
	protected function getContainerGroupsUsersAssocTableName() {
		return $this->getTablePrefix().'_groups_users_assoc';
	}
	
	/** 
	 * @return String name of the privileges table
	 */
	protected function getContainerPrivilegesTableName() {
		return $this->getTablePrefix().'_privileges';
	}
	
	/** 
	 * @return String prefix to be used for needed database tables
	 */
	protected abstract function getTablePrefix();
}

?>