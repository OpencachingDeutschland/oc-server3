SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_rating`;
CREATE TABLE `cache_rating` (
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating_date` datetime NOT NULL,
  PRIMARY KEY (`cache_id`,`user_id`),
  KEY `user_id` (`user_id`,`cache_id`),
  KEY `date` (`rating_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
