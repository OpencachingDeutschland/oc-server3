SET NAMES 'utf8';
DROP TABLE IF EXISTS `logentries_types`;
CREATE TABLE `logentries_types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL,
  `eventname` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
