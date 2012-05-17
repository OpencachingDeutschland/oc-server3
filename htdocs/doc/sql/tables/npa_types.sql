SET NAMES 'utf8';
DROP TABLE IF EXISTS `npa_types`;
CREATE TABLE `npa_types` (
  `id` char(3) NOT NULL,
  `name` varchar(60) NOT NULL,
  `ordinal` tinyint(4) NOT NULL,
  `no_warning` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ordinal` (`ordinal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
