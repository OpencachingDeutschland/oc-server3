SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_cache`;
CREATE TABLE `okapi_cache` (
  `key` varchar(64) NOT NULL,
  `value` mediumblob,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
