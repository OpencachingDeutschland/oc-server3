SET NAMES 'utf8';
DROP TABLE IF EXISTS `nuts_layer`;
CREATE TABLE `nuts_layer` (
  `id` int(11) NOT NULL auto_increment,
  `level` tinyint(1) NOT NULL,
  `code` varchar(5) NOT NULL,
  `shape` linestring NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `level` (`level`),
  SPATIAL KEY `shape` (`shape`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
