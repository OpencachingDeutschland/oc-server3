SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_consumers`;
CREATE TABLE `okapi_consumers` (
  `key` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  `secret` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `url` varchar(250) DEFAULT NULL,
  `email` varchar(70) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
