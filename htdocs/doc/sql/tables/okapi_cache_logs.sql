SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_cache_logs`;
CREATE TABLE `okapi_cache_logs` (
  `log_id` int(11) NOT NULL,
  `consumer_key` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `by_consumer` (`consumer_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
