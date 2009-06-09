<?php

$groupsContainer = $this->defineContainerGroups();

$queries[] = 'ALTER TABLE `'.$groupsContainer[1].'`
  ADD `alliance` smallint(5) unsigned DEFAULT NULL,
  ADD KEY `alliance` (`alliance`),
  ADD CONSTRAINT `'.$groupsContainer[1].'_ibfk_1` FOREIGN KEY (`alliance`) REFERENCES `alliances` (`id`) ON DELETE SET NULL;';

?>