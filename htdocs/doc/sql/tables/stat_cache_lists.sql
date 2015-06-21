SET NAMES 'utf8';
DROP TABLE IF EXISTS `stat_cache_lists`;
CREATE TABLE `stat_cache_lists` (
  `cache_list_id` int(10) NOT NULL,
  `entries` int(6) NOT NULL default '0' COMMENT 'via trigger in cache_list_items',
  `watchers` int(6) NOT NULL default '0' COMMENT 'via trigger in cache_list_watches',
  PRIMARY KEY (`cache_list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
