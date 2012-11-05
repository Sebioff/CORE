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

$groupsContainer = $self->getContainerGroupsTableName();
$groupsUsersAssocContainer = $self->getContainerGroupsUsersAssocTableName();
$usersContainer = $self->getContainerUsersTableName();
$privilegesContainer = $self->getContainerPrivilegesTableName();

// TODO: add users table
// TODO: add check if tables exist

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$groupsContainer.'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
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
  ADD CONSTRAINT `'.$groupsUsersAssocContainer.'_ibfk_1` FOREIGN KEY (`user`) REFERENCES `'.$usersContainer.'` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `'.$groupsUsersAssocContainer.'_ibfk_2` FOREIGN KEY (`user_group`) REFERENCES `'.$groupsContainer.'` (`id`) ON DELETE CASCADE;';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$privilegesContainer.'` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `privilege` varchar(255) NOT NULL,
  `value` tinyint(1) unsigned NOT NULL,
  `user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_group` (`user_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;';

$queries[] = 'ALTER TABLE `'.$privilegesContainer.'`
  ADD CONSTRAINT `'.$privilegesContainer.'_ibfk_1` FOREIGN KEY (`user_group`) REFERENCES `'.$groupsContainer.'` (`id`) ON DELETE CASCADE;';

?>