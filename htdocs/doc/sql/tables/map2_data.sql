SET NAMES 'utf8';
DROP TABLE IF EXISTS `map2_data`;
CREATE TABLE `map2_data` (
  `result_id` int(10) unsigned NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`result_id`,`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
