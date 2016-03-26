SET NAMES 'utf8';
DROP TABLE IF EXISTS `countries_options`;
CREATE TABLE `countries_options` (
  `country` char(2) NOT NULL,
  `display` tinyint(1) unsigned NOT NULL,
  `gmLat` double NOT NULL,
  `gmLon` double NOT NULL,
  `gmZoom` tinyint(3) unsigned NOT NULL,
  `nodeId` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`country`),
  KEY `ordinal` (`display`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
