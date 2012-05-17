SET NAMES 'utf8';
DROP TABLE IF EXISTS `nuts_codes`;
CREATE TABLE `nuts_codes` (
  `code` varchar(10) NOT NULL,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY  (`code`),
  KEY `code` (`code`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
