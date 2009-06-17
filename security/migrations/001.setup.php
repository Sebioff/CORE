<?php

$groupsContainer = $self->getContainerGroupsTableName();
$groupsUsersAssocContainer = $self->getContainerGroupsUsersAssocTableName();
$usersContainer = $self->getContainerUsersTableName();
$privilegesContainer = $self->getContainerPrivilegesTableName();

// TODO: add users table
// TODO: add check if tables exist

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$groupsContainer.'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `group_identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$groupsUsersAssocContainer.'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned DEFAULT NULL,
  `user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `user_group` (`user_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'ALTER TABLE `'.$groupsUsersAssocContainer.'`
  ADD CONSTRAINT `'.$groupsUsersAssocContainer.'_ibfk_1` FOREIGN KEY (`user`) REFERENCES `'.$usersContainer.'` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `'.$groupsUsersAssocContainer.'_ibfk_2` FOREIGN KEY (`user_group`) REFERENCES `'.$groupsContainer.'` (`id`) ON DELETE SET NULL;';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$privilegesContainer.'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `privilege` varchar(255) NOT NULL,
  `value` tinyint(1) unsigned NOT NULL,
  `user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_group` (`user_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'ALTER TABLE `'.$privilegesContainer.'`
  ADD CONSTRAINT `'.$privilegesContainer.'_ibfk_1` FOREIGN KEY (`user_group`) REFERENCES `'.$groupsContainer.'` (`id`) ON DELETE SET NULL;';

?>