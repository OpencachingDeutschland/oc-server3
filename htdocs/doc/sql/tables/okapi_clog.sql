SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_clog`;
CREATE TABLE `okapi_clog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `data` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
