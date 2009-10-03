<?php

/**
 * Provides functions for security protection.
 * Users are associated with groups.
 * Privileges can be assigned to groups.
 * In order to use automatic database table creation you need to register your
 * security object using Security::register() in your modules.php
 */
abstract class Security {
	private static $stack = array();
	
	private $privileges = array();
	private $containerUsers = null;
	private $containerGroups = null;
	private $containerGroupsUsersAssoc = null;
	private $containerPrivileges = null;
	
	protected function __construct() {
		// Singleton
	}
	
	/**
	 * Sets the privilege identified by $privilegeIdentifier for the given group
	 */
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
	
	/**
	 * Removes the privilege identified by $privilegeIdentifier from the given
	 * group
	 */
	public function removePrivilege($privilegeIdentifier, DB_Record $userGroup) {
		$options = array();
		$options['conditions'][] = array('user_group = ?', $userGroup);
		$options['conditions'][] = array('privilege = ?', $privilegeIdentifier);
		$this->getContainerPrivileges()->delete($options);
	}
	
	/**
	 * Adds the given user to the given group
	 */
	public function addToGroup(DB_Record $user, DB_Record $userGroup) {
		$usersGroupAssoc = new DB_Record();
		$usersGroupAssoc->user = $user;
		$usersGroupAssoc->userGroup = $userGroup;
		$this->getContainerGroupsUsersAssoc()->save($usersGroupAssoc);
	}
	
	/**
	 * Removes the given user from the given group
	 */
	public function removeFromGroup(DB_Record $user, DB_Record $userGroup) {
		$options = array();
		$options['conditions'][] = array('user = ?', $user);
		$options['conditions'][] = array('user_group = ?', $userGroup);
		$this->getContainerGroupsUsersAssoc()->delete($options);
	}
	
	/**
	 * Removes the given user from all groups he belongs to
	 */
	public function removeFromAllGroups(DB_Record $user) {
		$options = array();
		$options['conditions'][] = array('user = ?', $user);
		$this->getContainerGroupsUsersAssoc()->delete($options);
	}
	
	/**
	 * @return DB_Record group identified by $groupIdentifier
	 */
	public function getGroup($groupIdentifier) {
		return $this->getContainerGroups()->selectByGroupIdentifierFirst($groupIdentifier);
	}
	
	/**
	 * @return array containing all available groups
	 */
	public function getGroups() {
		return $this->getContainerGroups()->select();
	}
	
	/**
	 * @return array of users that belong to the group identified by groupIdentifier
	 */
	public function getGroupUsers($groupIdentifier) {
		$users = $this->getContainerUsersTableName();
		$groupsUsersAssoc = $this->getContainerGroupsUsersAssocTableName();
		$groups = $this->getContainerGroupsTableName();
		$options = array();
		$options['join'] = array($groupsUsersAssoc, $groups);
		$options['conditions'][] = array($groups.'.id = '.$groupsUsersAssoc.'.user_group');
		$options['conditions'][] = array($groupsUsersAssoc.'.user = '.$users.'.id');
		$options['conditions'][] = array($groups.'.group_identifier = ?', $groupIdentifier);
		return $this->getContainerUsers()->select($options);
	}
	
	/**
	 * @return array of groups the given user belongs to
	 */
	public function getUserGroups(DB_Record $user) {
		$groupsUsersAssoc = $this->getContainerGroupsUsersAssocTableName();
		$groups = $this->getContainerGroupsTableName();
		$options = array();
		$options['join'] = array($groupsUsersAssoc);
		$options['conditions'][] = array($groups.'.id = '.$groupsUsersAssoc.'.user_group');
		$options['conditions'][] = array($groupsUsersAssoc.'.user = ?', $user);
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
	
	/**
	 * @return true if the given user has the privilege identified by $privilegeIdentifier,
	 * false otherwise
	 */
	public function hasPrivilege(DB_Record $user = null, $privilegeIdentifier) {
		if (!$user)
			return false;
			
		$groupsUsersAssoc = $this->getContainerGroupsUsersAssocTableName();
		$groups = $this->getContainerGroupsTableName();
		$privileges = $this->getContainerPrivilegesTableName();
		$options = array();
		$options['join'] = array($groups, $groupsUsersAssoc);
		$options['conditions'][] = array($privileges.'.privilege = ?', $privilegeIdentifier);
		$options['conditions'][] = array($privileges.'.user_group = '.$groups.'.id');
		$options['conditions'][] = array($groups.'.id = '.$groupsUsersAssoc.'.user_group');
		$options['conditions'][] = array($groupsUsersAssoc.'.user = ?', $user);
		$privilege = $this->getContainerPrivileges()->selectFirst($options);
		
		if (!$privilege)
			return $this->getDefaultValue($privilegeIdentifier, $user);
		else if ($privilege->value == true)
			return true;
		else
			return false;
	}
	
	/**
	 * @return true if the given group has the privilege identified by $privilegeIdentifier,
	 * false otherwise
	 */
	public function groupHasPrivilege(DB_Record $group, $privilegeIdentifier) {
		$options = array();
		$options['conditions'][] = array('privilege = ?', $privilegeIdentifier);
		$options['conditions'][] = array('user_group = ?', $group);
		$privilege = $this->getContainerPrivileges()->selectFirst($options);
		
		if ($privilege && $privilege->value == true)
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
		Core_MigrationsLoader::executeMigration(dirname(__FILE__).'/migrations/001.setup.php', array('self' => $this));
	}
	
	/**
	 * @param $tablePrefix to be used for database tables
	 * @param $securityImpl your implementation of Security
	 */
	public static function register(Security $securityImpl) {
		CoreRoutes_Reset::addCallbackOnAfterReset(array($securityImpl, 'onSetup'));
	}
	
	// TODO use late static binding PHP 5.3, remove $class parameter (use current class)
	public static function get($class = '', $name = '') {
		$stackName = $class.$name;
		if (!isset(self::$stack[$stackName]) || empty(self::$stack[$stackName]))
			self::push(new $class(), $name, $class); // TODO remove $class parameter with php 5.3, use late static binding
		
		return end(self::$stack[$stackName]);
	}
	
	// TODO use late static binding PHP 5.3, remove $class parameter (use current class)
	public static function push(Security $context, $name = '', $class = '') {
		$stackName = $class.$name;
		
		if (!isset(self::$stack[$stackName]))
			self::$stack[$stackName] = array();

		array_push(self::$stack[$stackName], $context);
	}
	
	// TODO use late static binding PHP 5.3, remove $class parameter (use current class)
	public static function pop($name = '', $class = '') {
		$stackName = $class.$name;
		
		return array_pop(self::$stack[$stackName]);
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
	public function getContainerUsersTableName() {
		return $this->getTablePrefix().'_users';
	}
	
	/**
	 * @return String name of the groups table
	 */
	public function getContainerGroupsTableName() {
		return $this->getTablePrefix().'_groups';
	}
	
	/**
	 * @return String name of the groups/users-assoc table
	 */
	public function getContainerGroupsUsersAssocTableName() {
		return $this->getTablePrefix().'_groups_users_assoc';
	}
	
	/**
	 * @return String name of the privileges table
	 */
	public function getContainerPrivilegesTableName() {
		return $this->getTablePrefix().'_privileges';
	}
	
	/**
	 * @return String prefix to be used for needed database tables
	 */
	protected abstract function getTablePrefix();
}

?>