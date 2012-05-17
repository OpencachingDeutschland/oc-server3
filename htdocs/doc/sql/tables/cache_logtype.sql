SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_logtype`;
CREATE TABLE `cache_logtype` (
  `cache_type_id` tinyint(3) unsigned NOT NULL,
  `log_type_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`cache_type_id`,`log_type_id`),
  KEY `log_type_id` (`log_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
