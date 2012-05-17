SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_coordinates`;
CREATE TABLE `cache_coordinates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`,`date_created`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via Trigger (caches)' ;
