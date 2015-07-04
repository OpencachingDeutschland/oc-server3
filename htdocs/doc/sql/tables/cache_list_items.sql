SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_list_items`;
CREATE TABLE `cache_list_items` (
  `cache_list_id` int(10) NOT NULL,
  `cache_id` int(10) NOT NULL,
  UNIQUE KEY `cache_list_id` (`cache_list_id`,`cache_id`),
  KEY `cache_id` (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
