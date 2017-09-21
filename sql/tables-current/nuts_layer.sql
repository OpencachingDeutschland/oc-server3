SET NAMES 'utf8';
DROP TABLE IF EXISTS `nuts_layer`;
CREATE TABLE `nuts_layer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` tinyint(1) NOT NULL,
  `code` varchar(5) NOT NULL,
  `shape` geometry NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `level` (`level`),
  SPATIAL KEY `shape` (`shape`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
