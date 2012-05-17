SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_ignore`;
CREATE TABLE `cache_ignore` (
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cache_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
