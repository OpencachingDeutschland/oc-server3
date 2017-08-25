SET NAMES 'utf8';
DROP TABLE IF EXISTS `stat_cache_countries`;
CREATE TABLE `stat_cache_countries` (
  `country` char(2) NOT NULL DEFAULT '',
  `active_caches` int(10) NOT NULL,
  PRIMARY KEY (`country`),
  KEY `active_caches` (``date_created``)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='via Trigger (caches)' ;
