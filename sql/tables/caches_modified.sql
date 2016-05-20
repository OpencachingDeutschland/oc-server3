SET NAMES 'utf8';
DROP TABLE IF EXISTS `caches_modified`;
CREATE TABLE `caches_modified` (
  `cache_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_modified` date NOT NULL COMMENT 'no time! see restorecaches.php',
  `name` varchar(255) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `date_hidden` date NOT NULL,
  `size` tinyint(3) unsigned NOT NULL,
  `difficulty` tinyint(3) unsigned NOT NULL,
  `terrain` tinyint(3) unsigned NOT NULL,
  `search_time` float unsigned NOT NULL DEFAULT '0',
  `way_length` float unsigned NOT NULL DEFAULT '0',
  `wp_gc` varchar(7) NOT NULL,
  `wp_nc` varchar(6) NOT NULL,
  `restored_by` int(10) NOT NULL,
  UNIQUE KEY `cache_id` (`cache_id`,`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
