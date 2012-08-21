SET NAMES 'utf8';
DROP TABLE IF EXISTS `okapi_stats_temp`;
CREATE TABLE `okapi_stats_temp` (
  `datetime` datetime NOT NULL,
  `consumer_key` varchar(32) NOT NULL DEFAULT 'internal',
  `user_id` int(10) NOT NULL DEFAULT '-1',
  `service_name` varchar(80) NOT NULL,
  `calltype` enum('internal','http') NOT NULL,
  `runtime` float NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8 ;
