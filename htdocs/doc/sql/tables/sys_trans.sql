SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_trans`;
CREATE TABLE `sys_trans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `text` mediumtext NOT NULL,
  `last_modified` datetime NOT NULL COMMENT 'via trigger (sys_trans)',
  PRIMARY KEY  (`id`),
  KEY `text` (`text`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
