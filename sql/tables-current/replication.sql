SET NAMES 'utf8';
DROP TABLE IF EXISTS `replication`;
CREATE TABLE `replication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `use` int(1) NOT NULL DEFAULT '0',
  `prio` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='obsolete' ;
