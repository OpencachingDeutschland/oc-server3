SET NAMES 'utf8';
DROP TABLE IF EXISTS `stat_caches`;
CREATE TABLE `stat_caches` (
  `cache_id` int(10) unsigned NOT NULL,
  `found` smallint(5) unsigned NOT NULL,
  `notfound` smallint(5) unsigned NOT NULL,
  `note` smallint(5) unsigned NOT NULL,
  `will_attend` smallint(5) unsigned NOT NULL,
  `last_found` date default NULL,
  `watch` smallint(5) unsigned NOT NULL,
  `ignore` smallint(5) unsigned NOT NULL,
  `toprating` smallint(5) unsigned NOT NULL,
  `picture` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via trigger (caches)' ;
