SET NAMES 'utf8';
DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE `sysconfig` (
  `name` varchar(60) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='obsolete' ;
