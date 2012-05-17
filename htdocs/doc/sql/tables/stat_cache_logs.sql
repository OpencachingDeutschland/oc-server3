SET NAMES 'utf8';
DROP TABLE IF EXISTS `stat_cache_logs`;
CREATE TABLE `stat_cache_logs` (
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `found` smallint(5) unsigned NOT NULL,
  `notfound` smallint(5) unsigned NOT NULL,
  `note` smallint(5) unsigned NOT NULL,
  `will_attend` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`cache_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via trigger (cache_logs)' ;
