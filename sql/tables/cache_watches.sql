SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_watches`;
CREATE TABLE `cache_watches` (
  `cache_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cache_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
