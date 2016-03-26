SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_repl_timestamp`;
CREATE TABLE `sys_repl_timestamp` (
  `id` tinyint(1) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
