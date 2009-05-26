<?php

$groupsContainer = $this->defineContainerGroups();
$groupsUsersAssocContainer = $this->defineContainerGroupsUsersAssoc();
$usersContainer = $this->defineContainerUsers();
$privilegesContainer = $this->defineContainerPrivileges();

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$groupsContainer[1].'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `group_identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$groupsUsersAssocContainer[1].'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned DEFAULT NULL,
  `user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `user_group` (`user_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'ALTER TABLE `'.$groupsUsersAssocContainer[1].'`
  ADD CONSTRAINT `'.$groupsUsersAssocContainer[1].'_ibfk_1` FOREIGN KEY (`user`) REFERENCES `'.$usersContainer[1].'` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `'.$groupsUsersAssocContainer[1].'_ibfk_2` FOREIGN KEY (`user_group`) REFERENCES `'.$groupsContainer[1].'` (`id`) ON DELETE SET NULL;';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$privilegesContainer[1].'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `privilege` varchar(255) NOT NULL,
  `value` tinyint(1) unsigned NOT NULL,
  `user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_group` (`user_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'ALTER TABLE `'.$privilegesContainer[1].'`
  ADD CONSTRAINT `'.$privilegesContainer[1].'_ibfk_1` FOREIGN KEY (`user_group`) REFERENCES `'.$groupsContainer[1].'` (`id`) ON DELETE SET NULL;';

?>