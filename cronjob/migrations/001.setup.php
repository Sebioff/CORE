<?php

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$databaseTableName.'` (
  `identifier` varchar(25) NOT NULL,
  `last_execution` int(10) unsigned NOT NULL,
  `last_execution_duration` float unsigned NOT NULL,
  `last_execution_successful` tinyint(1) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;';

?>