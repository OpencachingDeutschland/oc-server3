SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_stats_hourly`;
CREATE TABLE `okapi_stats_hourly` (
  `consumer_key` varchar(32) NOT NULL,
  `user_id` int(10) NOT NULL,
  `period_start` datetime NOT NULL,
  `service_name` varchar(80) NOT NULL,
  `total_calls` int(10) NOT NULL,
  `http_calls` int(10) NOT NULL,
  `total_runtime` float NOT NULL DEFAULT '0',
  `http_runtime` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`consumer_key`,`user_id`,`period_start`,`service_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
