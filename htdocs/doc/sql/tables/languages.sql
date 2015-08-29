SET NAMES 'utf8';
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `short` char(2) NOT NULL,
  `name` varchar(60) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `de` varchar(60) NOT NULL COMMENT 'obsolete',
  `en` varchar(60) NOT NULL COMMENT 'obsolete',
  `list_default_de` tinyint(1) NOT NULL default '0' COMMENT 'obsolete',
  `list_default_en` tinyint(1) NOT NULL default '0' COMMENT 'obsolete',
  PRIMARY KEY  (`short`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
