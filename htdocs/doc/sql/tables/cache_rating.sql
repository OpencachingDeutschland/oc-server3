SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_rating`;
CREATE TABLE `cache_rating` (
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cache_id`,`user_id`),
  KEY `user_id` (`user_id`,`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
