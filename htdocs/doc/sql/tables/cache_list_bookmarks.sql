SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_list_bookmarks`;
CREATE TABLE `cache_list_bookmarks` (
  `cache_list_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `password` varchar(80) NOT NULL,
  UNIQUE KEY `cache_list_id` (`cache_list_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
