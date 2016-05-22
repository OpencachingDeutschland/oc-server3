SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_logs_restored`;
CREATE TABLE `cache_logs_restored` (
  `id` int(10) NOT NULL,
  `date_modified` datetime NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `original_id` int(10) unsigned NOT NULL,
  `restored_by` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `original_id` (`original_id`),
  KEY `cache_id` (`cache_id`,`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
