SET NAMES 'utf8';
DROP TABLE IF EXISTS `rating_tops`;
CREATE TABLE `rating_tops` (
  `cache_id` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cache_id`),
  KEY `rating` (`rating`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
