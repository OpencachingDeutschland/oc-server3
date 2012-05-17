SET NAMES 'utf8';
DROP TABLE IF EXISTS `ws_sessions`;
CREATE TABLE `ws_sessions` (
  `id` varchar(36) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL COMMENT 'via cronjob',
  `last_usage` datetime NOT NULL,
  `valid` tinyint(1) NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
