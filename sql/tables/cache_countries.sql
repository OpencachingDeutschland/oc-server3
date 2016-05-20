SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_countries`;
CREATE TABLE `cache_countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `country` char(2) NOT NULL DEFAULT '',
  `restored_by` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cache_id` (`cache_id`,`date_created`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='via Trigger (caches)' ;
