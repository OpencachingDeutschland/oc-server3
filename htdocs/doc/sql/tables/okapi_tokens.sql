SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_tokens`;
CREATE TABLE `okapi_tokens` (
  `key` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `secret` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `token_type` enum('request','access') NOT NULL,
  `timestamp` int(10) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `consumer_key` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `verifier` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `callback` varchar(2083) DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `by_consumer` (`consumer_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
