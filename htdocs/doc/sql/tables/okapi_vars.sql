SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_vars`;
CREATE TABLE `okapi_vars` (
  `var` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` text,
  PRIMARY KEY (`var`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
