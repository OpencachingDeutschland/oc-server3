SET NAMES 'utf8';
DROP TABLE IF EXISTS `caches_attributes_modified`;
CREATE TABLE `caches_attributes_modified` (
  `cache_id` int(10) unsigned NOT NULL,
  `attrib_id` tinyint(3) unsigned NOT NULL,
  `date_modified` date NOT NULL COMMENT 'no time! see restorecaches.php',
  `was_set` tinyint(1) unsigned NOT NULL,
  `restored_by` int(10) NOT NULL,
  UNIQUE KEY `cache_id` (`cache_id`,`date_modified`,`attrib_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
