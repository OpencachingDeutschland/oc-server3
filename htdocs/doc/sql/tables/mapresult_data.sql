SET NAMES 'utf8';
DROP TABLE IF EXISTS `mapresult_data`;
CREATE TABLE `mapresult_data` (
  `query_id` int(10) unsigned NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`query_id`,`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
