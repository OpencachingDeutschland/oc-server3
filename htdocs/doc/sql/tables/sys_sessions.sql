SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_sessions`;
CREATE TABLE `sys_sessions` (
  `uuid` varchar(36) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `permanent` tinyint(1) NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY  (`uuid`),
  KEY `last_login` (`last_login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
