SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_nonces`;
CREATE TABLE `okapi_nonces` (
  `consumer_key` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`consumer_key`,`key`,`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 ;
