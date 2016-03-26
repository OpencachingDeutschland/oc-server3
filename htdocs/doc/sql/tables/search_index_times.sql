SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_index_times`;
CREATE TABLE `search_index_times` (
  `object_type` tinyint(3) unsigned NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `last_refresh` datetime NOT NULL,
  PRIMARY KEY  (`object_type`,`object_id`),
  KEY `last_refresh` (`last_refresh`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via cronjob' ;
