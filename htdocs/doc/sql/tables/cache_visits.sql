SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_visits`;
CREATE TABLE `cache_visits` (
  `cache_id` int(10) unsigned NOT NULL default '0',
  `user_id_ip` varchar(15) NOT NULL default '0',
  `count` smallint(5) unsigned NOT NULL default '0',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'via trigger (cache_visits)',
  PRIMARY KEY  (`cache_id`,`user_id_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
