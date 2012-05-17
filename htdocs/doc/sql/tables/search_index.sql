SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_index`;
CREATE TABLE `search_index` (
  `object_type` tinyint(3) unsigned NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `hash` int(10) unsigned NOT NULL,
  `count` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_type`,`cache_id`,`hash`),
  KEY `object_type` (`object_type`,`hash`,`cache_id`,`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via cronjob' ;
