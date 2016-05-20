SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_waiting`;
CREATE TABLE `watches_waiting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'via trigger (watches_waiting)',
  `watchtext` longtext NOT NULL,
  `watchtype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
