SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_adoption`;
CREATE TABLE `cache_adoption` (
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL COMMENT 'via Trigger',
  UNIQUE KEY `id` (`cache_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
