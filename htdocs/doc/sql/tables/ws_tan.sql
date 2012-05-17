SET NAMES 'utf8';
DROP TABLE IF EXISTS `ws_tan`;
CREATE TABLE `ws_tan` (
  `session` varchar(36) NOT NULL,
  `tan` varchar(36) NOT NULL,
  PRIMARY KEY  (`session`,`tan`),
  UNIQUE KEY `tan` (`tan`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
