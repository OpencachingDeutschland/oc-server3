SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_repl_exclude`;
CREATE TABLE `sys_repl_exclude` (
  `user_id` int(10) unsigned NOT NULL,
  `datExclude` datetime NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `datExclude` (`datExclude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
