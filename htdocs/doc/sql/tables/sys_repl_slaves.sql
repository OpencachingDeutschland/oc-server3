SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_repl_slaves`;
CREATE TABLE `sys_repl_slaves` (
  `id` smallint(5) unsigned NOT NULL,
  `server` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `weight` tinyint(4) NOT NULL,
  `online` tinyint(1) NOT NULL,
  `last_check` datetime NOT NULL,
  `time_diff` int(10) unsigned NOT NULL,
  `current_log_name` varchar(60) NOT NULL,
  `current_log_pos` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
