SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_waypoint_pool`;
CREATE TABLE `cache_waypoint_pool` (
  `wp_oc` char(7) NOT NULL,
  `uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`wp_oc`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
