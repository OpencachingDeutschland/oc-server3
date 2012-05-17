SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_watches`;
CREATE TABLE `cache_watches` (
  `cache_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `last_executed` datetime default NULL COMMENT 'via cronjob',
  PRIMARY KEY  (`cache_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
