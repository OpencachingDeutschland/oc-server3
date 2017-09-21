SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_status_modified`;
CREATE TABLE `cache_status_modified` (
  `cache_id` int(10) unsigned NOT NULL,
  `date_modified` datetime NOT NULL,
  `old_state` tinyint(2) unsigned NOT NULL,
  `new_state` tinyint(2) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `cache_id` (`cache_id`,`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
