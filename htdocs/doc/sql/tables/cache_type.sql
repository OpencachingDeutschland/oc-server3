SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_type`;
CREATE TABLE `cache_type` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) NOT NULL,
  `ordinal` tinyint(3) unsigned NOT NULL,
  `short` varchar(10) NOT NULL COMMENT 'obsolete',
  `de` varchar(60) NOT NULL COMMENT 'obsolete',
  `en` varchar(60) NOT NULL COMMENT 'obsolete',
  `icon_large` varchar(60) NOT NULL COMMENT 'obsolete',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
