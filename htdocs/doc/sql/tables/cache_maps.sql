SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_maps`;
CREATE TABLE `cache_maps` (
  `cache_id` int(10) unsigned NOT NULL default '0',
  `last_refresh` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`cache_id`),
  KEY `last_refresh` (`last_refresh`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via cronjob' ;
