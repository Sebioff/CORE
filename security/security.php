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
	
	public function __construct() {
		// FIXME horrible, horrible abuse of exceptions
		try {
			$container = $this->defineContainerGroups();
			$this->containerGroups = new $container[0]($container[1]);
			$container = $this->defineContainerGroupsUsersAssoc();
			$this->containerGroupsUsersAssoc = new $container[0]($container[1]);
			$container = $this->defineContainerPrivileges();
			$this->containerPrivileges = new $container[0]($container[1]);
			$container = $this->defineContainerUsers();
			$this->containerUsers = new $container[0]($container[1]);
		}
		catch (Core_QueryException $qe) {
			if (!(Router::get()->getCurrentModule() instanceof CoreRoutes_Reset))
				throw $qe;
		}
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
		$usersGroupAssoc = new DB_Record();
		$usersGroupAssoc->user = $user;
		$usersGroupAssoc->userGroup = $userGroup;
		$this->getContainerGroupsUsersAssoc()->save($usersGroupAssoc);
	}
	
	public function getGroup($groupIdentifier) {
		return $this->getContainerGroups()->selectByGroupIdentifierFirst($groupIdentifier);
	}
	
	public function getGroupUsers($groupIdentifier) {
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
	
	public function getUserGroups(DB_Record $user) {
		$assocEntries = $this->getContainerGroupsUsersAssoc()->selectByUser($user);
		if (empty($assocEntries))
			return array();
		$userGroups = array();
		foreach ($assocEntries as $assocEntry)
			$userGroups[] = $assocEntry->userGroup;
		$condition = 'id IN ('.implode(', ', $userGroups).')';
		$options = array();
		$options['conditions'][] = array($condition);
		return $this->getContainerGroups()->select($options);
	}
	
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
	
	public function getPrivilegedUsers($privilegeIdentifier) {
		$users = $this->getContainerUsers()->getTable();
		$groupsUsersAssoc = $this->getContainerGroupsUsersAssoc()->getTable();
		$groups = $this->getContainerGroups()->getTable();
		$privileges = $this->getContainerPrivileges()->getTable();
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
	
	/**
	 * @return DB_Container
	 */
	public function getContainerUsers() {
		return $this->containerUsers;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainerGroups() {
		return $this->containerGroups;
	}
	
	/**
	 * @return DB_Container
	 */
	public function getContainerGroupsUsersAssoc() {
		return $this->containerGroupsUsersAssoc;
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
	/** Must return an array with a classname that inherits from DB_Container at
	 * index 0 and a table name at index 1 */
	protected abstract function defineContainerUsers();
	/** Must return an array with a classname that inherits from DB_Container at
	 * index 0 and a table name at index 1 */
	protected abstract function defineContainerGroups();
	/** Must return an array with a classname that inherits from DB_Container at
	 * index 0 and a table name at index 1 */
	protected abstract function defineContainerGroupsUsersAssoc();
	/** Must return an array with a classname that inherits from DB_Container at
	 * index 0 and a table name at index 1 */
	protected abstract function defineContainerPrivileges();
}

?>