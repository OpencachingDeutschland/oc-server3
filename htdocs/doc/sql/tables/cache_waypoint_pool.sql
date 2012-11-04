SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_waypoint_pool`;
CREATE TABLE `cache_waypoint_pool` (
  `wp_oc` char(7) NOT NULL,
  `cache_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`wp_oc`),
  KEY `cache_id` (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
