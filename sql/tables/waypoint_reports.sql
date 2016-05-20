SET NAMES 'utf8';
DROP TABLE IF EXISTS `waypoint_reports`;
CREATE TABLE `waypoint_reports` (
  `report_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_reported` datetime NOT NULL,
  `wp_oc` varchar(7) NOT NULL,
  `wp_external` varchar(8) NOT NULL,
  `source` varchar(64) NOT NULL,
  `gcwp_processed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `gcwp_processed` (`gcwp_processed`,`date_reported`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
