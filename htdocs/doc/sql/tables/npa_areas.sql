SET NAMES 'utf8';
DROP TABLE IF EXISTS `npa_areas`;
CREATE TABLE `npa_areas` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type_id` char(3) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `shape` linestring NOT NULL,
  PRIMARY KEY  (`id`),
  SPATIAL KEY `shape` (`shape`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
